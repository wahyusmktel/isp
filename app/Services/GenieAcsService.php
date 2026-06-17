<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GenieAcsService
{
    public function isConfigured(): bool
    {
        return filled($this->nbiUrl());
    }

    public function updateWifi(string $deviceId, string $ssid, string $password): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'URL GenieACS NBI belum dikonfigurasi.',
            ];
        }

        $parameterValues = [
            [$this->ssidParameter(), $ssid],
            [$this->passwordParameter(), $password],
        ];

        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->withHeaders($this->headers())
                ->post($this->taskUrl($deviceId), [
                    'name' => 'setParameterValues',
                    'parameterValues' => $parameterValues,
                ]);
        } catch (ConnectionException $exception) {
            return [
                'success' => false,
                'message' => 'Tidak bisa terhubung ke GenieACS NBI.',
                'error' => $exception->getMessage(),
            ];
        }

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => 'GenieACS menolak task ubah WiFi.',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        return [
            'success' => true,
            'message' => 'Task ubah WiFi berhasil dikirim ke GenieACS.',
            'task' => $response->json(),
        ];
    }

    public function deviceInfo(string $deviceId, bool $refresh = false): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'URL GenieACS NBI belum dikonfigurasi.',
            ];
        }

        if ($refresh) {
            $this->refreshObjects($deviceId);
        }

        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->withHeaders($this->headers())
                ->get($this->nbiUrl().'/devices/', [
                    'query' => json_encode(['_id' => $deviceId]),
                ]);
        } catch (ConnectionException $exception) {
            return [
                'success' => false,
                'message' => 'Tidak bisa terhubung ke GenieACS NBI.',
                'error' => $exception->getMessage(),
            ];
        }

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data ONT dari GenieACS.',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $device = collect($response->json())->first();

        if (! $device) {
            return [
                'success' => false,
                'message' => 'Device tidak ditemukan di GenieACS.',
            ];
        }

        $parameters = $this->flattenParameters($device);
        $optical = $this->opticalInfo($parameters);

        return [
            'success' => true,
            'device' => [
                'id' => $device['_id'] ?? $deviceId,
                'serial_number' => $this->firstParameter($parameters, [
                    'DeviceID.SerialNumber',
                    'InternetGatewayDevice.DeviceInfo.SerialNumber',
                    'Device.DeviceInfo.SerialNumber',
                ]),
                'manufacturer' => $this->firstParameter($parameters, [
                    'DeviceID.Manufacturer',
                    'InternetGatewayDevice.DeviceInfo.Manufacturer',
                    'Device.DeviceInfo.Manufacturer',
                ]),
                'product_class' => $this->firstParameter($parameters, [
                    'DeviceID.ProductClass',
                    'InternetGatewayDevice.DeviceInfo.ProductClass',
                    'Device.DeviceInfo.ProductClass',
                ]),
                'wifi_ssid' => $this->firstParameter($parameters, [
                    $this->ssidParameter(),
                    'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID',
                    'Device.WiFi.SSID.1.SSID',
                ]),
                'ip' => $this->firstParameter($parameters, [
                    'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress',
                    'Device.IP.Interface.1.IPv4Address.1.IPAddress',
                    'VirtualParameters.IP',
                ]),
                'last_inform' => $this->firstParameter($parameters, [
                    '_lastInform',
                    'Events.Inform',
                ]),
            ],
            'optical' => $optical,
            'has_optical_data' => collect($optical)->filter(fn ($value) => filled($value))->isNotEmpty(),
        ];
    }

    private function taskUrl(string $deviceId): string
    {
        return $this->nbiUrl().'/devices/'.rawurlencode($deviceId).'/tasks?connection_request&timeout='.$this->taskTimeout();
    }

    private function refreshObjects(string $deviceId): void
    {
        $objectNames = [
            'InternetGatewayDevice.WANDevice.1',
            'InternetGatewayDevice.WANDevice.1.WANPONInterfaceConfig',
            'InternetGatewayDevice.WANDevice.1.X_ZTE-COM_WANPONInterfaceConfig',
            'InternetGatewayDevice.WANDevice.1.X_ZTE-COM_EPONInterfaceConfig',
            'InternetGatewayDevice.WANDevice.1.X_CT-COM_EponInterfaceConfig',
            'InternetGatewayDevice.WANDevice.1.X_CT-COM_GponInterfaceConfig',
            'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.2.X_CT-COM_WANEponLinkConfig',
        ];

        foreach ($objectNames as $objectName) {
            try {
                Http::timeout($this->timeout())
                    ->acceptJson()
                    ->withHeaders($this->headers())
                    ->post($this->taskUrl($deviceId), [
                        'name' => 'refreshObject',
                        'objectName' => $objectName,
                    ]);
            } catch (ConnectionException) {
                return;
            }
        }
    }

    private function opticalInfo(array $parameters): array
    {
        return [
            'rx_power' => $this->firstOpticalParameterByNeedles($parameters, [
                ['rxpower'],
                ['rx', 'power'],
                ['optical', 'input', 'power'],
                ['input', 'power'],
                ['receive', 'power'],
                ['pon', 'rx', 'power'],
            ]),
            'tx_power' => $this->firstOpticalParameterByNeedles($parameters, [
                ['txpower'],
                ['tx', 'power'],
                ['optical', 'output', 'power'],
                ['output', 'power'],
                ['transmit', 'power'],
                ['pon', 'tx', 'power'],
            ]),
            'supply_voltage' => $this->firstOpticalParameterByNeedles($parameters, [
                ['supplyvoltage'],
                ['supply', 'voltage'],
                ['optic', 'voltage'],
            ]),
            'bias_current' => $this->firstOpticalParameterByNeedles($parameters, [
                ['biascurrent'],
                ['bias', 'current'],
                ['transmitter', 'current'],
            ]),
            'temperature' => $this->firstOpticalParameterByNeedles($parameters, [
                ['temperature'],
                ['operating', 'temperature'],
                ['optic', 'temperature'],
                ['module', 'temperature'],
            ]),
            'pon_status' => $this->firstOpticalParameterByNeedles($parameters, [
                ['eponstate'],
                ['epon', 'state'],
                ['gpon', 'state'],
                ['pon', 'state'],
                ['registration', 'status'],
            ]),
            'fec_errors' => $this->firstParameterByNeedles($parameters, [
                ['fec', 'error'],
            ]),
            'loss_packets' => $this->firstParameterByNeedles($parameters, [
                ['loss', 'packet'],
            ]),
        ];
    }

    private function flattenParameters(array $data, string $prefix = ''): array
    {
        $parameters = [];

        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value) && array_key_exists('_value', $value)) {
                $parameters[$path] = $value['_value'];
            }

            if (is_array($value)) {
                $parameters += $this->flattenParameters(
                    collect($value)->except(['_value', '_timestamp', '_type', '_writable', '_object'])->toArray(),
                    $path
                );
            }
        }

        return $parameters;
    }

    private function firstParameter(array $parameters, array $paths): mixed
    {
        foreach ($paths as $path) {
            if (array_key_exists($path, $parameters)) {
                return $parameters[$path];
            }
        }

        return null;
    }

    private function firstOpticalParameterByNeedles(array $parameters, array $needleGroups): mixed
    {
        $opticalContext = ['optical', 'optic', 'wanpon', 'wanepon', 'pon', 'epon', 'gpon'];

        foreach ($needleGroups as $needles) {
            foreach ($parameters as $path => $value) {
                $normalizedPath = Str::lower(str_replace(['_', '-', '.'], ' ', $path));
                $compactPath = str_replace(' ', '', $normalizedPath);

                $hasOpticalContext = collect($opticalContext)->contains(
                    fn (string $context) => str_contains($normalizedPath, $context) || str_contains($compactPath, $context)
                );

                if (! $hasOpticalContext || str_contains($normalizedPath, 'wlan')) {
                    continue;
                }

                if (collect($needles)->every(fn (string $needle) => str_contains($normalizedPath, Str::lower($needle)) || str_contains($compactPath, Str::lower($needle)))) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function firstParameterByNeedles(array $parameters, array $needleGroups): mixed
    {
        foreach ($needleGroups as $needles) {
            foreach ($parameters as $path => $value) {
                $normalizedPath = Str::lower(str_replace(['_', '-', '.'], ' ', $path));

                if (collect($needles)->every(fn (string $needle) => str_contains($normalizedPath, Str::lower($needle)))) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function nbiUrl(): string
    {
        return rtrim((string) Setting::get('genieacs_nbi_url', ''), '/');
    }

    private function ssidParameter(): string
    {
        return (string) Setting::get(
            'genieacs_wifi_ssid_parameter',
            'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID'
        );
    }

    private function passwordParameter(): string
    {
        return (string) Setting::get(
            'genieacs_wifi_password_parameter',
            'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.KeyPassphrase'
        );
    }

    private function timeout(): int
    {
        return max(3, (int) Setting::get('genieacs_http_timeout', 15));
    }

    private function taskTimeout(): int
    {
        return max(1000, (int) Setting::get('genieacs_task_timeout', 5000));
    }

    private function headers(): array
    {
        $headers = ['Content-Type' => 'application/json'];
        $token = (string) Setting::get('genieacs_api_token', '');

        if (Str::of($token)->trim()->isNotEmpty()) {
            $headers['Authorization'] = 'Bearer '.trim($token);
        }

        return $headers;
    }
}
