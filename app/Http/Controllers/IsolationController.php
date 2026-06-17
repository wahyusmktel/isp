<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Router;
use App\Models\Setting;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IsolationController extends Controller
{
    private string $addressList = 'isolir';

    public function index()
    {
        $settings = Setting::getAllAsArray();
        $customers = $this->eligibleCustomers();
        $isolated = Customer::where('is_isolated', true)->count();
        $candidates = $customers->where('is_candidate', true)->count();
        $totalOverdueAmount = $customers->sum('overdue_amount');

        return view('isolation.index', compact('settings', 'customers', 'isolated', 'candidates', 'totalOverdueAmount'));
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'isolation_auto_enabled' => 'nullable|boolean',
            'isolation_grace_days' => 'required|integer|min:0|max:60',
            'isolation_bank_name' => 'nullable|string|max:100',
            'isolation_bank_account' => 'nullable|string|max:100',
            'isolation_account_name' => 'nullable|string|max:150',
            'isolation_cash_note' => 'nullable|string|max:500',
        ]);

        Setting::set('isolation_auto_enabled', $request->boolean('isolation_auto_enabled') ? '1' : '0');
        Setting::set('isolation_grace_days', (string) $validated['isolation_grace_days']);
        Setting::set('isolation_bank_name', $validated['isolation_bank_name'] ?? '');
        Setting::set('isolation_bank_account', $validated['isolation_bank_account'] ?? '');
        Setting::set('isolation_account_name', $validated['isolation_account_name'] ?? '');
        Setting::set('isolation_cash_note', $validated['isolation_cash_note'] ?? 'Pembayaran tunai dapat dilakukan langsung ke kantor atau petugas resmi.');

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan isolir berhasil disimpan.',
        ]);
    }

    public function isolate(Customer $customer, MikrotikService $mikrotik): JsonResponse
    {
        if (blank($customer->ip_address)) {
            return response()->json(['success' => false, 'message' => 'Pelanggan belum memiliki IP Address.'], 422);
        }

        $routerResult = $this->applyMikrotikIsolation($customer, $mikrotik, true);
        if (! $routerResult['success']) {
            return response()->json($routerResult, 422);
        }

        $customer->update([
            'is_isolated' => true,
            'isolated_at' => now(),
            'isolation_reason' => $this->isolationReason($customer),
            'isolation_released_at' => null,
            'status' => 'suspend',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pelanggan {$customer->name} berhasil diisolir.",
            'customer' => $customer->fresh()->toJsonData(),
            'router_message' => $routerResult['message'],
        ]);
    }

    public function release(Customer $customer, MikrotikService $mikrotik): JsonResponse
    {
        if (blank($customer->ip_address)) {
            return response()->json(['success' => false, 'message' => 'Pelanggan belum memiliki IP Address.'], 422);
        }

        $routerResult = $this->applyMikrotikIsolation($customer, $mikrotik, false);
        if (! $routerResult['success']) {
            return response()->json($routerResult, 422);
        }

        $customer->update([
            'is_isolated' => false,
            'isolation_released_at' => now(),
            'status' => 'aktif',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Isolir pelanggan {$customer->name} berhasil dibuka.",
            'customer' => $customer->fresh()->toJsonData(),
            'router_message' => $routerResult['message'],
        ]);
    }

    public function runAutomatic(MikrotikService $mikrotik): JsonResponse
    {
        if (Setting::get('isolation_auto_enabled', '0') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Isolir otomatis masih nonaktif. Aktifkan dari pengaturan isolir jika sudah siap uji coba otomatis.',
            ], 422);
        }

        $processed = 0;
        $failed = [];

        foreach ($this->eligibleCustomers()->where('is_candidate', true)->where('is_isolated', false) as $customer) {
            $result = $this->applyMikrotikIsolation($customer, $mikrotik, true);
            if (! $result['success']) {
                $failed[] = "{$customer->name}: {$result['message']}";
                continue;
            }

            $customer->update([
                'is_isolated' => true,
                'isolated_at' => now(),
                'isolation_reason' => $this->isolationReason($customer),
                'isolation_released_at' => null,
                'status' => 'suspend',
            ]);
            $processed++;
        }

        return response()->json([
            'success' => empty($failed),
            'message' => "Proses otomatis selesai. {$processed} pelanggan diisolir.",
            'failed' => $failed,
        ]);
    }

    public function portal(Request $request)
    {
        $settings = Setting::getAllAsArray();
        $ip = $request->query('ip') ?: $request->ip();
        $customer = Customer::with('package')
            ->where(function ($query) use ($ip, $request) {
                $query->where('ip_address', $ip);
                if (filled($request->query('user'))) {
                    $query->orWhere('pppoe_user', $request->query('user'));
                }
            })
            ->first();
        $invoice = $customer
            ? Invoice::where('customer_id', $customer->id)->whereIn('status', ['unpaid', 'overdue'])->orderBy('due_date')->first()
            : null;

        return view('isolation.portal', compact('settings', 'customer', 'invoice', 'ip'));
    }

    private function eligibleCustomers()
    {
        $graceDays = (int) Setting::get('isolation_grace_days', '0');
        $limitDate = now()->subDays($graceDays)->toDateString();

        return Customer::with(['package'])
            ->where('status', '!=', 'terminate')
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->orderBy('name')
            ->get()
            ->map(function (Customer $customer) use ($limitDate) {
                $invoices = Invoice::where('customer_id', $customer->id)
                    ->whereIn('status', ['unpaid', 'overdue'])
                    ->orderBy('due_date')
                    ->get();

                $oldestDue = $invoices->first()?->due_date;
                $customer->overdue_count = $invoices->count();
                $customer->overdue_amount = (int) $invoices->sum('amount');
                $customer->oldest_due_date = $oldestDue;
                $customer->is_candidate = $oldestDue ? $oldestDue->toDateString() <= $limitDate : false;

                return $customer;
            });
    }

    private function applyMikrotikIsolation(Customer $customer, MikrotikService $mikrotik, bool $isolate): array
    {
        $router = Router::where('status', 'online')->first() ?: Router::first();
        if (! $router) {
            return ['success' => false, 'message' => 'Router Mikrotik belum dikonfigurasi.'];
        }

        $connection = $mikrotik->connect($router->host, $router->api_port, $router->username, $router->password);
        if (! $connection['success']) {
            return ['success' => false, 'message' => "Gagal konek ke {$router->name}: {$connection['message']}"];
        }

        if ($isolate) {
            $result = $mikrotik->addAddressList($this->addressList, $customer->ip_address, "isolir {$customer->name}");
            if (filled($customer->pppoe_user)) {
                $mikrotik->removePppoeActiveByName($customer->pppoe_user);
            }
        } else {
            $result = $mikrotik->removeAddressList($this->addressList, $customer->ip_address);
        }

        $mikrotik->close();
        return $result;
    }

    private function isolationReason(Customer $customer): string
    {
        $invoice = Invoice::where('customer_id', $customer->id)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->orderBy('due_date')
            ->first();

        return $invoice
            ? "Tagihan {$invoice->invoice_number} jatuh tempo {$invoice->due_date?->format('d M Y')}"
            : 'Isolir manual';
    }
}
