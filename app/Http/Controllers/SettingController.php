<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $tabFields = [
        'identitas' => ['company_name', 'brand_name', 'npwp', 'license_no', 'founded_year', 'address', 'description'],
        'kontak'    => ['phone', 'whatsapp', 'support_hours', 'email', 'billing_email', 'website'],
        'tagihan'   => ['billing_date', 'grace_period', 'late_fee', 'bank_account', 'currency', 'ewallet', 'custom_billing_enabled'],
        'jaringan'  => ['network_type', 'upstream', 'total_bandwidth', 'ip_range', 'dns', 'contention'],
    ];

    public function index(Request $request)
    {
        $settings = Setting::getAllAsArray();
        $activeTab = $request->query('tab', 'identitas');

        if (! array_key_exists($activeTab, $this->tabFields)) {
            $activeTab = 'identitas';
        }

        return view('settings.index', compact('settings', 'activeTab'));
    }

    public function update(Request $request): JsonResponse
    {
        $tab = $request->input('active_tab', 'identitas');

        if (! array_key_exists($tab, $this->tabFields)) {
            return response()->json(['success' => false, 'message' => 'Tab tidak valid.'], 422);
        }

        foreach ($this->tabFields[$tab] as $field) {
            if ($field === 'custom_billing_enabled') {
                Setting::set($field, $request->boolean($field) ? '1' : '0');
            } else {
                Setting::set($field, $request->input($field, ''));
            }
        }

        $tabLabels = [
            'identitas' => 'Identitas Perusahaan',
            'kontak'    => 'Kontak & Dukungan',
            'tagihan'   => 'Konfigurasi Tagihan',
            'jaringan'  => 'Informasi Jaringan',
        ];

        return response()->json([
            'success' => true,
            'message' => "Pengaturan {$tabLabels[$tab]} berhasil disimpan.",
            'tab'     => $tab,
        ]);
    }
}
