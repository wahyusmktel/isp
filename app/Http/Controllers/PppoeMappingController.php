<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PppoeMappingController extends Controller
{
    public function index()
    {
        $routers   = Router::orderBy('name')->get();
        $customers = Customer::with('package:id,name')
            ->orderBy('name')
            ->get();

        return view('network.pppoe-mapping', compact('routers', 'customers'));
    }

    /**
     * Fetch PPPoE secrets from a specific router.
     */
    public function fetchSecrets(Router $router): JsonResponse
    {
        $svc    = new MikrotikService();
        $result = $svc->connect($router->host, $router->api_port, $router->username, $router->password);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => "Gagal terhubung ke {$router->name}: {$result['message']}",
            ]);
        }

        $secrets = $svc->getPppoeSecrets();
        $actives = $svc->getPppoeActives();
        $svc->close();

        // Build a lookup of active sessions by username
        $activeMap = [];
        foreach ($actives as $a) {
            $activeMap[$a['name'] ?? ''] = [
                'address' => $a['address'] ?? '',
                'uptime'  => $a['uptime'] ?? '',
                'service' => $a['service'] ?? '',
            ];
        }

        // Enrich secrets with active/online info
        $enriched = [];
        foreach ($secrets as $s) {
            $username = $s['name'] ?? '';
            $enriched[] = [
                'username' => $username,
                'service'  => $s['service'] ?? 'pppoe',
                'profile'  => $s['profile'] ?? '',
                'comment'  => $s['comment'] ?? '',
                'disabled' => ($s['disabled'] ?? 'false') === 'true',
                'online'   => isset($activeMap[$username]),
                'ip'       => $activeMap[$username]['address'] ?? '',
                'uptime'   => $activeMap[$username]['uptime'] ?? '',
            ];
        }

        return response()->json([
            'success'     => true,
            'router_id'   => $router->id,
            'router_name' => $router->name,
            'secrets'     => $enriched,
        ]);
    }

    /**
     * Map a PPPoE username to a customer.
     */
    public function mapCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'pppoe_user'   => 'required|string|max:100',
            'ip_address'   => 'nullable|string|max:45',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $customer->update([
            'pppoe_user' => $validated['pppoe_user'],
            'ip_address' => $validated['ip_address'] ?? $customer->ip_address,
        ]);

        $customer->load('package:id,name');

        return response()->json([
            'success'  => true,
            'message'  => "PPPoE \"{$validated['pppoe_user']}\" berhasil dimapping ke pelanggan \"{$customer->name}\".",
            'customer' => $customer->toJsonData(),
        ]);
    }

    /**
     * Remove PPPoE mapping from a customer.
     */
    public function unmapCustomer(Customer $customer): JsonResponse
    {
        $oldUser = $customer->pppoe_user;
        $customer->update(['pppoe_user' => null]);

        return response()->json([
            'success' => true,
            'message' => "Mapping PPPoE \"{$oldUser}\" dari \"{$customer->name}\" berhasil dihapus.",
        ]);
    }

    /**
     * Bulk auto-map: match PPPoE usernames to customers by exact pppoe_user match.
     */
    public function autoMap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mappings'              => 'required|array|min:1',
            'mappings.*.customer_id' => 'required|exists:customers,id',
            'mappings.*.pppoe_user'  => 'required|string|max:100',
            'mappings.*.ip_address'  => 'nullable|string|max:45',
        ]);

        $count = 0;
        foreach ($validated['mappings'] as $m) {
            $customer = Customer::find($m['customer_id']);
            if ($customer) {
                $customer->update([
                    'pppoe_user' => $m['pppoe_user'],
                    'ip_address' => $m['ip_address'] ?? $customer->ip_address,
                ]);
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} pelanggan berhasil dimapping.",
            'count'   => $count,
        ]);
    }
}
