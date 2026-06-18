<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Setting;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HisfocusOltService
{
    public function allClients(): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi OLT HisFocus belum lengkap.',
                'clients' => [],
            ];
        }

        $session = Http::timeout($this->timeout())
            ->accept('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8')
            ->withOptions(['cookies' => new CookieJar]);

        if (filled($this->username())) {
            $session->withBasicAuth($this->username(), $this->password());
        }

        $this->login($session);

        $clients = [];
        $sources = [];
        $errors = [];

        foreach ($this->onuListUrls() as $url) {
            try {
                $response = $session->get($this->absoluteUrl($url));
            } catch (ConnectionException $exception) {
                $errors[] = "Tidak bisa terhubung ke {$url}: {$exception->getMessage()}";
                continue;
            }

            if (! $response->successful()) {
                $errors[] = "{$url}: HTTP {$response->status()}";
                continue;
            }

            $rows = $this->parseRows($response->body());
            foreach ($rows as $row) {
                $row['_source_url'] = $url;
                $clients[] = $row;
            }
            $sources[] = [
                'url' => $url,
                'count' => count($rows),
            ];
        }

        $clients = collect($clients)
            ->unique(fn (array $row) => ($this->value($row, ['id', 'onu_id', 'onuid']) ?: '').'|'.($this->value($row, ['mac_address', 'macaddress']) ?: ''))
            ->values()
            ->all();

        return [
            'success' => count($clients) > 0 || empty($errors),
            'message' => count($clients) > 0 ? 'Data client OLT berhasil dibaca.' : 'Data client OLT tidak ditemukan.',
            'clients' => $clients,
            'sources' => $sources,
            'errors' => $errors,
            'base_url' => $this->baseUrl(),
        ];
    }

    public function customerOpticalInfo(Customer $customer): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi OLT HisFocus belum lengkap.',
            ];
        }

        $session = Http::timeout($this->timeout())
            ->accept('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8')
            ->withOptions(['cookies' => new CookieJar]);

        if (filled($this->username())) {
            $session->withBasicAuth($this->username(), $this->password());
        }

        $this->login($session);

        foreach ($this->onuListUrls() as $url) {
            try {
                $response = $session->get($this->absoluteUrl($url));
            } catch (ConnectionException $exception) {
                return [
                    'success' => false,
                    'message' => 'Tidak bisa terhubung ke OLT HisFocus.',
                    'error' => $exception->getMessage(),
                ];
            }

            if (! $response->successful()) {
                continue;
            }

            $match = $this->findCustomerRow($response->body(), $customer);

            if ($match) {
                return [
                    'success' => true,
                    'source' => 'olt',
                    'device' => [
                        'id' => $this->value($match, ['id', 'onu_id', 'onuid']),
                        'serial_number' => $customer->ont_serial_number,
                        'product_class' => null,
                        'ip' => null,
                        'last_inform' => now()->toIso8601String(),
                    ],
                    'optical' => [
                        'rx_power' => $this->value($match, ['rx_power', 'rxpower']),
                        'tx_power' => $this->value($match, ['tx_power', 'txpower']),
                        'temperature' => $this->value($match, ['temperature']),
                        'distance' => $this->value($match, ['distance']),
                        'pon_status' => $this->value($match, ['status']),
                        'supply_voltage' => null,
                        'bias_current' => null,
                        'fec_errors' => null,
                        'loss_packets' => null,
                    ],
                    'has_optical_data' => true,
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Data ONU pelanggan tidak ditemukan di tabel OLT HisFocus.',
            'lookup' => [
                'onu_id' => $customer->onu_id,
                'mac_ont' => $customer->mac_ont,
            ],
        ];
    }

    private function isConfigured(): bool
    {
        return filled($this->baseUrl()) && count($this->onuListUrls()) > 0;
    }

    private function login(PendingRequest $session): void
    {
        $path = (string) Setting::get('hisfocus_olt_login_path', '');
        $username = (string) Setting::get('hisfocus_olt_username', '');
        $password = (string) Setting::get('hisfocus_olt_password', '');

        if (blank($path) || blank($username)) {
            return;
        }

        try {
            $session->post($this->absoluteUrl($path), [
                'username' => $username,
                'user' => $username,
                'name' => $username,
                'password' => $password,
                'pass' => $password,
                'pwd' => $password,
            ]);
        } catch (ConnectionException) {
            return;
        }
    }

    private function findCustomerRow(string $html, Customer $customer): ?array
    {
        foreach ($this->parseRows($html) as $row) {
            $onuId = $this->value($row, ['id', 'onu_id', 'onuid']);
            $mac = $this->value($row, ['mac_address', 'macaddress']);

            if (filled($customer->onu_id) && $this->sameText($customer->onu_id, $onuId)) {
                return $row;
            }

            if (filled($customer->mac_ont) && $this->sameMacFamily($customer->mac_ont, $mac)) {
                return $row;
            }
        }

        return null;
    }

    private function parseRows(string $html): array
    {
        return array_merge($this->parseJavascriptOnuTable($html), $this->parseTables($html));
    }

    private function parseJavascriptOnuTable(string $html): array
    {
        if (! preg_match('/var\s+onutable\s*=\s*new\s+Array\s*\((.*?)\)\s*;/is', $html, $match)) {
            return [];
        }

        $values = str_getcsv($match[1], ',', "'");
        $values = array_map(fn (string $value) => trim($value), $values);
        $rows = [];

        foreach (array_chunk($values, 18) as $chunk) {
            if (count($chunk) < 18 || blank($chunk[0])) {
                continue;
            }

            $rows[] = [
                'id' => $chunk[0],
                'name' => $chunk[1],
                'mac_address' => $chunk[2],
                'status' => $chunk[3],
                'version' => $chunk[4],
                'chip_id' => $chunk[5],
                'ports' => $chunk[6],
                'temperature' => $this->cleanMetric($chunk[7]),
                'tx_power' => $this->cleanMetric($chunk[10]),
                'rx_power' => $this->cleanMetric($chunk[11]),
                'running_time_seconds' => $chunk[12],
                'deregister_count' => $chunk[13],
                'offline_reason' => ((string) $chunk[14] === '1') ? 'Dying_gasp' : 'Timeout',
                'rtt' => $chunk[15],
                'register_time' => $chunk[16],
                'deregister_time' => $chunk[17],
            ];
        }

        return $rows;
    }

    private function parseTables(string $html): array
    {
        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);
        $rows = [];

        foreach ($xpath->query('//table//tr') as $tr) {
            $cells = [];
            foreach ($xpath->query('./th|./td', $tr) as $cell) {
                $cells[] = trim(preg_replace('/\s+/', ' ', $cell->textContent));
            }

            if (count($cells) < 2) {
                continue;
            }

            if ($this->looksLikeHeader($cells)) {
                $headers = array_map(fn (string $cell) => $this->key($cell), $cells);

                continue;
            }

            if (empty($headers ?? []) || count($cells) !== count($headers)) {
                continue;
            }

            $row = array_combine($headers, $cells);

            if ($row && ($this->value($row, ['id', 'onuid', 'onu_id']) || $this->value($row, ['macaddress', 'mac_address']))) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function cleanMetric(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || $value === '--' || $value === '-inf' ? null : $value;
    }

    private function looksLikeHeader(array $cells): bool
    {
        $keys = array_map(fn (string $cell) => $this->key($cell), $cells);

        return in_array('id', $keys, true) || in_array('onuid', $keys, true) || in_array('macaddress', $keys, true);
    }

    private function key(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->replace([' ', '-', '/', '(', ')'], '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->toString();
    }

    private function value(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return null;
    }

    private function sameText(?string $first, ?string $second): bool
    {
        return filled($first) && filled($second) && Str::lower(trim($first)) === Str::lower(trim($second));
    }

    private function sameMacFamily(?string $first, ?string $second): bool
    {
        $normalize = fn (?string $mac) => Str::lower(preg_replace('/[^a-fA-F0-9]/', '', (string) $mac));
        $first = $normalize($first);
        $second = $normalize($second);

        if (blank($first) || blank($second)) {
            return false;
        }

        return $first === $second || substr($first, 0, 10) === substr($second, 0, 10);
    }

    private function absoluteUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        return $this->baseUrl().'/'.ltrim($url, '/');
    }

    private function baseUrl(): string
    {
        return rtrim((string) Setting::get('hisfocus_olt_base_url', ''), '/');
    }

    private function onuListUrls(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) Setting::get('hisfocus_olt_onu_list_urls', '/onuAllPonOnuList.asp')))
            ->map(fn (string $url) => trim($url))
            ->filter()
            ->values()
            ->all();
    }

    private function username(): string
    {
        return (string) Setting::get('hisfocus_olt_username', '');
    }

    private function password(): string
    {
        return (string) Setting::get('hisfocus_olt_password', '');
    }

    private function timeout(): int
    {
        return max(3, (int) Setting::get('hisfocus_olt_timeout', 15));
    }
}
