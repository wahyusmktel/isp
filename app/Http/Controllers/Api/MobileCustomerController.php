<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\News;
use App\Models\Router;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileCustomerController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_number' => ['required', 'string', 'max:50'],
        ]);

        $customer = Customer::with('package')
            ->where('customer_number', $validated['customer_number'])
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pelanggan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'customer' => $this->customerData($customer),
            'invoices' => $this->invoiceQuery($customer)->take(10)->get()->map(fn (Invoice $invoice) => $this->invoiceData($invoice, [
                'customer_number' => $customer->customer_number,
            ]))->values(),
            'summary' => $this->invoiceSummary($customer),
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_number' => ['required', 'string', 'max:50'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $customer = Customer::with('package')
            ->where('customer_number', $validated['customer_number'])
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pelanggan tidak ditemukan.',
            ], 404);
        }

        $limit = (int) ($validated['limit'] ?? 25);

        return response()->json([
            'success' => true,
            'customer' => $this->customerData($customer),
            'summary' => $this->invoiceSummary($customer),
            'invoices' => $this->invoiceQuery($customer)->take($limit)->get()->map(fn (Invoice $invoice) => $this->invoiceData($invoice, [
                'customer_number' => $customer->customer_number,
            ]))->values(),
        ]);
    }

    public function customerInvoicePdf(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_number' => ['required', 'string', 'max:50'],
        ]);

        $customer = Customer::where('customer_number', $validated['customer_number'])->firstOrFail();
        abort_unless((int) $invoice->customer_id === (int) $customer->id, 403);

        $invoice->load('customer.package');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function news(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $limit = (int) ($validated['limit'] ?? 5);

        $news = News::query()
            ->where('status', 'published')
            ->latest('published_at')
            ->take($limit)
            ->get()
            ->map(fn (News $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt ?: Str::limit(strip_tags($item->body), 120),
                'category' => $item->category,
                'category_label' => $item->category_label,
                'published_at' => $item->published_at?->format('Y-m-d H:i:s'),
                'published_at_label' => $item->published_at?->translatedFormat('d F Y') ?? '-',
            ])
            ->values();

        return response()->json([
            'success' => true,
            'news' => $news,
        ]);
    }

    public function staffLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])
            ->whereIn('role', ['admin', 'operator'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password tidak valid untuk admin/operator.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $this->makeStaffToken($user),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function staffDashboard(Request $request): JsonResponse
    {
        if (!$this->staffUser($request)) {
            return $this->unauthorizedStaff();
        }

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'aktif')->count();
        $newThisMonth = Customer::whereYear('join_date', now()->year)
            ->whereMonth('join_date', now()->month)
            ->count();

        $revenueThisMonth = (int) Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');
        $paidThisMonth = Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->count();

        $overdueCount = Invoice::where('status', 'overdue')->count();
        $unpaidCount = Invoice::where('status', 'unpaid')->count();

        $totalRouters = Router::count();
        $onlineRouters = Router::where('status', 'online')->count();
        $totalPppoe = (int) Router::sum('pppoe_online');
        $mappedCustomers = Customer::whereNotNull('pppoe_user')->where('pppoe_user', '!=', '')->count();

        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = (int) Invoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
            $revenueChart[] = [
                'month' => $date->translatedFormat('M'),
                'value' => $total,
                'label' => 'Rp ' . number_format($total / 1000000, 1, ',', '.') . ' Jt',
            ];
        }

        $packageDistribution = Customer::where('status', 'aktif')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->selectRaw('packages.name as name, COUNT(*) as total')
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'total' => (int) $item->total,
                'percentage' => $activeCustomers > 0 ? round(((int) $item->total / $activeCustomers) * 100) : 0,
            ])
            ->values();

        $monthlyInvoices = Invoice::whereYear('billing_period', now()->year)
            ->whereMonth('billing_period', now()->month);
        $invoiceTotal = (clone $monthlyInvoices)->count();
        $invoicePaid = (clone $monthlyInvoices)->where('status', 'paid')->count();
        $invoiceUnpaid = (clone $monthlyInvoices)->where('status', 'unpaid')->count();
        $invoiceOverdue = (clone $monthlyInvoices)->where('status', 'overdue')->count();
        $invoiceCancelled = (clone $monthlyInvoices)->where('status', 'cancelled')->count();

        $upcomingDue = Invoice::with('customer:id,name,phone,package_id', 'customer.package:id,name')
            ->whereIn('status', ['unpaid', 'overdue'])
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(function (Invoice $invoice) {
                $diff = now()->startOfDay()->diffInDays($invoice->due_date, false);
                if ($diff < 0) {
                    $dueLabel = abs($diff) . ' hari terlambat';
                } elseif ($diff === 0) {
                    $dueLabel = 'Hari ini';
                } elseif ($diff === 1) {
                    $dueLabel = 'Besok';
                } else {
                    $dueLabel = $diff . ' hari lagi';
                }

                return [
                    'customer_name' => $invoice->customer?->name ?? '-',
                    'package_name' => $invoice->customer?->package?->name ?? '-',
                    'amount' => (int) $invoice->amount,
                    'due_label' => $dueLabel,
                    'days' => (int) $diff,
                    'status' => $invoice->status,
                ];
            })
            ->values();

        $latestCustomers = Customer::with('package:id,name,category')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn (Customer $customer) => [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'package_name' => $customer->package?->name ?? '-',
                'join_date_label' => $customer->join_date?->translatedFormat('d M Y') ?? '-',
                'status' => $customer->status,
                'status_label' => $customer->status_label,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'new_customers_this_month' => $newThisMonth,
                'revenue_this_month' => $revenueThisMonth,
                'paid_invoices_this_month' => $paidThisMonth,
                'overdue_invoices' => $overdueCount,
                'unpaid_invoices' => $unpaidCount,
                'online_routers' => $onlineRouters,
                'total_routers' => $totalRouters,
                'total_pppoe' => $totalPppoe,
                'mapped_customers' => $mappedCustomers,
            ],
            'revenue_chart' => $revenueChart,
            'package_distribution' => $packageDistribution,
            'invoice_status' => [
                'total' => $invoiceTotal,
                'paid' => $invoicePaid,
                'unpaid' => $invoiceUnpaid,
                'overdue' => $invoiceOverdue,
                'cancelled' => $invoiceCancelled,
            ],
            'upcoming_due' => $upcomingDue,
            'network' => [
                'total_routers' => $totalRouters,
                'online_routers' => $onlineRouters,
                'total_pppoe' => $totalPppoe,
                'mapped_customers' => $mappedCustomers,
                'uptime_percentage' => $totalRouters > 0 ? round(($onlineRouters / $totalRouters) * 100) : 0,
            ],
            'latest_customers' => $latestCustomers,
        ]);
    }

    public function staffInvoices(Request $request): JsonResponse
    {
        if (!$this->staffUser($request)) {
            return $this->unauthorizedStaff();
        }

        $validated = $request->validate([
            'period' => ['nullable', 'date_format:Y-m'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $period = $validated['period'] ?? now()->format('Y-m');
        $year = substr($period, 0, 4);
        $month = substr($period, 5, 2);
        $limit = (int) ($validated['limit'] ?? 100);

        $invoices = Invoice::with('customer:id,name,phone,customer_number')
            ->whereYear('billing_period', $year)
            ->whereMonth('billing_period', $month)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'period' => $period,
            'stats' => [
                'total' => $invoices->count(),
                'unpaid' => $invoices->where('status', 'unpaid')->count(),
                'paid' => $invoices->where('status', 'paid')->count(),
                'overdue' => $invoices->where('status', 'overdue')->count(),
                'cancelled' => $invoices->where('status', 'cancelled')->count(),
            ],
            'invoices' => $invoices->map(fn (Invoice $invoice) => $this->staffInvoiceData($invoice, $request->bearerToken()))->values(),
        ]);
    }

    public function staffUpdateInvoiceStatus(Request $request, Invoice $invoice): JsonResponse
    {
        if (!$this->staffUser($request)) {
            return $this->unauthorizedStaff();
        }

        $validated = $request->validate([
            'status' => ['required', 'in:unpaid,paid,overdue,cancelled'],
        ]);

        $updateData = ['status' => $validated['status']];
        if ($validated['status'] === 'paid' && !$invoice->paid_at) {
            $updateData['paid_at'] = now();
        } elseif ($validated['status'] !== 'paid') {
            $updateData['paid_at'] = null;
        }

        $invoice->update($updateData);
        $invoice->load('customer:id,name,phone,customer_number');

        return response()->json([
            'success' => true,
            'message' => "Status tagihan \"{$invoice->invoice_number}\" berhasil diperbarui.",
            'invoice' => $this->staffInvoiceData($invoice, $request->bearerToken()),
        ]);
    }

    public function staffUpdatePaymentMethod(Request $request, Invoice $invoice): JsonResponse
    {
        if (!$this->staffUser($request)) {
            return $this->unauthorizedStaff();
        }

        $validated = $request->validate([
            'payment_method' => ['nullable', 'string', 'max:50'],
        ]);

        $invoice->update(['payment_method' => $validated['payment_method'] ?? null]);
        $invoice->load('customer:id,name,phone,customer_number');

        return response()->json([
            'success' => true,
            'message' => "Metode pembayaran tagihan \"{$invoice->invoice_number}\" berhasil diperbarui.",
            'invoice' => $this->staffInvoiceData($invoice, $request->bearerToken()),
        ]);
    }

    public function staffInvoicePdf(Request $request, Invoice $invoice)
    {
        abort_unless($this->staffUser($request), 401);

        $invoice->load('customer.package');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function invoiceQuery(Customer $customer)
    {
        return Invoice::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('billing_period')
            ->orderByDesc('created_at');
    }

    private function invoiceSummary(Customer $customer): array
    {
        $invoices = Invoice::where('customer_id', $customer->id)->get();

        return [
            'total' => $invoices->count(),
            'unpaid' => $invoices->where('status', 'unpaid')->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'outstanding_amount' => (int) $invoices->whereIn('status', ['unpaid', 'overdue'])->sum('amount'),
        ];
    }

    private function customerData(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'customer_number' => $customer->customer_number,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'status' => $customer->status,
            'status_label' => $customer->status_label,
            'package_name' => $customer->package?->name ?? 'Belum ada paket',
            'package_price' => (int) ($customer->package?->price ?? 0),
            'billing_date' => $customer->billing_date ?? 1,
        ];
    }

    private function invoiceData(Invoice $invoice, array $context = []): array
    {
        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'billing_period' => $invoice->billing_period?->format('Y-m-d'),
            'billing_period_label' => $invoice->billing_period?->translatedFormat('F Y') ?? '-',
            'amount' => (int) $invoice->amount,
            'status' => $invoice->status,
            'status_label' => $invoice->status_label,
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'due_date_label' => $invoice->due_date?->translatedFormat('d F Y') ?? '-',
            'paid_at' => $invoice->paid_at?->format('Y-m-d H:i:s'),
            'payment_method' => $invoice->payment_method,
            'notes' => $invoice->notes,
            'pdf_url' => url('/api/mobile/customer/invoices/' . $invoice->id . '/pdf?' . http_build_query([
                'customer_number' => $context['customer_number'] ?? '',
            ])),
        ];
    }

    private function staffInvoiceData(Invoice $invoice, ?string $token = null): array
    {
        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'customer_id' => $invoice->customer_id,
            'customer_name' => $invoice->customer?->name ?? '-',
            'customer_number' => $invoice->customer?->customer_number ?? '-',
            'customer_phone' => $invoice->customer?->phone ?? '-',
            'billing_period' => $invoice->billing_period?->format('Y-m-d'),
            'billing_period_label' => $invoice->billing_period?->translatedFormat('F Y') ?? '-',
            'amount' => (int) $invoice->amount,
            'status' => $invoice->status,
            'status_label' => $invoice->status_label,
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'due_date_label' => $invoice->due_date?->translatedFormat('d F Y') ?? '-',
            'paid_at' => $invoice->paid_at?->format('Y-m-d H:i:s'),
            'payment_method' => $invoice->payment_method,
            'notes' => $invoice->notes,
            'pdf_url' => url('/api/mobile/staff/invoices/' . $invoice->id . '/pdf?' . http_build_query([
                'token' => $token ?? '',
            ])),
        ];
    }

    private function makeStaffToken(User $user): string
    {
        $issuedAt = now()->timestamp;
        $payload = $user->id . '|' . $issuedAt;
        $signature = hash_hmac('sha256', $payload . '|' . $user->password, config('app.key'));

        return base64_encode($payload . '|' . $signature);
    }

    private function staffUser(Request $request): ?User
    {
        $token = $request->bearerToken() ?: $request->query('token');
        if (!$token) {
            return null;
        }

        $decoded = base64_decode($token, true);
        if (!$decoded) {
            return null;
        }

        $parts = explode('|', $decoded);
        if (count($parts) !== 3) {
            return null;
        }

        [$userId, $issuedAt, $signature] = $parts;
        if (!ctype_digit($issuedAt) || (int) $issuedAt < now()->subDays(30)->timestamp) {
            return null;
        }

        $user = User::whereKey($userId)->whereIn('role', ['admin', 'operator'])->first();
        if (!$user) {
            return null;
        }

        $expected = hash_hmac('sha256', $user->id . '|' . $issuedAt . '|' . $user->password, config('app.key'));

        return hash_equals($expected, $signature) ? $user : null;
    }

    private function unauthorizedStaff(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sesi admin/operator tidak valid. Silakan login ulang.',
        ], 401);
    }
}
