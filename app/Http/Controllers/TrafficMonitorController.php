<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrafficMonitorController extends Controller
{
    public function index()
    {
        $routers = Router::where('status', 'online')->orderBy('name')->get();

        $mappedCustomers = Customer::with('package:id,name')
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('network.traffic', compact('routers', 'mappedCustomers'));
    }

    /**
     * Fetch real-time traffic data from a specific router.
     * Uses simple queues for per-customer bandwidth and PPPoE actives for session info.
     */
    public function fetchTraffic(Router $router): JsonResponse
    {
        $svc  = new MikrotikService();
        $conn = $svc->connect($router->host, $router->api_port, $router->username, $router->password, 3);

        if (!$conn['success']) {
            return response()->json([
                'success' => false,
                'message' => "Gagal terhubung ke {$router->name}: {$conn['message']}",
            ]);
        }

        $queues  = $svc->getSimpleQueues();
        $actives = $svc->getPppoeActives();
        $resource = $svc->getSystemResource();
        $svc->close();

        // Build active sessions lookup by name
        $activeMap = [];
        foreach ($actives as $a) {
            $name = $a['name'] ?? '';
            $activeMap[$name] = [
                'address' => $a['address'] ?? '',
                'uptime'  => $a['uptime'] ?? '',
                'service' => $a['service'] ?? '',
            ];
        }

        // Get mapped customers lookup by pppoe_user
        $customers = Customer::with('package:id,name')
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->pluck('name', 'pppoe_user')
            ->toArray();

        $customerPkgs = Customer::with('package:id,name')
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->get()
            ->keyBy('pppoe_user');

        // Process queues into traffic data
        $trafficData = [];
        $totalUpload = 0;
        $totalDownload = 0;

        foreach ($queues as $q) {
            $name    = $q['name'] ?? '';
            $target  = $q['target'] ?? '';
            $disabled = ($q['disabled'] ?? 'false') === 'true';

            // Parse rate: "upload/download" in bps
            $rate = $q['rate'] ?? '0/0';
            $rateParts = explode('/', $rate);
            $uploadBps   = intval($rateParts[0] ?? 0);
            $downloadBps = intval($rateParts[1] ?? 0);

            // Parse max-limit
            $maxLimit = $q['max-limit'] ?? '0/0';
            $maxParts = explode('/', $maxLimit);
            $maxUpload   = intval($maxParts[0] ?? 0);
            $maxDownload = intval($maxParts[1] ?? 0);

            // Parse bytes
            $bytes = $q['bytes'] ?? '0/0';
            $bytesParts = explode('/', $bytes);
            $bytesUp   = intval($bytesParts[0] ?? 0);
            $bytesDown = intval($bytesParts[1] ?? 0);

            // Match with PPPoE user
            $pppoeUser = $this->extractPppoeUser($name, $target);
            $isOnline  = isset($activeMap[$pppoeUser]) || ($uploadBps > 0 || $downloadBps > 0);
            $custName  = $customers[$pppoeUser] ?? null;
            $custPkg   = $customerPkgs[$pppoeUser] ?? null;

            $totalUpload   += $uploadBps;
            $totalDownload += $downloadBps;

            $trafficData[] = [
                'queue_name'   => $name,
                'pppoe_user'   => $pppoeUser,
                'customer_name'=> $custName,
                'package_name' => $custPkg?->package?->name ?? '',
                'target'       => $target,
                'disabled'     => $disabled,
                'online'       => $isOnline,
                'upload_bps'   => $uploadBps,
                'download_bps' => $downloadBps,
                'max_upload'   => $maxUpload,
                'max_download' => $maxDownload,
                'bytes_up'     => $bytesUp,
                'bytes_down'   => $bytesDown,
                'ip'           => $activeMap[$pppoeUser]['address'] ?? '',
                'uptime'       => $activeMap[$pppoeUser]['uptime'] ?? '',
            ];
        }

        // Sort: online first, then by download desc
        usort($trafficData, function ($a, $b) {
            if ($a['online'] !== $b['online']) return $b['online'] - $a['online'];
            return $b['download_bps'] - $a['download_bps'];
        });

        return response()->json([
            'success'       => true,
            'router_id'     => $router->id,
            'router_name'   => $router->name,
            'traffic'       => $trafficData,
            'summary'       => [
                'total_queues'   => count($trafficData),
                'active_queues'  => count(array_filter($trafficData, fn($t) => $t['online'])),
                'total_upload'   => $totalUpload,
                'total_download' => $totalDownload,
                'cpu_load'       => $resource['cpu-load'] ?? 0,
            ],
            'timestamp'     => now()->format('H:i:s'),
        ]);
    }

    /**
     * Fetch live stats for ether4-Internet_In interface + router uptime.
     */
    public function fetchInterfaceStats(Router $router): JsonResponse
    {
        $svc  = new MikrotikService();
        $conn = $svc->connect($router->host, $router->api_port, $router->username, $router->password, 3);

        if (!$conn['success']) {
            return response()->json([
                'success' => false,
                'message' => "Gagal terhubung ke {$router->name}: {$conn['message']}",
            ]);
        }

        $traffic   = $svc->getInterfaceTraffic('ether4-Internet_In');
        $resource  = $svc->getSystemResource();
        $interfaces = $svc->getInterfaces();
        $svc->close();

        $iface = collect($interfaces)->firstWhere('name', 'ether4-Internet_In') ?? [];

        return response()->json([
            'success'      => true,
            'download_bps' => (int) ($traffic['rx-bits-per-second'] ?? 0),
            'upload_bps'   => (int) ($traffic['tx-bits-per-second'] ?? 0),
            'rx_byte'      => (int) ($iface['rx-byte'] ?? 0),
            'tx_byte'      => (int) ($iface['tx-byte'] ?? 0),
            'uptime'       => $resource['uptime'] ?? '—',
        ]);
    }

    /**
     * Try to extract PPPoE username from queue name or target.
     */
    private function extractPppoeUser(string $name, string $target): string
    {
        // Queue name often is the PPPoE username, or "<pppoe-username>"
        $cleaned = preg_replace('/^<pppoe-/', '', $name);
        $cleaned = rtrim($cleaned, '>');

        // If target looks like "<pppoe-xxx>" extract it
        if (preg_match('/<pppoe-([^>]+)>/', $target, $m)) {
            return $m[1];
        }

        return $cleaned ?: $name;
    }
}
