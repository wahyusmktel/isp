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

    public function deviceInfo(string $deviceId): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'URL GenieACS NBI belum dikonfigurasi.',
            ];
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

        return [
            'success' => true,
            'device' => [
                'id' => $device['_id'] ?? $deviceId,
                'serial_number' => $this->firstParameter($parameters, [
                    'DeviceID.SerialNumber',
                    'InternetGatewayDevice.DeviceInfo.SerialNumber',
                    'Device.DeviceInfo.SerialNumber',
                ]),
                'product_class' => $this->firstParameter($parameters, [
                    'DeviceID.ProductClass',
                    'InternetGatewayDevice.DeviceInfo.ProductClass',
                    'Device.DeviceInfo.ProductClass',
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
            'optical' => $this->opticalInfo($parameters),
        ];
    }

    private function taskUrl(string $deviceId): string
    {
        return $this->nbiUrl().'/devices/'.rawurlencode($deviceId).'/tasks?connection_request&timeout='.$this->taskTimeout();
    }

    private function opticalInfo(array $parameters): array
    {
        return [
            'rx_power' => $this->firstParameterByNeedles($parameters, [
                ['optical', 'input', 'power'],
                ['receive', 'power'],
                ['rx', 'power'],
                ['pon', 'rx', 'power'],
            ]),
            'tx_power' => $this->firstParameterByNeedles($parameters, [
                ['optical', 'output', 'power'],
                ['transmit', 'power'],
                ['tx', 'power'],
                ['pon', 'tx', 'power'],
            ]),
            'supply_voltage' => $this->firstParameterByNeedles($parameters, [
                ['supply', 'voltage'],
                ['optic', 'voltage'],
            ]),
            'bias_current' => $this->firstParameterByNeedles($parameters, [
                ['bias', 'current'],
                ['transmitter', 'current'],
            ]),
            'temperature' => $this->firstParameterByNeedles($parameters, [
                ['operating', 'temperature'],
                ['optic', 'temperature'],
                ['module', 'temperature'],
            ]),
            'pon_status' => $this->firstParameterByNeedles($parameters, [
                ['epon', 'state'],
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
