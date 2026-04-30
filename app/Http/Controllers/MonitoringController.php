<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    public function index()
    {
        $routers = Router::withCount('odps')->orderBy('name')->get();

        $mappedCustomers = Customer::with('package:id,name')
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('network.monitoring', compact('routers', 'mappedCustomers'));
    }

    /**
     * Refresh: connect to all routers, collect active PPPoE, return consolidated data.
     */
    public function refresh(): JsonResponse
    {
        $routers = Router::orderBy('name')->get();
        $svc     = new MikrotikService();
        $results = [];
        $allActives = [];

        foreach ($routers as $router) {
            $conn = $svc->connect($router->host, $router->api_port, $router->username, $router->password, 3);

            if ($conn['success']) {
                $resource = $svc->getSystemResource();
                $actives  = $svc->getPppoeActives();
                $svc->close();

                $cpuLoad = $resource['cpu-load'] ?? 0;
                $freeM   = $resource['free-memory'] ?? 0;
                $totalM  = $resource['total-memory'] ?? 1;
                $memPct  = $totalM > 0 ? round((1 - intval($freeM) / intval($totalM)) * 100) : 0;

                $router->update([
                    'status'        => 'online',
                    'model'         => $resource['board-name'] ?? $router->model,
                    'firmware'      => $resource['version'] ?? $router->firmware,
                    'pppoe_online'  => count($actives),
                    'last_check_at' => now(),
                ]);

                foreach ($actives as $a) {
                    $allActives[] = [
                        'name'      => $a['name'] ?? '',
                        'address'   => $a['address'] ?? '',
                        'uptime'    => $a['uptime'] ?? '',
                        'service'   => $a['service'] ?? '',
                        'router_id' => $router->id,
                    ];
                }

                $results[] = [
                    'id'           => $router->id,
                    'name'         => $router->name,
                    'host'         => $router->host,
                    'status'       => 'online',
                    'model'        => $resource['board-name'] ?? $router->model,
                    'firmware'     => $resource['version'] ?? $router->firmware,
                    'cpu_load'     => intval($cpuLoad),
                    'mem_pct'      => $memPct,
                    'uptime'       => $resource['uptime'] ?? '—',
                    'pppoe_online' => count($actives),
                ];
            } else {
                $router->update(['status' => 'offline', 'last_check_at' => now()]);

                $results[] = [
                    'id'           => $router->id,
                    'name'         => $router->name,
                    'host'         => $router->host,
                    'status'       => 'offline',
                    'model'        => $router->model,
                    'firmware'     => $router->firmware,
                    'cpu_load'     => 0,
                    'mem_pct'      => 0,
                    'uptime'       => '—',
                    'pppoe_online' => 0,
                ];
            }
        }

        // Build active usernames set for quick lookup
        $activeUsernames = array_column($allActives, 'name');

        // Get mapped customers
        $customers = Customer::with('package:id,name')
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get()
            ->map(function ($c) use ($activeUsernames, $allActives) {
                $isOnline = in_array($c->pppoe_user, $activeUsernames);
                $session  = null;
                if ($isOnline) {
                    foreach ($allActives as $a) {
                        if ($a['name'] === $c->pppoe_user) { $session = $a; break; }
                    }
                }
                return [
                    'id'           => $c->id,
                    'name'         => $c->name,
                    'pppoe_user'   => $c->pppoe_user,
                    'package_name' => $c->package?->name ?? '—',
                    'phone'        => $c->phone,
                    'online'       => $isOnline,
                    'ip'           => $session['address'] ?? $c->ip_address ?? '',
                    'uptime'       => $session['uptime'] ?? '',
                    'router_id'    => $session['router_id'] ?? null,
                ];
            });

        $onlineRouters = collect($results)->where('status', 'online')->count();
        $totalPppoe    = collect($results)->sum('pppoe_online');
        $onlineCust    = $customers->where('online', true)->count();

        return response()->json([
            'success'   => true,
            'routers'   => $results,
            'customers' => $customers->values(),
            'summary'   => [
                'total_routers'    => count($results),
                'online_routers'   => $onlineRouters,
                'offline_routers'  => count($results) - $onlineRouters,
                'total_pppoe'      => $totalPppoe,
                'online_customers' => $onlineCust,
                'offline_customers'=> $customers->count() - $onlineCust,
                'total_mapped'     => $customers->count(),
            ],
            'timestamp' => now()->format('H:i:s'),
        ]);
    }
}
