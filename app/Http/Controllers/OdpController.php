<?php

namespace App\Http\Controllers;

use App\Models\Odp;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OdpController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $odp       = Odp::create($validated);
        $odp->load(['router:id,name', 'odc:id,name'])->loadCount('customers');

        return response()->json([
            'success' => true,
            'message' => "ODP \"{$odp->name}\" berhasil ditambahkan.",
            'odp'     => $odp->toJsonData(),
        ]);
    }

    public function update(Request $request, Odp $odp): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $odp->update($validated);
        $odp->load(['router:id,name', 'odc:id,name'])->loadCount('customers');

        return response()->json([
            'success' => true,
            'message' => "ODP \"{$odp->name}\" berhasil diperbarui.",
            'odp'     => $odp->toJsonData(),
        ]);
    }

    public function destroy(Odp $odp): JsonResponse
    {
        $name = $odp->name;
        $odp->delete();

        return response()->json([
            'success' => true,
            'message' => "ODP \"{$name}\" berhasil dihapus.",
        ]);
    }

    public function mapCustomer(Request $request, Odp $odp): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cable_distance_meters' => 'nullable|integer|min:0|max:1000000',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $customer->update([
            'odp_id' => $odp->id,
            'cable_distance_meters' => $validated['cable_distance_meters'] ?? null,
            'odp_mapped_at' => now(),
        ]);

        $odp->load(['router:id,name', 'odc:id,name'])->loadCount('customers');
        $customer->load(['package:id,name', 'odp:id,name']);

        return response()->json([
            'success' => true,
            'message' => "Pelanggan \"{$customer->name}\" berhasil dimapping ke {$odp->name}.",
            'odp' => $odp->toJsonData(),
            'customer' => $customer->toJsonData(),
        ]);
    }

    public function unmapCustomer(Odp $odp, Customer $customer): JsonResponse
    {
        if ((int) $customer->odp_id !== (int) $odp->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak sedang terhubung ke ODP ini.',
            ], 422);
        }

        $customer->update([
            'odp_id' => null,
            'cable_distance_meters' => null,
            'odp_mapped_at' => null,
        ]);

        $odp->load(['router:id,name', 'odc:id,name'])->loadCount('customers');

        return response()->json([
            'success' => true,
            'message' => "Mapping pelanggan \"{$customer->name}\" berhasil dilepas.",
            'odp' => $odp->toJsonData(),
            'customer_id' => $customer->id,
        ]);
    }

    private function rules(): array
    {
        return [
            'name'      => 'required|string|max:100',
            'router_id' => 'nullable|exists:routers,id',
            'odc_id'    => 'nullable|exists:odcs,id',
            'location'  => 'required|string|max:255',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'capacity'  => 'required|integer|min:1|max:255',
            'notes'     => 'nullable|string',
        ];
    }
}
