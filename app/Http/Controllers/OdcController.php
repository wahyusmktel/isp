<?php

namespace App\Http\Controllers;

use App\Models\Odc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OdcController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $odc = Odc::create($request->validate($this->rules()));
        $odc->loadCount('odps');

        return response()->json([
            'success' => true,
            'message' => "ODC \"{$odc->name}\" berhasil ditambahkan.",
            'odc' => $odc->toJsonData(),
        ]);
    }

    public function update(Request $request, Odc $odc): JsonResponse
    {
        $odc->update($request->validate($this->rules()));
        $odc->loadCount('odps');

        return response()->json([
            'success' => true,
            'message' => "ODC \"{$odc->name}\" berhasil diperbarui.",
            'odc' => $odc->toJsonData(),
        ]);
    }

    public function destroy(Odc $odc): JsonResponse
    {
        $name = $odc->name;
        $odc->delete();

        return response()->json([
            'success' => true,
            'message' => "ODC \"{$name}\" berhasil dihapus.",
        ]);
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'capacity' => 'nullable|integer|min:0|max:65535',
            'notes' => 'nullable|string',
        ];
    }
}
