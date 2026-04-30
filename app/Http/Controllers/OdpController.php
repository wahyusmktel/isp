<?php

namespace App\Http\Controllers;

use App\Models\Odp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OdpController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $odp       = Odp::create($validated);
        $odp->load('router:id,name');

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
        $odp->load('router:id,name');

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

    private function rules(): array
    {
        return [
            'name'      => 'required|string|max:100',
            'router_id' => 'nullable|exists:routers,id',
            'location'  => 'required|string|max:255',
            'capacity'  => 'required|integer|min:1|max:255',
            'notes'     => 'nullable|string',
        ];
    }
}
