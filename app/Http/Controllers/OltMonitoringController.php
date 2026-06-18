<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\HisfocusOltService;
use Illuminate\Support\Str;

class OltMonitoringController extends Controller
{
    public function index(HisfocusOltService $olt)
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
                $client['_customer'] = $matched ? [
                    'id' => $matched->id,
                    'name' => $matched->name,
                    'customer_number' => $matched->customer_number,
                    'phone' => $matched->phone,
                    'pppoe_user' => $matched->pppoe_user,
                    'ip_address' => $matched->ip_address,
                ] : null;

                return $client;
            })
            ->values();

        $stats = [
            'total' => $clients->count(),
            'online' => $clients->filter(fn ($row) => $this->looksOnline($row['status'] ?? null))->count(),
            'offline' => $clients->filter(fn ($row) => ! $this->looksOnline($row['status'] ?? null))->count(),
            'mapped' => $clients->whereNotNull('_customer')->count(),
            'rx_warning' => $clients->filter(fn ($row) => $this->badRx($row['rx_power'] ?? null))->count(),
        ];

        return view('network.olt-monitoring', [
            'result' => $result,
            'clients' => $clients,
            'stats' => $stats,
        ]);
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
        if (! preg_match('/-?\d+(?:\.\d+)?/', (string) $rx, $match)) {
            return false;
        }

        $value = (float) $match[0];
        return $value < -27 || $value > -8;
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
