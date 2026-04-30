<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Router;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Customers ───────────────────────────────────────────────────────
        $totalCust   = Customer::count();
        $activeCust  = Customer::where('status', 'aktif')->count();
        $suspendCust = Customer::where('status', 'suspend')->count();
        $termCust    = Customer::where('status', 'terminate')->count();
        $newThisMonth = Customer::whereYear('join_date', now()->year)
            ->whereMonth('join_date', now()->month)->count();
        $newLastMonth = Customer::whereYear('join_date', now()->subMonth()->year)
            ->whereMonth('join_date', now()->subMonth()->month)->count();
        $custGrowthPct = $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1) : ($newThisMonth > 0 ? 100 : 0);

        // ── Revenue ─────────────────────────────────────────────────────────
        $revenueThisMonth = Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');
        $revenueLastMonth = Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->subMonth()->year)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->sum('amount');
        $revGrowthPct = $revenueLastMonth > 0 ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) : 0;

        // Revenue 6 months for chart
        $revChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $total = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $d->year)
                ->whereMonth('paid_at', $d->month)
                ->sum('amount');
            $revChart[] = [
                'month' => $d->translatedFormat('M'),
                'value' => $total,
                'label' => 'Rp ' . number_format($total / 1000000, 1, ',', '.') . ' Jt',
            ];
        }
        $revMax = max(1, max(array_column($revChart, 'value')));

        // ── Invoices ────────────────────────────────────────────────────────
        $overdueCount = Invoice::where('status', 'overdue')->count();
        $overdueLastMonth = Invoice::where('status', 'overdue')
            ->whereMonth('due_date', now()->subMonth()->month)->count();
        $unpaidCount  = Invoice::where('status', 'unpaid')->count();
        $paidThisMonth = Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)->count();

        // Upcoming due invoices
        $upcomingDue = Invoice::with('customer:id,name,phone', 'customer.package:id,name')
            ->whereIn('status', ['unpaid', 'overdue'])
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(function ($inv) {
                $diff = now()->startOfDay()->diffInDays($inv->due_date, false);
                if ($diff < 0) $dueLabel = abs($diff) . ' hari terlambat';
                elseif ($diff === 0) $dueLabel = 'Hari ini';
                elseif ($diff === 1) $dueLabel = 'Besok';
                else $dueLabel = $diff . ' hari lagi';
                return [
                    'name'    => $inv->customer?->name ?? '—',
                    'package' => $inv->customer?->package?->name ?? '—',
                    'amount'  => $inv->amount,
                    'due'     => $dueLabel,
                    'days'    => $diff,
                    'status'  => $inv->status,
                ];
            });

        // ── Network ─────────────────────────────────────────────────────────
        $totalRouters  = Router::count();
        $onlineRouters = Router::where('status', 'online')->count();
        $totalPppoe    = Router::sum('pppoe_online');
        $mappedCust    = Customer::whereNotNull('pppoe_user')->where('pppoe_user', '!=', '')->count();

        // ── Package distribution ────────────────────────────────────────────
        $pkgDist = Customer::where('status', 'aktif')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->selectRaw('packages.name as pkg_name, COUNT(*) as total')
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->toArray();

        $pkgColors = ['bg-blue-500','bg-green-500','bg-purple-500','bg-amber-500','bg-red-400','bg-cyan-500'];

        // ── Latest customers ────────────────────────────────────────────────
        $latestCustomers = Customer::with('package:id,name,category')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Invoice status donut ────────────────────────────────────────────
        $invThisMonth = Invoice::whereYear('billing_period', now()->year)
            ->whereMonth('billing_period', now()->month);
        $invTotal  = (clone $invThisMonth)->count();
        $invPaid   = (clone $invThisMonth)->where('status', 'paid')->count();
        $invUnpaid = (clone $invThisMonth)->where('status', 'unpaid')->count();
        $invOverdue= (clone $invThisMonth)->where('status', 'overdue')->count();

        return view('dashboard', compact(
            'totalCust', 'activeCust', 'suspendCust', 'termCust',
            'newThisMonth', 'custGrowthPct',
            'revenueThisMonth', 'revGrowthPct', 'revChart', 'revMax',
            'overdueCount', 'overdueLastMonth', 'unpaidCount', 'paidThisMonth',
            'upcomingDue',
            'totalRouters', 'onlineRouters', 'totalPppoe', 'mappedCust',
            'pkgDist', 'pkgColors',
            'latestCustomers',
            'invTotal', 'invPaid', 'invUnpaid', 'invOverdue'
        ));
    }
}
