<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('sort_order')->orderBy('price')->get();

        $stats = [
            'total'     => $packages->count(),
            'aktif'     => $packages->where('is_active', true)->count(),
            'nonaktif'  => $packages->where('is_active', false)->count(),
            'home'      => $packages->where('category', 'home')->count(),
            'bisnis'    => $packages->where('category', 'bisnis')->count(),
            'dedicated' => $packages->where('category', 'dedicated')->count(),
        ];

        return view('packages.index', compact('packages', 'stats'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->input('is_active') === '1';
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        $package = Package::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Paket \"{$package->name}\" berhasil ditambahkan.",
            'package' => $package->toJsonData(),
        ]);
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->input('is_active') === '1';
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        $package->update($validated);
        $package->refresh();

        return response()->json([
            'success' => true,
            'message' => "Paket \"{$package->name}\" berhasil diperbarui.",
            'package' => $package->toJsonData(),
        ]);
    }

    public function destroy(Package $package): JsonResponse
    {
        $name = $package->name;
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => "Paket \"{$name}\" berhasil dihapus.",
        ]);
    }

    public function toggleStatus(Package $package): JsonResponse
    {
        $package->update(['is_active' => ! $package->is_active]);
        $label = $package->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success'   => true,
            'message'   => "Paket \"{$package->name}\" berhasil {$label}.",
            'is_active' => $package->is_active,
        ]);
    }

    public function generateDummy(Request $request): JsonResponse
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50',
        ]);

        $count = $request->count;
        $categories = ['home', 'bisnis', 'dedicated'];
        $speeds = [10, 20, 30, 50, 100, 150, 200, 300, 500];

        $generated = [];
        for ($i = 0; $i < $count; $i++) {
            $cat = $categories[array_rand($categories)];
            $spd = $speeds[array_rand($speeds)];
            $price = $spd * 10000 + rand(1, 9) * 5000;

            $package = Package::create([
                'name'           => strtoupper($cat) . " " . $spd . " Mbps",
                'category'       => $cat,
                'speed_download' => $spd,
                'speed_upload'   => $spd / 2,
                'price'          => $price,
                'contention'     => '1:' . rand(1, 8),
                'description'    => "Paket internet {$cat} dengan kecepatan {$spd} Mbps.",
                'is_active'      => true,
                'sort_order'     => rand(0, 100),
            ]);
            $generated[] = $package->toJsonData();
        }

        return response()->json([
            'success'  => true,
            'message'  => "Berhasil generate {$count} data paket dummy.",
            'packages' => $generated,
        ]);
    }


    private function rules(): array
    {
        return [
            'name'           => 'required|string|max:100',
            'category'       => 'required|in:home,bisnis,dedicated',
            'speed_download' => 'required|integer|min:1|max:10000',
            'speed_upload'   => 'required|integer|min:1|max:10000',
            'price'          => 'required|numeric|min:0',
            'contention'     => 'nullable|string|max:20',
            'description'    => 'nullable|string|max:500',
            'sort_order'     => 'nullable|integer|min:0',
        ];
    }
}
