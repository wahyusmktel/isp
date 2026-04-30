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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
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
