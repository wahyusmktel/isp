<?php

namespace App\Http\Controllers;

use App\Models\Odp;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::withCount('odps')->orderByDesc('created_at')->get();
        $odps    = Odp::with('router:id,name')->orderBy('name')->get();

        $routerStats = [
            'total'   => $routers->count(),
            'online'  => $routers->where('status', 'online')->count(),
            'offline' => $routers->where('status', 'offline')->count(),
        ];

        $odpStats = [
            'total'   => $odps->count(),
            'cap_total' => $odps->sum('capacity'),
        ];

        return view('network.index', compact('routers', 'odps', 'routerStats', 'odpStats'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $router    = Router::create($validated);
        $router->loadCount('odps');

        return response()->json([
            'success' => true,
            'message' => "Router \"{$router->name}\" berhasil ditambahkan.",
            'router'  => $router->toJsonData(),
        ]);
    }

    public function update(Request $request, Router $router): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $router->update($validated);
        $router->loadCount('odps');

        return response()->json([
            'success' => true,
            'message' => "Router \"{$router->name}\" berhasil diperbarui.",
            'router'  => $router->toJsonData(),
        ]);
    }

    public function destroy(Router $router): JsonResponse
    {
        $name = $router->name;
        $router->delete();

        return response()->json([
            'success' => true,
            'message' => "Router \"{$name}\" berhasil dihapus.",
        ]);
    }

    public function testConnection(Router $router): JsonResponse
    {
        $svc    = new MikrotikService();
        $result = $svc->connect($router->host, $router->api_port, $router->username, $router->password);

        if ($result['success']) {
            $resource    = $svc->getSystemResource();
            $pppoeCount  = $svc->getPppoeOnlineCount();
            $svc->close();

            $router->update([
                'status'        => 'online',
                'model'         => $resource['board-name'] ?? $router->model,
                'firmware'      => $resource['version'] ?? $router->firmware,
                'pppoe_online'  => $pppoeCount,
                'last_check_at' => now(),
            ]);
        } else {
            $router->update(['status' => 'offline', 'last_check_at' => now()]);
        }

        $router->loadCount('odps');

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'router'  => $router->toJsonData(),
        ]);
    }

    public function liveData(Router $router): JsonResponse
    {
        $svc    = new MikrotikService();
        $result = $svc->connect($router->host, $router->api_port, $router->username, $router->password);

        if (!$result['success']) {
            $router->update(['status' => 'offline', 'last_check_at' => now()]);
            return response()->json(['success' => false, 'message' => $result['message']]);
        }

        $resource   = $svc->getSystemResource();
        $actives    = $svc->getPppoeActives();
        $interfaces = $svc->getInterfaces();
        $svc->close();

        $router->update([
            'status'        => 'online',
            'model'         => $resource['board-name'] ?? $router->model,
            'firmware'      => $resource['version'] ?? $router->firmware,
            'pppoe_online'  => count($actives),
            'last_check_at' => now(),
        ]);

        $router->loadCount('odps');

        return response()->json([
            'success'    => true,
            'resource'   => $resource,
            'actives'    => $actives,
            'interfaces' => $interfaces,
            'router'     => $router->toJsonData(),
        ]);
    }

    private function rules(): array
    {
        return [
            'name'        => 'required|string|max:100',
            'host'        => 'required|string|max:255',
            'api_port'    => 'required|integer|min:1|max:65535',
            'winbox_port' => 'nullable|integer|min:1|max:65535',
            'username'    => 'required|string|max:100',
            'password'    => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'notes'       => 'nullable|string',
        ];
    }
}
