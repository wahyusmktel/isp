<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Router;
use App\Services\GenieAcsService;
use App\Services\HisfocusOltService;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            'total' => $customers->count(),
            'aktif' => $customers->where('status', 'aktif')->count(),
            'suspend' => $customers->where('status', 'suspend')->count(),
            'terminate' => $customers->where('status', 'terminate')->count(),
        ];

        return view('customers.index', compact('customers', 'packages', 'stats'));
    }

    public function show(Customer $customer)
    {
        $customer->load('package');
        $routers = Router::orderBy('name')->get(['id', 'name', 'host', 'status']);
        $packages = Package::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get(['id', 'name', 'category', 'price']);
        $mappedPppoeUsers = Customer::query()
            ->whereNotNull('pppoe_user')
            ->where('pppoe_user', '!=', '')
            ->where('id', '!=', $customer->id)
            ->pluck('name', 'pppoe_user');

        return view('customers.show', compact('customer', 'routers', 'packages', 'mappedPppoeUsers'));
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->query('q');
        if (empty($q)) {
            return response()->json([]);
        }

        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('customer_number', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'name', 'customer_number', 'phone', 'email']);

        return response()->json($customers);
    }

    public function liveTraffic(Customer $customer, MikrotikService $mikrotik)
    {
        if (empty($customer->pppoe_user)) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan belum dimaping PPPoE akun nya.',
            ]);
        }

        $router = Router::where('status', 'online')->first();
        if (! $router) {
            $router = Router::first();
        }

        if (! $router) {
            return response()->json([
                'success' => false,
                'message' => 'Router belum dikonfigurasi.',
            ]);
        }

        $conn = $mikrotik->connect($router->host, $router->api_port, $router->username, $router->password);
        if (! $conn['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Mikrotik.',
            ]);
        }

        // Cari interface PPPoE (biasanya berawalan <pppoe-username>)
        $interface = '<pppoe-'.$customer->pppoe_user.'>';
        $traffic = $mikrotik->getInterfaceTraffic($interface);

        // Ambil penggunaan data dari interface
        $interfaces = $mikrotik->getInterfaces();
        $rxBytes = 0;
        $txBytes = 0;
        foreach ($interfaces as $iface) {
            if (isset($iface['name']) && $iface['name'] === $interface) {
                $rxBytes = (int) ($iface['rx-byte'] ?? 0);
                $txBytes = (int) ($iface['tx-byte'] ?? 0);
                break;
            }
        }

        // Ambil uptime dari sesi PPPoE aktif
        $actives = $mikrotik->getPppoeActives();
        $uptime = '';
        foreach ($actives as $a) {
            if (($a['name'] ?? '') === $customer->pppoe_user) {
                $uptime = $a['uptime'] ?? '';
                break;
            }
        }

        // Konversi ke GB (RX mikrotik = Upload pelanggan, TX mikrotik = Download pelanggan)
        $usageData = [
            'download_gb' => round($txBytes / 1073741824, 2),
            'upload_gb' => round($rxBytes / 1073741824, 2),
        ];

        $mikrotik->close();

        if (empty($traffic) || ! isset($traffic['rx-bits-per-second'])) {
            return response()->json([
                'success' => true,
                'status' => 'offline',
                'rx' => 0,
                'tx' => 0,
                'uptime' => '',
                'usage' => $usageData,
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'online',
            'rx' => round((int) $traffic['tx-bits-per-second'] / 1000000, 2),
            'tx' => round((int) $traffic['rx-bits-per-second'] / 1000000, 2),
            'uptime' => $uptime,
            'usage' => $usageData,
        ]);
    }

    public function ontAdminProxy(Request $request, Customer $customer, string $path = '')
    {
        if (blank($customer->pppoe_user) || blank($customer->ip_address)) {
            abort(404, 'Pelanggan belum memiliki mapping PPPoE dan IP Address.');
        }

        if (str_starts_with(ltrim($path, '/'), 'cdn-cgi/')) {
            return response('', 204);
        }

        $targetUrl = 'http://'.$customer->ip_address.':80/'.ltrim($path, '/');
        if ($request->getQueryString()) {
            $targetUrl .= '?'.$request->getQueryString();
        }
        $targetOrigin = 'http://'.$customer->ip_address;

        $headers = collect($request->headers->all())
            ->mapWithKeys(fn ($values, $key) => [$key => implode(', ', $values)])
            ->except([
                'host',
                'connection',
                'content-length',
                'content-encoding',
                'x-csrf-token',
                'x-xsrf-token',
                'sec-fetch-site',
                'sec-fetch-mode',
                'sec-fetch-dest',
                'sec-fetch-user',
            ])
            ->all();
        $headers['referer'] = $targetOrigin.'/'.ltrim($path, '/');
        $headers['origin'] = $targetOrigin;

        if ($request->headers->has('cookie')) {
            $headers['cookie'] = collect(explode(';', $request->headers->get('cookie')))
                ->map(fn ($cookie) => trim($cookie))
                ->reject(fn ($cookie) => str_starts_with($cookie, 'laravel_session=') || str_starts_with($cookie, 'XSRF-TOKEN='))
                ->implode('; ');
        }

        try {
            $upstream = Http::timeout(15)
                ->withHeaders($headers)
                ->withOptions(['allow_redirects' => false])
                ->send($request->method(), $targetUrl, [
                    'body' => $request->getContent(),
                ]);
        } catch (\Throwable $e) {
            return response(
                '<div style="font-family:Arial,sans-serif;padding:24px;color:#111827">'.
                '<h2 style="margin:0 0 8px">Admin ONT tidak dapat diakses</h2>'.
                '<p style="margin:0 0 12px;color:#4b5563">Server aplikasi belum bisa terhubung ke <code>http://'.e($customer->ip_address).':80</code>.</p>'.
                '<pre style="white-space:pre-wrap;background:#f3f4f6;padding:12px;border-radius:8px">'.e($e->getMessage()).'</pre>'.
                '</div>',
                502,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        $contentType = $upstream->header('Content-Type', 'text/html; charset=UTF-8');
        $body = $upstream->body();

        if (str_contains(strtolower($contentType), 'text/html')) {
            $body = $this->rewriteOntAdminHtml($body, $customer, trim($path, '/'));
        } elseif (str_contains(strtolower($contentType), 'text/css')) {
            $body = $this->rewriteOntAdminCss($body, $customer, trim($path, '/'));
        } elseif ($this->isOntAdminScriptResponse($contentType, $path)) {
            $body = $this->rewriteOntAdminTextUrls($body, $customer, trim($path, '/'));
        }

        $responseHeaders = ['Content-Type' => $contentType];
        if ($upstream->header('Location')) {
            $responseHeaders['Location'] = $this->rewriteOntAdminUrl($upstream->header('Location'), $customer, trim($path, '/'));
        }
        if ($upstream->header('Set-Cookie')) {
            $responseHeaders['Set-Cookie'] = $upstream->header('Set-Cookie');
        }

        return response($body, $upstream->status(), $responseHeaders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['customer_number'] = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $customer = Customer::create($validated);
        $customer->load('package:id,name,category');

        return response()->json([
            'success' => true,
            'message' => "Pelanggan \"{$customer->name}\" berhasil ditambahkan.",
            'customer' => $customer->toJsonData(),
        ]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $customer->update($validated);
        $customer->load('package:id,name,category');

        return response()->json([
            'success' => true,
            'message' => "Data \"{$customer->name}\" berhasil diperbarui.",
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
            'status' => $customer->status,
        ]);
    }

    public function updateWifi(Request $request, Customer $customer, GenieAcsService $genieAcs): JsonResponse
    {
        $validated = $request->validate([
            'ssid' => 'required|string|min:1|max:32',
            'password' => 'required|string|min:8|max:63',
        ]);

        if (blank($customer->acs_device_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan belum memiliki ACS Device ID.',
            ], 422);
        }

        $result = $genieAcs->updateWifi(
            $customer->acs_device_id,
            $validated['ssid'],
            $validated['password']
        );

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        $customer->update(['wifi_ssid' => $validated['ssid']]);

        return response()->json([
            'success' => true,
            'message' => 'Perintah ubah WiFi berhasil dikirim ke ONT pelanggan.',
            'wifi_ssid' => $customer->wifi_ssid,
        ]);
    }

    public function updateAcsDevice(Request $request, Customer $customer, GenieAcsService $genieAcs): JsonResponse
    {
        $validated = $request->validate([
            'acs_device_id' => 'required|string|max:255',
        ]);

        $result = $genieAcs->deviceInfo($validated['acs_device_id'], true);

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Device tidak ditemukan di GenieACS.',
                'details' => $result,
            ], 422);
        }

        $device = $result['device'] ?? [];
        $customer->update([
            'acs_device_id' => $validated['acs_device_id'],
            'ont_serial_number' => $device['serial_number'] ?? $customer->ont_serial_number,
            'wifi_ssid' => $device['wifi_ssid'] ?? $customer->wifi_ssid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ACS Device ID berhasil disimpan dan data ONT berhasil dibaca dari GenieACS.',
            'customer' => $customer->fresh('package')->toJsonData(),
            'device' => $device,
            'optical' => $result['optical'] ?? [],
            'has_optical_data' => $result['has_optical_data'] ?? false,
        ]);
    }

    public function ontInfo(Request $request, Customer $customer, GenieAcsService $genieAcs, HisfocusOltService $hisfocusOlt): JsonResponse
    {
        $oltResult = $hisfocusOlt->customerOpticalInfo($customer);

        if (($oltResult['success'] ?? false) && ($oltResult['has_optical_data'] ?? false)) {
            if (filled($customer->acs_device_id)) {
                $genieResult = $genieAcs->deviceInfo($customer->acs_device_id, false);
                if (($genieResult['success'] ?? false) && isset($genieResult['device'])) {
                    $oltResult['device'] = array_merge($oltResult['device'] ?? [], $genieResult['device']);
                    if (filled($genieResult['device']['wifi_ssid'] ?? null) && $customer->wifi_ssid !== $genieResult['device']['wifi_ssid']) {
                        $customer->update(['wifi_ssid' => $genieResult['device']['wifi_ssid']]);
                    }
                }
            }

            return response()->json($oltResult);
        }

        if (blank($customer->acs_device_id)) {
            return response()->json([
                'success' => false,
                'message' => $oltResult['message'] ?? 'Pelanggan belum memiliki ACS Device ID.',
            ]);
        }

        $result = $genieAcs->deviceInfo($customer->acs_device_id, $request->boolean('refresh'));
        $result['fallback_message'] = $oltResult['message'] ?? null;

        return response()->json($result);
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
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '08'.fake()->numerify('##########'),
                'address' => fake()->address(),
                'package_id' => $packages[array_rand($packages)],
                'ip_address' => fake()->ipv4(),
                'pppoe_user' => fake()->userName(),
                'status' => 'aktif',
                'join_date' => now()->subDays(rand(0, 30))->format('Y-m-d'),
                'billing_date' => rand(1, 28),
                'customer_number' => str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            ]);
            $customer->load('package:id,name,category');
            $generated[] = $customer->toJsonData();
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil generate {$count} data pelanggan dummy.",
            'customers' => $generated,
        ]);
    }

    private function rewriteOntAdminHtml(string $body, Customer $customer, string $currentPath): string
    {
        $body = $this->rewriteOntAdminTextUrls($body, $customer, $currentPath);

        $body = preg_replace_callback(
            '/\b(href|src|action)=([\'"])(?!https?:|\/\/|#|javascript:|data:|mailto:|tel:)([^\'"]*)\2/i',
            function (array $matches) use ($customer, $currentPath) {
                $url = trim($matches[3]);
                if ($url === '') {
                    return $matches[0];
                }

                return $matches[1].'='.$matches[2].$this->rewriteOntAdminUrl($url, $customer, $currentPath).$matches[2];
            },
            $body
        ) ?? $body;

        return $this->injectOntAdminProxyShim($body, $customer);
    }

    private function rewriteOntAdminCss(string $body, Customer $customer, string $currentPath): string
    {
        $body = $this->rewriteOntAdminTextUrls($body, $customer, $currentPath);

        return preg_replace_callback(
            '/url\(([\'"]?)(?!https?:|\/\/|data:)([^)\'"]+)\1\)/i',
            function (array $matches) use ($customer, $currentPath) {
                $url = trim($matches[2]);
                if ($url === '') {
                    return $matches[0];
                }

                return 'url('.$matches[1].$this->rewriteOntAdminUrl($url, $customer, $currentPath).$matches[1].')';
            },
            $body
        ) ?? $body;
    }

    private function rewriteOntAdminTextUrls(string $body, Customer $customer, string $currentPath): string
    {
        $body = preg_replace_callback(
            '#http://(?:\d{1,3}\.){3}\d{1,3}(?::80)?(/[^\s\'")<>]*)?#i',
            function (array $matches) use ($customer, $currentPath) {
                return $this->rewriteOntAdminUrl($matches[0], $customer, $currentPath);
            },
            $body
        ) ?? $body;

        return preg_replace_callback(
            '#([\'"])((?:\.\./)+(?:img|css|js|images|style|help|login|cgi-bin|web|common|template|lang|res|user|admin|status|network|wlan|security|management|dev_info)[^\'"]*)\1#i',
            function (array $matches) use ($customer) {
                return $matches[1].$this->ontAdminProxyBase($customer).'/'.ltrim(preg_replace('#^(?:\.\./)+#', '', $matches[2]), '/').$matches[1];
            },
            $body
        ) ?? $body;
    }

    private function rewriteOntAdminUrl(string $url, Customer $customer, string $currentPath = ''): string
    {
        $proxyBase = $this->ontAdminProxyBase($customer);
        $url = trim($url);

        if ($url === '' || str_starts_with($url, '#') || preg_match('/^(https?:|\/\/|javascript:|data:|mailto:|tel:)/i', $url)) {
            if (preg_match('#^http://((?:\d{1,3}\.){3}\d{1,3})(?::80)?(/.*)?$#i', $url, $matches)) {
                $host = $matches[1];
                if ($host === $customer->ip_address || $this->isPrivateIpv4($host)) {
                    return $proxyBase.($matches[2] ?? '/');
                }
            }

            return $url;
        }

        if (str_starts_with($url, '/')) {
            if (str_starts_with($url, '/cdn-cgi/')) {
                return $url;
            }

            return $proxyBase.$url;
        }

        if (str_starts_with($url, '?')) {
            return $proxyBase.'/'.ltrim($currentPath, '/').$url;
        }

        $normalized = $this->normalizeOntAdminRelativePath($url, $currentPath);
        return $proxyBase.'/'.$normalized;
    }

    private function ontAdminProxyBase(Customer $customer): string
    {
        return "/customers/{$customer->id}/ont-admin-proxy";
    }

    private function injectOntAdminProxyShim(string $body, Customer $customer): string
    {
        $shim = '<script>(function(){'."\n".
            'var PROXY_BASE='.json_encode($this->ontAdminProxyBase($customer)).';'."\n".
            'function proxify(url){'."\n".
            ' if(!url || typeof url!=="string") return url;'."\n".
            ' if(url.indexOf(PROXY_BASE)===0 || /^(data:|mailto:|tel:|javascript:|#)/i.test(url)) return url;'."\n".
            ' if(url.indexOf("/cdn-cgi/")===0) return url;'."\n".
            ' var m=url.match(/^http:\/\/(?:\d{1,3}\.){3}\d{1,3}(?::80)?(\/.*)?$/i);'."\n".
            ' if(m) return PROXY_BASE+(m[1]||"/");'."\n".
            ' if(url.charAt(0)==="/") return PROXY_BASE+url;'."\n".
            ' if(url.charAt(0)==="?") return window.location.pathname+url;'."\n".
            ' if(url.indexOf("../")===0) return PROXY_BASE+"/"+url.replace(/^(\\.\\.\\/)+/,"");'."\n".
            ' return url;'."\n".
            '}'."\n".
            'try{var xo=XMLHttpRequest.prototype.open;XMLHttpRequest.prototype.open=function(method,url){arguments[1]=proxify(url);return xo.apply(this,arguments);};}catch(e){}'."\n".
            'try{var ff=window.fetch; if(ff){window.fetch=function(input,init){if(typeof input==="string"){input=proxify(input);}else if(input&&input.url){input=new Request(proxify(input.url),input);}return ff.call(this,input,init);};}}catch(e){}'."\n".
            'document.addEventListener("click",function(ev){'."\n".
            ' var el=ev.target && ev.target.closest ? ev.target.closest("[onclick*=openLink]") : null;'."\n".
            ' if(!el) return;'."\n".
            ' var onclick=el.getAttribute("onclick")||"";'."\n".
            ' var match=onclick.match(/openLink\((?:\s*[\'\"])(.*?)(?:[\'\"])/);'."\n".
            ' if(match&&match[1]){ev.preventDefault();ev.stopImmediatePropagation();window.location.href=proxify(match[1]);}'."\n".
            '},true);'."\n".
            'window.__ontAdminProxify=proxify;'."\n".
            '})();</script>';

        if (stripos($body, '</head>') !== false) {
            return preg_replace('/<\/head>/i', $shim.'</head>', $body, 1) ?? $body;
        }

        if (stripos($body, '</body>') !== false) {
            return preg_replace('/<\/body>/i', $shim.'</body>', $body, 1) ?? $body;
        }

        return $shim.$body;
    }

    private function isOntAdminScriptResponse(string $contentType, string $path): bool
    {
        $contentType = strtolower($contentType);
        $path = strtolower($path);

        return str_contains($contentType, 'javascript')
            || str_contains($contentType, 'ecmascript')
            || str_ends_with($path, '.js')
            || str_ends_with($path, '.gch');
    }

    private function isPrivateIpv4(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    private function normalizeOntAdminRelativePath(string $url, string $currentPath): string
    {
        $url = str_replace('\\', '/', trim($url));
        if (str_starts_with($url, '../')) {
            return ltrim(preg_replace('#^(?:\.\./)+#', '', $url), '/');
        }

        $directory = trim(str_replace('\\', '/', dirname($currentPath)), './');
        $path = ($directory !== '' ? $directory.'/' : '').ltrim($url, '/');
        $parts = [];

        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'package_id' => 'required|exists:packages,id',
            'ip_address' => 'nullable|string|max:45',
            'pppoe_user' => 'nullable|string|max:100',
            'onu_id' => 'nullable|string|max:100',
            'acs_device_id' => 'nullable|string|max:255',
            'ont_serial_number' => 'nullable|string|max:100',
            'wifi_ssid' => 'nullable|string|max:32',
            'status' => 'required|in:aktif,suspend,terminate',
            'join_date' => 'required|date',
            'billing_date' => 'nullable|integer|min:1|max:31',
            'notes' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }
}
