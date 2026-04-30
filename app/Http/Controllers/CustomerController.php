<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('package:id,name,category')
            ->orderByDesc('created_at')
            ->get();

        $packages = Package::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get(['id', 'name', 'category', 'price']);

        $stats = [
            'total'     => $customers->count(),
            'aktif'     => $customers->where('status', 'aktif')->count(),
            'suspend'   => $customers->where('status', 'suspend')->count(),
            'terminate' => $customers->where('status', 'terminate')->count(),
        ];

        return view('customers.index', compact('customers', 'packages', 'stats'));
    }

    public function show(Customer $customer)
    {
        $customer->load('package');
        return view('customers.show', compact('customer'));
    }

    public function liveTraffic(Customer $customer, \App\Services\MikrotikService $mikrotik)
    {
        if (empty($customer->pppoe_user)) {
            return response()->json([
                'success' => false, 
                'message' => 'Pelanggan belum dimaping PPPoE akun nya.'
            ]);
        }

        $router = \App\Models\Router::where('status', 'online')->first();
        if (!$router) {
            $router = \App\Models\Router::first();
        }

        if (!$router) {
            return response()->json([
                'success' => false, 
                'message' => 'Router belum dikonfigurasi.'
            ]);
        }

        $conn = $mikrotik->connect($router->host, $router->api_port, $router->username, $router->password);
        if (!$conn['success']) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal terhubung ke Mikrotik.'
            ]);
        }

        // Cari interface PPPoE (biasanya berawalan <pppoe-username>)
        $interface = '<pppoe-' . $customer->pppoe_user . '>';
        $traffic = $mikrotik->getInterfaceTraffic($interface);

        // Ambil penggunaan data uptime dari interface
        $interfaces = $mikrotik->getInterfaces();
        $rxBytes = 0;
        $txBytes = 0;
        foreach ($interfaces as $iface) {
            if (isset($iface['name']) && $iface['name'] === $interface) {
                $rxBytes = (int)($iface['rx-byte'] ?? 0);
                $txBytes = (int)($iface['tx-byte'] ?? 0);
                break;
            }
        }
        
        // Konversi ke GB (Catatan: RX mikrotik = Upload pelanggan, TX mikrotik = Download pelanggan)
        $usageData = [
            'download_gb' => round($txBytes / 1073741824, 2),
            'upload_gb'   => round($rxBytes / 1073741824, 2)
        ];

        $mikrotik->close();

        if (empty($traffic) || !isset($traffic['rx-bits-per-second'])) {
            return response()->json([
                'success' => true,
                'status'  => 'offline',
                'rx'      => 0, // ini untuk Download di frontend
                'tx'      => 0, // ini untuk Upload di frontend
                'usage'   => $usageData
            ]);
        }

        return response()->json([
            'success' => true,
            'status'  => 'online',
            // Konversi dari bps ke Mbps 
            // Router TX -> Customer Download, Router RX -> Customer Upload
            'rx'      => round((int)$traffic['tx-bits-per-second'] / 1000000, 2),
            'tx'      => round((int)$traffic['rx-bits-per-second'] / 1000000, 2),
            'usage'   => $usageData
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['customer_number'] = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $customer  = Customer::create($validated);
        $customer->load('package:id,name,category');

        return response()->json([
            'success'  => true,
            'message'  => "Pelanggan \"{$customer->name}\" berhasil ditambahkan.",
            'customer' => $customer->toJsonData(),
        ]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $customer->update($validated);
        $customer->load('package:id,name,category');

        return response()->json([
            'success'  => true,
            'message'  => "Data \"{$customer->name}\" berhasil diperbarui.",
            'customer' => $customer->toJsonData(),
        ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $name = $customer->name;
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => "Pelanggan \"{$name}\" berhasil dihapus.",
        ]);
    }

    public function updateStatus(Request $request, Customer $customer): JsonResponse
    {
        $request->validate(['status' => 'required|in:aktif,suspend,terminate']);
        $customer->update(['status' => $request->status]);

        $labels = ['aktif' => 'diaktifkan', 'suspend' => 'disuspend', 'terminate' => 'diterminasi'];

        return response()->json([
            'success' => true,
            'message' => "Pelanggan \"{$customer->name}\" berhasil {$labels[$request->status]}.",
            'status'  => $customer->status,
        ]);
    }

    public function generateDummy(Request $request): JsonResponse
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ]);

        $count = $request->count;
        $packages = Package::where('is_active', true)->pluck('id')->toArray();

        if (empty($packages)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate: Belum ada paket internet yang aktif.',
            ], 422);
        }

        $generated = [];
        for ($i = 0; $i < $count; $i++) {
            $customer = Customer::create([
                'name'       => fake()->name(),
                'email'      => fake()->unique()->safeEmail(),
                'phone'      => '08' . fake()->numerify('##########'),
                'address'    => fake()->address(),
                'package_id' => $packages[array_rand($packages)],
                'ip_address' => fake()->ipv4(),
                'pppoe_user' => fake()->userName(),
                'status'     => 'aktif',
                'join_date'  => now()->subDays(rand(0, 30))->format('Y-m-d'),
                'billing_date' => rand(1, 28),
                'customer_number' => str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            ]);
            $customer->load('package:id,name,category');
            $generated[] = $customer->toJsonData();
        }

        return response()->json([
            'success'   => true,
            'message'   => "Berhasil generate {$count} data pelanggan dummy.",
            'customers' => $generated,
        ]);
    }


    private function rules(): array
    {
        return [
            'name'       => 'required|string|max:150',
            'email'      => 'nullable|email|max:150',
            'phone'      => 'required|string|max:20',
            'address'    => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'ip_address' => 'nullable|string|max:45',
            'pppoe_user' => 'nullable|string|max:100',
            'onu_id'     => 'nullable|string|max:100',
            'status'     => 'required|in:aktif,suspend,terminate',
            'join_date'    => 'required|date',
            'billing_date' => 'nullable|integer|min:1|max:31',
            'notes'        => 'nullable|string|max:1000',
        ];
    }
}
