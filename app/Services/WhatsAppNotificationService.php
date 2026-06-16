<?php

namespace App\Services;

use App\Models\CustomerActivity;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public function notifyPppoeConnected(CustomerActivity $activity): void
    {
        if (Setting::get('whatsapp_pppoe_connected_enabled', '1') !== '1') {
            return;
        }

        $this->sendPppoeActivityNotification($activity, 'PPPoE Terhubung', 'Terhubung');
    }

    public function notifyPppoeDisconnected(CustomerActivity $activity): void
    {
        if (Setting::get('whatsapp_pppoe_disconnected_enabled', '1') !== '1') {
            return;
        }

        $this->sendPppoeActivityNotification($activity, 'PPPoE Terputus', 'Terputus');
    }

    private function sendPppoeActivityNotification(CustomerActivity $activity, string $title, string $statusLabel): void
    {
        $groupId = Setting::get('whatsapp_notification_group_id', '');
        if (! $groupId) {
            Log::info('WhatsApp PPPoE notification skipped: group is not configured.');
            return;
        }

        $activity->loadMissing('customer.package');
        $customer = $activity->customer;
        $customerName = $customer?->name ?? 'Tidak terdaftar';
        $phone = $customer?->phone ?? '-';
        $package = $customer?->package?->name ?? '-';
        $address = $customer?->address ?? '-';

        $message = implode("\n", [
            '*' . $title . '*',
            '',
            'Status: ' . $statusLabel,
            'Pelanggan: ' . $customerName,
            'PPPoE: ' . ($activity->pppoe_user ?? '-'),
            'Paket: ' . $package,
            'Telepon: ' . $phone,
            'IP terakhir: ' . ($activity->ip_address ?? '-'),
            'Alamat: ' . $address,
            'Waktu: ' . $activity->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s'),
            '',
            'Keterangan: ' . ($activity->description ?: '-'),
        ]);

        try {
            $response = Http::timeout((int) config('services.whatsapp.timeout', 10))
                ->acceptJson()
                ->post(rtrim((string) config('services.whatsapp.bridge_url'), '/') . '/send', [
                    'to' => $groupId,
                    'message' => $message,
                ]);

            if (! $response->successful()) {
                Log::warning('WhatsApp PPPoE notification failed.', [
                    'activity_id' => $activity->id,
                    'action' => $activity->action,
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('WhatsApp PPPoE notification exception.', [
                'activity_id' => $activity->id,
                'action' => $activity->action,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
