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

    private function taskUrl(string $deviceId): string
    {
        return $this->nbiUrl().'/devices/'.rawurlencode($deviceId).'/tasks?connection_request&timeout='.$this->taskTimeout();
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
