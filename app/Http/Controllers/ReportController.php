<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // ── Revenue per month (last 12 months) ─────────────────────────────
        $revenueMonthly = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as period, SUM(amount) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period')
            ->toArray();

        // Fill missing months
        $revenueChart = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $revenueChart[$key] = $revenueMonthly[$key] ?? 0;
        }

        // ── Summary for selected month ──────────────────────────────────────
        $periodStart = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $periodDate  = \Carbon\Carbon::parse($periodStart);

        $invoicesThisMonth = Invoice::whereYear('billing_period', $year)
            ->whereMonth('billing_period', $month)
            ->get();

        $totalRevenue   = $invoicesThisMonth->where('status', 'paid')->sum('amount');
        $totalUnpaid    = $invoicesThisMonth->where('status', 'unpaid')->sum('amount');
        $totalOverdue   = $invoicesThisMonth->where('status', 'overdue')->sum('amount');
        $totalCancelled = $invoicesThisMonth->where('status', 'cancelled')->sum('amount');
        $invoiceCount   = $invoicesThisMonth->count();
        $paidCount      = $invoicesThisMonth->where('status', 'paid')->count();
        $unpaidCount    = $invoicesThisMonth->where('status', 'unpaid')->count();
        $overdueCount   = $invoicesThisMonth->where('status', 'overdue')->count();

        // ── Customers ───────────────────────────────────────────────────────
        $totalCustomers  = Customer::count();
        $activeCustomers = Customer::where('status', 'aktif')->count();
        $suspendCustomers = Customer::where('status', 'suspend')->count();
        $terminateCustomers = Customer::where('status', 'terminate')->count();

        // Customers per package
        $custByPackage = Customer::where('status', 'aktif')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->selectRaw('packages.name as pkg_name, packages.price, COUNT(*) as total')
            ->groupBy('packages.id', 'packages.name', 'packages.price')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        // New customers this month
        $newCustomers = Customer::whereYear('join_date', $year)
            ->whereMonth('join_date', $month)
            ->count();

        // Customer growth last 12 months
        $custGrowth = Customer::where('join_date', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(join_date, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period')
            ->toArray();

        $custGrowthChart = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $custGrowthChart[$key] = $custGrowth[$key] ?? 0;
        }

        // ── Network ─────────────────────────────────────────────────────────
        $totalRouters  = Router::count();
        $onlineRouters = Router::where('status', 'online')->count();
        $totalPppoe    = Router::sum('pppoe_online');
        $mappedCust    = Customer::whereNotNull('pppoe_user')->where('pppoe_user', '!=', '')->count();

        // ── Payment methods breakdown ───────────────────────────────────────
        $paymentMethods = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->selectRaw("COALESCE(payment_method, 'Lainnya') as method, COUNT(*) as total, SUM(amount) as amount")
            ->groupBy('method')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        // ── Top 5 overdue customers ─────────────────────────────────────────
        $topOverdue = Invoice::with('customer:id,name,phone')
            ->where('status', 'overdue')
            ->orderByDesc('amount')
            ->limit(5)
            ->get()
            ->map(fn($inv) => [
                'customer_name' => $inv->customer?->name ?? '—',
                'phone'         => $inv->customer?->phone ?? '',
                'amount'        => $inv->amount,
                'due_date'      => $inv->due_date?->format('d M Y'),
                'days_overdue'  => $inv->due_date ? now()->diffInDays($inv->due_date) : 0,
            ])
            ->toArray();

        return view('reports.index', compact(
            'year', 'month', 'periodDate',
            'revenueChart',
            'totalRevenue', 'totalUnpaid', 'totalOverdue', 'totalCancelled',
            'invoiceCount', 'paidCount', 'unpaidCount', 'overdueCount',
            'totalCustomers', 'activeCustomers', 'suspendCustomers', 'terminateCustomers',
            'custByPackage', 'newCustomers', 'custGrowthChart',
            'totalRouters', 'onlineRouters', 'totalPppoe', 'mappedCust',
            'paymentMethods', 'topOverdue'
        ));
    }
}
