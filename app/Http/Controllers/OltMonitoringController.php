<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\HisfocusOltService;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class OltMonitoringController extends Controller
{
    public function index(HisfocusOltService $olt)
    {
        return view('network.olt-monitoring', $this->monitoringData($olt));
    }

    public function exportExcel(HisfocusOltService $olt): Response
    {
        $html = view('network.olt-monitoring-excel', $this->monitoringData($olt))->render();
        $filename = 'Monitoring-OLT-'.now()->format('Ymd-His').'.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate',
        ]);
    }

    private function monitoringData(HisfocusOltService $olt): array
    {
        $result = $olt->allClients();
        $customers = Customer::query()
            ->where(function ($query) {
                $query->whereNotNull('onu_id')
                    ->orWhereNotNull('mac_ont');
            })
            ->get(['id', 'name', 'customer_number', 'phone', 'onu_id', 'mac_ont', 'pppoe_user', 'ip_address']);

        $clients = collect($result['clients'] ?? [])
            ->map(function (array $client) use ($customers) {
                $matched = $this->matchCustomer($client, $customers);
                $rxCategory = $this->rxCategory($client['rx_power'] ?? null);
                $client['_customer'] = $matched ? [
                    'id' => $matched->id,
                    'name' => $matched->name,
                    'customer_number' => $matched->customer_number,
                    'phone' => $matched->phone,
                    'pppoe_user' => $matched->pppoe_user,
                    'ip_address' => $matched->ip_address,
                ] : null;
                $client['_is_online'] = $this->looksOnline($client['status'] ?? null);
                $client['_rx_category'] = $rxCategory['key'];
                $client['_rx_label'] = $rxCategory['label'];
                $client['_rx_value'] = $this->rxValue($client['rx_power'] ?? null);
                $client['_port_label'] = $this->portLabel($client);

                return $client;
            })
            ->values();

        $stats = [
            'total' => $clients->count(),
            'online' => $clients->filter(fn ($row) => $this->looksOnline($row['status'] ?? null))->count(),
            'offline' => $clients->filter(fn ($row) => ! $this->looksOnline($row['status'] ?? null))->count(),
            'mapped' => $clients->whereNotNull('_customer')->count(),
            'rx_warning' => $clients->filter(fn ($row) => $this->badRx($row['rx_power'] ?? null))->count(),
            'rx_excellent' => $clients->where('_rx_category', 'excellent')->count(),
            'rx_good' => $clients->where('_rx_category', 'good')->count(),
            'rx_critical' => $clients->where('_rx_category', 'critical')->count(),
        ];

        $portGroups = $clients
            ->groupBy('_port_label')
            ->map(function ($items, string $label) {
                return [
                    'label' => $label,
                    'total' => $items->count(),
                    'online' => $items->where('_is_online', true)->count(),
                    'excellent' => $items->where('_rx_category', 'excellent')->count(),
                    'good' => $items->where('_rx_category', 'good')->count(),
                    'critical' => $items->where('_rx_category', 'critical')->count(),
                    'unknown' => $items->where('_rx_category', 'unknown')->count(),
                    'avg_rx' => round($items->pluck('_rx_value')->filter(fn ($value) => $value !== null)->avg() ?? 0, 2),
                    'clients' => $items->values()->all(),
                ];
            })
            ->sortKeys()
            ->values();

        return [
            'result' => $result,
            'clients' => $clients,
            'stats' => $stats,
            'portGroups' => $portGroups,
        ];
    }

    private function matchCustomer(array $client, $customers): ?Customer
    {
        $onuId = $this->text($client['id'] ?? $client['onu_id'] ?? $client['onuid'] ?? null);
        $mac = $this->mac($client['mac_address'] ?? $client['macaddress'] ?? null);

        return $customers->first(function (Customer $customer) use ($onuId, $mac) {
            if ($onuId !== '' && $this->text($customer->onu_id) === $onuId) {
                return true;
            }

            $customerMac = $this->mac($customer->mac_ont);
            return $mac !== '' && $customerMac !== ''
                && ($mac === $customerMac || substr($mac, 0, 10) === substr($customerMac, 0, 10));
        });
    }

    private function looksOnline(?string $status): bool
    {
        $status = Str::lower(trim((string) $status));

        return $status !== ''
            && ! Str::contains($status, ['offline', 'down', 'los', 'timeout', 'dying', 'disable']);
    }

    private function badRx(?string $rx): bool
    {
        $value = $this->rxValue($rx);
        if ($value === null) return false;

        return $value < -25 || $value > -8;
    }

    private function rxCategory(?string $rx): array
    {
        $value = $this->rxValue($rx);
        if ($value === null) {
            return ['key' => 'unknown', 'label' => 'Tidak terbaca'];
        }

        if ($value >= -22 && $value <= -15) {
            return ['key' => 'excellent', 'label' => 'Excellent'];
        }

        if ($value < -22 && $value >= -25) {
            return ['key' => 'good', 'label' => 'Good'];
        }

        if ($value < -25) {
            return ['key' => 'critical', 'label' => 'Critical'];
        }

        return ['key' => 'warning', 'label' => 'Perlu Cek'];
    }

    private function rxValue(?string $rx): ?float
    {
        if (! preg_match('/-?\d+(?:\.\d+)?/', (string) $rx, $match)) {
            return null;
        }

        return (float) $match[0];
    }

    private function portLabel(array $client): string
    {
        $id = trim((string) ($client['id'] ?? $client['onu_id'] ?? $client['onuid'] ?? ''));
        if (preg_match('/^(\d+)[\/:_-](\d+)/', $id, $match)) {
            return "PON {$match[1]}/{$match[2]}";
        }

        if (preg_match('/pon\s*([0-9\/:_-]+)/i', (string) ($client['name'] ?? ''), $match)) {
            return 'PON '.str_replace(['_', ':', '-'], '/', $match[1]);
        }

        if ($id !== '') {
            return 'Port '.$id;
        }

        return 'Port Tidak Terbaca';
    }

    private function text(?string $value): string
    {
        return Str::lower(trim((string) $value));
    }

    private function mac(?string $value): string
    {
        return Str::lower(preg_replace('/[^a-fA-F0-9]/', '', (string) $value));
    }
}
