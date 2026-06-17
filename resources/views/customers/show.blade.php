@extends('layouts.app')
@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

{{-- Header --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('customers.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm border border-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div class="min-w-0 flex-1">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <h1 id="customer-name-heading" class="text-xl font-bold text-gray-900 truncate">{{ $customer->name }}</h1>
            <button type="button" onclick="openCustomerEditModal()"
                    class="inline-flex items-center gap-1.5 self-start px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Data
            </button>
        </div>
        <p id="customer-subtitle" class="text-sm text-gray-400 mt-0.5">ID: {{ $customer->customer_number ?? '-' }} | Pemantauan Trafik dan Detail Pelanggan</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Info Pelanggan --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Informasi Identitas</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status Layanan</p>
                    @if($customer->status === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                        </span>
                    @elseif($customer->status === 'suspend')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Suspend
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Terminate
                        </span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Nomor Telepon / WhatsApp</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->phone }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Alamat Pemasangan</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->address }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Tanggal Bergabung</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->join_date?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Layanan Jaringan</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Paket Internet</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->package?->name ?? 'Belum ada paket' }}</p>
                </div>
                @if($customer->package)
                <div>
                    <p class="text-xs text-gray-400 mb-1">Kecepatan</p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->package->speed_download }} Mbps (DL) / {{ $customer->package->speed_upload }} Mbps (UL)</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400 mb-1">IP Address</p>
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-gray-900 font-mono truncate">{{ $customer->ip_address ?? 'Dynamic/DHCP' }}</p>
                        @if(!empty($customer->pppoe_user) && !empty($customer->ip_address))
                            <button type="button" onclick="openOntAdminModal('{{ $customer->ip_address }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-xl transition-colors flex-shrink-0"
                                    title="Buka admin ONT http://{{ $customer->ip_address }}:80">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.172-1.172M10.172 13.828a4 4 0 005.656 0l3-3a4 4 0 10-5.656-5.656L12 6.343"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">PPPoE Username</p>
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-gray-900 font-mono truncate">{{ $customer->pppoe_user ?? '-' }}</p>
                        @if(!empty($customer->pppoe_user))
                            <button type="button" onclick="openPppoeMappingPanel()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                Ubah Mapping
                            </button>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">ONU / SN / Modem ID</p>
                    <p class="text-sm font-medium text-gray-900 font-mono">{{ $customer->onu_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">ACS Device ID</p>
                    <p class="text-sm font-medium text-gray-900 font-mono break-all">{{ $customer->acs_device_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">SSID WiFi Tercatat</p>
                    <p id="wifi-ssid-display" class="text-sm font-medium text-gray-900">{{ $customer->wifi_ssid ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Uptime Koneksi</p>
                    <p id="uptime-display" class="text-sm font-medium text-gray-900">
                        @if(empty($customer->pppoe_user))
                            <span class="text-gray-400">—</span>
                        @else
                            <span class="text-gray-400">Memuat...</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div id="pppoe-map-card" class="{{ empty($customer->pppoe_user) ? '' : 'hidden' }} bg-white rounded-2xl border border-amber-100 p-6 shadow-sm">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">{{ empty($customer->pppoe_user) ? 'Mapping PPPoE' : 'Ubah Mapping PPPoE' }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Pilih router, ambil akun PPPoE, lalu mapping langsung ke pelanggan ini.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 uppercase">
                    {{ empty($customer->pppoe_user) ? 'Belum Mapped' : 'Mapped' }}
                </span>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Router Mikrotik</label>
                    <select id="detail-router-select"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        <option value="">Pilih router...</option>
                        @foreach($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->host }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" onclick="fetchDetailPppoeSecrets()" id="detail-fetch-pppoe-btn"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span id="detail-fetch-pppoe-label">Ambil Akun PPPoE</span>
                </button>

                <div id="detail-pppoe-result" class="hidden space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Akun PPPoE Tersedia</label>
                        <div class="mb-2 flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input type="text" id="detail-pppoe-search" placeholder="Cari username PPPoE, profile, IP, atau MAC..."
                                   class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1">
                        </div>
                        <select id="detail-pppoe-select"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                            <option value="">Pilih akun PPPoE...</option>
                        </select>
                        <p id="detail-pppoe-hint" class="text-[10px] text-gray-400 mt-1"></p>
                    </div>

                    <div id="detail-pppoe-preview" class="hidden rounded-xl bg-gray-50 border border-gray-100 p-3 text-xs text-gray-600"></div>

                    <button type="button" onclick="mapDetailSelectedPppoe()" id="detail-map-pppoe-btn" disabled
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        {{ empty($customer->pppoe_user) ? 'Mapping ke' : 'Simpan Mapping Baru untuk' }} {{ $customer->name }}
                    </button>
                </div>

                <p id="detail-pppoe-message" class="hidden text-xs rounded-xl px-3 py-2"></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Manajemen WiFi ONT</h3>
                <button type="button" id="ont-refresh-btn"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors disabled:opacity-60"
                        {{ empty($customer->acs_device_id) ? 'disabled' : '' }}>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5M19 5h-5M5 19h5"/>
                    </svg>
                    Refresh
                </button>
            </div>
            @if(empty($customer->acs_device_id))
                <div id="quick-acs-card" class="bg-amber-50 border border-amber-200 text-amber-900 text-sm rounded-xl p-4 mb-4">
                    <p class="font-semibold mb-2">ACS Device ID belum diisi</p>
                    <p class="text-xs text-amber-800 mb-3">Isi ACS Device ID di sini untuk langsung membaca data ONT dari GenieACS dan mengaktifkan manajemen WiFi.</p>
                    <form id="quick-acs-form" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold mb-1.5">ACS Device ID</label>
                            <input type="text" name="acs_device_id" id="quick-acs-device-id" required maxlength="255"
                                   placeholder="Contoh: ZTE%2D..."
                                   class="w-full px-3.5 py-2.5 text-sm border border-amber-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-white font-mono">
                        </div>
                        <button type="submit" id="quick-acs-save-btn"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors disabled:opacity-60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="quick-acs-save-text">Simpan & Ambil Data GenieACS</span>
                        </button>
                    </form>
                    <p id="quick-acs-message" class="hidden text-xs rounded-xl px-3 py-2 mt-3"></p>
                </div>
            @endif

            <div id="genieacs-device-card" class="rounded-xl border border-gray-100 bg-white p-4 mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Informasi Device GenieACS</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <p class="text-[10px] text-gray-400">ACS Device ID</p>
                        <p id="acs-device-display" class="text-sm font-bold text-gray-900 font-mono break-all">{{ $customer->acs_device_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Manufacturer</p>
                        <p id="genieacs-manufacturer" class="text-sm font-bold text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Product Class</p>
                        <p id="genieacs-product-class" class="text-sm font-bold text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Serial Number</p>
                        <p id="genieacs-serial-number" class="text-sm font-bold text-gray-900 font-mono">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">SSID dari GenieACS</p>
                        <p id="genieacs-wifi-ssid" class="text-sm font-bold text-gray-900">-</p>
                    </div>
                </div>
            </div>

            <div id="ont-optical-card" class="rounded-xl border border-gray-100 bg-gray-50 p-4 mb-4">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi Optik ONT</p>
                        <p id="ont-info-status" class="text-[11px] text-gray-400 mt-0.5">Memuat data dari GenieACS...</p>
                    </div>
                    <span id="ont-rx-badge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">-</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[10px] text-gray-400">RX Power</p>
                        <p id="ont-rx-power" class="text-sm font-bold text-gray-900 font-mono">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">TX Power</p>
                        <p id="ont-tx-power" class="text-sm font-bold text-gray-900 font-mono">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Temperature</p>
                        <p id="ont-temperature" class="text-sm font-bold text-gray-900 font-mono">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Distance</p>
                        <p id="ont-distance" class="text-sm font-bold text-gray-900 font-mono">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Bias Current</p>
                        <p id="ont-bias-current" class="text-sm font-bold text-gray-900 font-mono">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">PON Status</p>
                        <p id="ont-pon-status" class="text-sm font-bold text-gray-900 truncate">-</p>
                    </div>
                </div>
            </div>

            <form id="wifi-form" class="space-y-4 mt-{{ empty($customer->acs_device_id) ? '4' : '0' }}">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama WiFi / SSID</label>
                    <input type="text" name="ssid" value="{{ $customer->wifi_ssid ?? '' }}" maxlength="32" required
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password WiFi Baru</label>
                    <input type="password" name="password" minlength="8" maxlength="63" required
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                    <p class="text-[10px] text-gray-400 mt-1">WPA/WPA2 umumnya membutuhkan 8-63 karakter.</p>
                </div>
                <button type="submit" id="wifi-save-btn"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                        {{ empty($customer->acs_device_id) ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    <span id="wifi-save-text">Kirim Perintah Ubah WiFi</span>
                </button>
                <p id="wifi-message" class="hidden text-xs rounded-xl px-3 py-2"></p>
            </form>
        </div>
    </div>

    {{-- Kolom Kanan: Monitoring Chart --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Realtime Traffic Monitoring --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Monitoring Trafik Realtime</h3>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
                </span>
            </div>
            @if(empty($customer->pppoe_user))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg p-4 mb-4">
                    <strong>Perhatian:</strong> Pelanggan ini belum dimaping PPPoE akun nya. Tidak dapat menampilkan traffic real-time dari router.
                </div>
            @endif
            <div class="h-64 w-full relative">
                <canvas id="trafficChart"></canvas>
            </div>
            <div class="flex gap-6 mt-4 pt-4 border-t border-gray-50 justify-center">
                <div class="text-center">
                    <p class="text-xs text-gray-400 mb-1">Download</p>
                    <p class="text-lg font-bold text-green-500" id="current-rx">0.0 Mbps</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-400 mb-1">Upload</p>
                    <p class="text-lg font-bold text-blue-500" id="current-tx">0.0 Mbps</p>
                </div>
            </div>
        </div>

        {{-- Uptime Data Usage --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Penggunaan Data Selama Uptime</h3>
            <div class="h-64 w-full relative">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

    </div>
</div>

{{-- ONT Admin Web Modal --}}
<div id="ont-admin-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeOntAdminModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-5">
        <div id="ont-admin-card" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[82vh] flex flex-col overflow-hidden transition-all duration-200 scale-95 opacity-0">
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 bg-white">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-gray-900">Admin ONT</h2>
                    <p id="ont-admin-url-label" class="text-xs text-gray-400 font-mono truncate">-</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="refreshOntAdminFrame()"
                            class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors"
                            title="Refresh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5M19 5h-5M5 19h5"/>
                        </svg>
                    </button>
                    <button type="button" onclick="openOntAdminNewTab()"
                            class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors"
                            title="Buka tab baru">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </button>
                    <button type="button" onclick="toggleOntAdminMaximize()" id="ont-admin-max-btn"
                            class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors"
                            title="Maximize">
                        <svg id="ont-admin-max-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/>
                        </svg>
                    </button>
                    <button type="button" onclick="closeOntAdminModal()"
                            class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors"
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="bg-amber-50 border-b border-amber-100 px-4 py-2 text-xs text-amber-800">
                Halaman admin ONT dibuka lewat proxy HTTPS aplikasi. Jika tidak tampil, pastikan server Ubuntu bisa route/ping ke IP pelanggan.
            </div>
            <iframe id="ont-admin-frame" src="about:blank" class="w-full flex-1 bg-white" referrerpolicy="no-referrer"></iframe>
        </div>
    </div>
</div>

{{-- Edit Customer Modal --}}
<div id="detail-customer-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCustomerEditModal()"></div>
    <div class="absolute inset-0 flex items-start justify-center p-3 sm:p-5 overflow-hidden">
        <div id="detail-customer-modal-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl mt-4 max-h-[calc(100vh-2rem)] flex flex-col transition-all duration-200 scale-95 opacity-0">
            <div id="detail-customer-modal-header" class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100 cursor-move select-none">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Edit Data Pelanggan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Drag header untuk memindahkan modal. Gunakan maximize jika butuh ruang kerja penuh.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleCustomerEditMaximize()" id="detail-customer-max-btn"
                            class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors"
                            title="Maximize">
                        <svg id="detail-customer-max-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/>
                        </svg>
                    </button>
                    <button type="button" onclick="closeCustomerEditModal()"
                            class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors"
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="detail-customer-form" class="px-6 py-5 space-y-5 overflow-y-auto">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="latitude" value="{{ $customer->latitude }}">
                <input type="hidden" name="longitude" value="{{ $customer->longitude }}">

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Identitas</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
                            <input type="text" id="edit-name" name="name" value="{{ $customer->name }}" required maxlength="150"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                            <p class="detail-edit-err hidden text-xs text-red-500 mt-1" data-field="name"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Telepon / WhatsApp</label>
                            <input type="text" id="edit-phone" name="phone" value="{{ $customer->phone }}" maxlength="20"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                            <input type="email" id="edit-email" name="email" value="{{ $customer->email }}" maxlength="150"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                            <p class="detail-edit-err hidden text-xs text-red-500 mt-1" data-field="email"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                            <select id="edit-status" name="status"
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                <option value="aktif" @selected($customer->status === 'aktif')>Aktif</option>
                                <option value="suspend" @selected($customer->status === 'suspend')>Suspend</option>
                                <option value="terminate" @selected($customer->status === 'terminate')>Terminate</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat Pemasangan</label>
                            <textarea id="edit-address" name="address" rows="2"
                                      class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white resize-none">{{ $customer->address }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Layanan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paket Internet <span class="text-red-500">*</span></label>
                            <select id="edit-package" name="package_id" required
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                <option value="">Pilih paket...</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" @selected($customer->package_id === $package->id)>
                                        {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="detail-edit-err hidden text-xs text-red-500 mt-1" data-field="package_id"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bergabung <span class="text-red-500">*</span></label>
                            <input type="date" id="edit-join-date" name="join_date" value="{{ $customer->join_date?->format('Y-m-d') }}" required
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                            <p class="detail-edit-err hidden text-xs text-red-500 mt-1" data-field="join_date"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Tagihan</label>
                            <select id="edit-billing-date" name="billing_date"
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                @for($day = 1; $day <= 28; $day++)
                                    <option value="{{ $day }}" @selected((int) ($customer->billing_date ?? 1) === $day)>Tanggal {{ $day }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">IP Address</label>
                            <input type="text" id="edit-ip-address" name="ip_address" value="{{ $customer->ip_address }}" maxlength="45"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white font-mono">
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Jaringan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username PPPoE</label>
                            <input type="text" id="edit-pppoe-user" name="pppoe_user" value="{{ $customer->pppoe_user }}" maxlength="100"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">ID ONT / ONU</label>
                            <input type="text" id="edit-onu-id" name="onu_id" value="{{ $customer->onu_id }}" maxlength="100"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">ACS Device ID</label>
                            <input type="text" id="edit-acs-device" name="acs_device_id" value="{{ $customer->acs_device_id }}" maxlength="255"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Serial Number ONT dari GenieACS</label>
                            <input type="text" id="edit-ont-serial" name="ont_serial_number" value="{{ $customer->ont_serial_number }}" maxlength="100"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white font-mono">
                            <p class="text-[10px] text-gray-400 mt-1">Manufacturer dan Product Class tampil otomatis di panel Manajemen WiFi ONT.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">SSID WiFi Tercatat</label>
                            <input type="text" id="edit-wifi-ssid" name="wifi_ssid" value="{{ $customer->wifi_ssid }}" maxlength="32"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                    <textarea id="edit-notes" name="notes" rows="2" maxlength="1000"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white resize-none">{{ $customer->notes }}</textarea>
                </div>

                <div id="detail-edit-message" class="hidden text-xs rounded-xl px-3 py-2"></div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCustomerEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="detail-edit-save-btn"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="detail-edit-save-text">Perbarui</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const DETAIL_CUSTOMER_ID = {{ $customer->id }};
const DETAIL_MAPPED_PPPOE = @json($mappedPppoeUsers);
let DETAIL_SECRETS = [];
let DETAIL_AVAILABLE_SECRET_INDEXES = [];
let DETAIL_HAS_ACS = {{ filled($customer->acs_device_id) ? 'true' : 'false' }};
let DETAIL_MODAL_MAXIMIZED = false;
let DETAIL_MODAL_DRAG = null;
let ONT_ADMIN_URL = '';
const ONT_ADMIN_PROXY_BASE = @json("/customers/{$customer->id}/ont-admin-proxy");
let ONT_ADMIN_MAXIMIZED = false;

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
}

function showInlineMessage(el, type, message) {
    if (!el) return;
    el.textContent = message;
    el.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700', 'bg-blue-50', 'text-blue-700');
    if (type === 'success') el.classList.add('bg-green-50', 'text-green-700');
    else if (type === 'info') el.classList.add('bg-blue-50', 'text-blue-700');
    else el.classList.add('bg-red-50', 'text-red-700');
}

function openOntAdminModal(ipAddress) {
    const modal = document.getElementById('ont-admin-modal');
    const card = document.getElementById('ont-admin-card');
    const frame = document.getElementById('ont-admin-frame');
    const label = document.getElementById('ont-admin-url-label');

    ONT_ADMIN_URL = `${ONT_ADMIN_PROXY_BASE}/`;
    label.textContent = `http://${ipAddress}:80/ via proxy`;
    frame.src = ONT_ADMIN_URL;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }));
}

function closeOntAdminModal() {
    const modal = document.getElementById('ont-admin-modal');
    const card = document.getElementById('ont-admin-card');
    const frame = document.getElementById('ont-admin-frame');

    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        frame.src = 'about:blank';
        document.body.style.overflow = '';
    }, 180);
}

function refreshOntAdminFrame() {
    const frame = document.getElementById('ont-admin-frame');
    if (ONT_ADMIN_URL) frame.src = ONT_ADMIN_URL;
}

function openOntAdminNewTab() {
    if (ONT_ADMIN_URL) window.open(ONT_ADMIN_URL, '_blank', 'noopener');
}

function toggleOntAdminMaximize() {
    const card = document.getElementById('ont-admin-card');
    const icon = document.getElementById('ont-admin-max-icon');
    ONT_ADMIN_MAXIMIZED = !ONT_ADMIN_MAXIMIZED;

    if (ONT_ADMIN_MAXIMIZED) {
        card.classList.remove('max-w-6xl', 'h-[82vh]', 'rounded-2xl');
        card.classList.add('max-w-none', 'h-[calc(100vh-1rem)]', 'rounded-xl');
        card.style.width = 'calc(100vw - 1rem)';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8 4v4H4M16 4v4h4M8 20v-4H4M16 20v-4h4"/>';
        return;
    }

    card.classList.add('max-w-6xl', 'h-[82vh]', 'rounded-2xl');
    card.classList.remove('max-w-none', 'h-[calc(100vh-1rem)]', 'rounded-xl');
    card.style.width = '';
    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/>';
}

function openPppoeMappingPanel() {
    const card = document.getElementById('pppoe-map-card');
    const message = document.getElementById('detail-pppoe-message');
    if (!card) return;

    card.classList.remove('hidden');
    showInlineMessage(message, 'info', 'Pilih router dan akun PPPoE baru untuk mengganti mapping pelanggan ini.');
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function fetchDetailPppoeSecrets() {
    const routerId = document.getElementById('detail-router-select')?.value;
    const btn = document.getElementById('detail-fetch-pppoe-btn');
    const label = document.getElementById('detail-fetch-pppoe-label');
    const result = document.getElementById('detail-pppoe-result');
    const select = document.getElementById('detail-pppoe-select');
    const hint = document.getElementById('detail-pppoe-hint');
    const message = document.getElementById('detail-pppoe-message');
    const mapBtn = document.getElementById('detail-map-pppoe-btn');

    if (!routerId) {
        showInlineMessage(message, 'error', 'Pilih router terlebih dahulu.');
        return;
    }

    btn.disabled = true;
    label.textContent = 'Mengambil data...';
    message?.classList.add('hidden');

    try {
        const response = await fetch(`/pppoe-mapping/${routerId}/secrets`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
            showInlineMessage(message, 'error', data.message || 'Gagal mengambil data PPPoE.');
            return;
        }

        DETAIL_SECRETS = data.secrets || [];
        DETAIL_AVAILABLE_SECRET_INDEXES = DETAIL_SECRETS
            .map((secret, index) => ({ secret, index }))
            .filter(item => !DETAIL_MAPPED_PPPOE[item.secret.username])
            .map(item => item.index);
        renderDetailPppoeOptions();

        hint.textContent = `${DETAIL_AVAILABLE_SECRET_INDEXES.length} akun belum dimapping dari ${DETAIL_SECRETS.length} akun PPPoE di ${data.router_name}.`;
        result.classList.remove('hidden');
        if (mapBtn) mapBtn.disabled = true;
        renderDetailPppoePreview();
        showInlineMessage(message, 'success', 'Data PPPoE berhasil diambil.');
    } catch (error) {
        showInlineMessage(message, 'error', 'Gagal mengambil data PPPoE: ' + error.message);
    } finally {
        btn.disabled = false;
        label.textContent = 'Ambil Akun PPPoE';
    }
}

function renderDetailPppoePreview() {
    const select = document.getElementById('detail-pppoe-select');
    const preview = document.getElementById('detail-pppoe-preview');
    const mapBtn = document.getElementById('detail-map-pppoe-btn');
    const idx = select?.value;
    const secret = idx !== '' ? DETAIL_SECRETS[Number(idx)] : null;

    if (!secret) {
        preview?.classList.add('hidden');
        if (preview) preview.innerHTML = '';
        if (mapBtn) mapBtn.disabled = true;
        return;
    }

    preview.innerHTML = `
        <div class="grid grid-cols-2 gap-2">
            <div><span class="text-gray-400">Username:</span><br><span class="font-mono font-semibold text-gray-800">${escapeHtml(secret.username)}</span></div>
            <div><span class="text-gray-400">Status:</span><br><span class="${secret.online ? 'text-green-600' : 'text-gray-500'} font-semibold">${secret.online ? 'Online' : 'Offline'}</span></div>
            <div><span class="text-gray-400">IP:</span><br><span class="font-mono">${escapeHtml(secret.ip || '-')}</span></div>
            <div><span class="text-gray-400">MAC ONT:</span><br><span class="font-mono">${escapeHtml(secret.mac || '-')}</span></div>
            <div class="col-span-2"><span class="text-gray-400">Profile:</span><br><span>${escapeHtml(secret.profile || '-')}</span></div>
        </div>
    `;
    preview.classList.remove('hidden');
    if (mapBtn) mapBtn.disabled = false;
}

function renderDetailPppoeOptions() {
    const select = document.getElementById('detail-pppoe-select');
    const search = (document.getElementById('detail-pppoe-search')?.value || '').trim().toLowerCase();
    if (!select) return;

    const rows = DETAIL_AVAILABLE_SECRET_INDEXES
        .map(index => ({ index, secret: DETAIL_SECRETS[index] }))
        .filter(({ secret }) => {
            if (!search) return true;
            return [
                secret.username,
                secret.profile,
                secret.ip,
                secret.mac,
                secret.comment,
            ].some(value => String(value || '').toLowerCase().includes(search));
        });

    select.innerHTML = '<option value="">Pilih akun PPPoE...</option>' + rows.map(({ secret, index }) => {
        const status = secret.online ? 'Online' : 'Offline';
        const profile = secret.profile ? ` - ${escapeHtml(secret.profile)}` : '';
        const ip = secret.ip ? ` - ${escapeHtml(secret.ip)}` : '';
        return `<option value="${index}">${escapeHtml(secret.username)} (${status}${profile}${ip})</option>`;
    }).join('');

    document.getElementById('detail-map-pppoe-btn').disabled = true;
    renderDetailPppoePreview();
}

async function mapDetailSelectedPppoe() {
    const select = document.getElementById('detail-pppoe-select');
    const mapBtn = document.getElementById('detail-map-pppoe-btn');
    const message = document.getElementById('detail-pppoe-message');
    const idx = select?.value;
    const secret = idx !== '' ? DETAIL_SECRETS[Number(idx)] : null;

    if (!secret) {
        showInlineMessage(message, 'error', 'Pilih akun PPPoE terlebih dahulu.');
        return;
    }

    mapBtn.disabled = true;
    message?.classList.add('hidden');

    try {
        const response = await fetch('/pppoe-mapping/map', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                customer_id: DETAIL_CUSTOMER_ID,
                pppoe_user: secret.username,
                ip_address: secret.ip || null,
                mac_ont: secret.mac || null,
            }),
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
            showInlineMessage(message, 'error', data.message || 'Gagal mapping PPPoE.');
            mapBtn.disabled = false;
            return;
        }

        showInlineMessage(message, 'success', data.message + ' Halaman akan dimuat ulang...');
        setTimeout(() => window.location.reload(), 900);
    } catch (error) {
        showInlineMessage(message, 'error', 'Gagal mapping PPPoE: ' + error.message);
        mapBtn.disabled = false;
    }
}

function openCustomerEditModal() {
    const modal = document.getElementById('detail-customer-modal');
    const card = document.getElementById('detail-customer-modal-card');
    document.getElementById('detail-edit-message')?.classList.add('hidden');
    document.querySelectorAll('.detail-edit-err').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (!DETAIL_MODAL_MAXIMIZED && !card.style.left) {
        card.style.position = '';
        card.style.left = '';
        card.style.top = '';
        card.style.marginTop = '';
    }
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }));
}

function closeCustomerEditModal() {
    const modal = document.getElementById('detail-customer-modal');
    const card = document.getElementById('detail-customer-modal-card');
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function toggleCustomerEditMaximize() {
    const card = document.getElementById('detail-customer-modal-card');
    const icon = document.getElementById('detail-customer-max-icon');
    DETAIL_MODAL_MAXIMIZED = !DETAIL_MODAL_MAXIMIZED;

    if (DETAIL_MODAL_MAXIMIZED) {
        card.dataset.prevStyle = JSON.stringify({
            position: card.style.position,
            left: card.style.left,
            top: card.style.top,
            marginTop: card.style.marginTop,
            width: card.style.width,
            maxWidth: card.style.maxWidth,
            maxHeight: card.style.maxHeight,
            height: card.style.height,
        });
        card.style.position = 'fixed';
        card.style.left = '12px';
        card.style.top = '12px';
        card.style.marginTop = '0';
        card.style.width = 'calc(100vw - 24px)';
        card.style.maxWidth = 'none';
        card.style.height = 'calc(100vh - 24px)';
        card.style.maxHeight = 'calc(100vh - 24px)';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8 4v4H4M16 4v4h4M8 20v-4H4M16 20v-4h4"/>';
        return;
    }

    const prev = card.dataset.prevStyle ? JSON.parse(card.dataset.prevStyle) : {};
    card.style.position = prev.position || '';
    card.style.left = prev.left || '';
    card.style.top = prev.top || '';
    card.style.marginTop = prev.marginTop || '';
    card.style.width = prev.width || '';
    card.style.maxWidth = prev.maxWidth || '';
    card.style.maxHeight = prev.maxHeight || '';
    card.style.height = prev.height || '';
    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/>';
}

function initCustomerEditModalMove() {
    const card = document.getElementById('detail-customer-modal-card');
    const header = document.getElementById('detail-customer-modal-header');
    if (!card || !header) return;

    header.addEventListener('mousedown', event => {
        if (event.target.closest('button')) return;
        if (DETAIL_MODAL_MAXIMIZED) return;

        const rect = card.getBoundingClientRect();
        DETAIL_MODAL_DRAG = {
            offsetX: event.clientX - rect.left,
            offsetY: event.clientY - rect.top,
        };
        card.style.position = 'fixed';
        card.style.left = `${rect.left}px`;
        card.style.top = `${rect.top}px`;
        card.style.marginTop = '0';
        event.preventDefault();
    });

    document.addEventListener('mousemove', event => {
        if (!DETAIL_MODAL_DRAG) return;
        const width = card.offsetWidth;
        const height = card.offsetHeight;
        const left = Math.max(8, Math.min(window.innerWidth - width - 8, event.clientX - DETAIL_MODAL_DRAG.offsetX));
        const top = Math.max(8, Math.min(window.innerHeight - 56, event.clientY - DETAIL_MODAL_DRAG.offsetY));
        card.style.left = `${left}px`;
        card.style.top = `${top}px`;
    });

    document.addEventListener('mouseup', () => {
        DETAIL_MODAL_DRAG = null;
    });
}

async function submitCustomerEdit(event) {
    event.preventDefault();
    const form = event.target;
    const btn = document.getElementById('detail-edit-save-btn');
    const label = document.getElementById('detail-edit-save-text');
    const message = document.getElementById('detail-edit-message');

    document.querySelectorAll('.detail-edit-err').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });
    message.classList.add('hidden');
    btn.disabled = true;
    label.textContent = 'Menyimpan...';

    try {
        const response = await fetch('/customers/{{ $customer->id }}', {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        const data = await response.json();

        if (response.status === 422 && data.errors) {
            Object.entries(data.errors).forEach(([field, errors]) => {
                const el = document.querySelector(`.detail-edit-err[data-field="${field}"]`);
                if (el) {
                    el.textContent = errors[0];
                    el.classList.remove('hidden');
                }
            });
            showInlineMessage(message, 'error', 'Periksa kembali isian form.');
            return;
        }

        if (!response.ok || !data.success) {
            showInlineMessage(message, 'error', data.message || 'Gagal memperbarui pelanggan.');
            return;
        }

        showInlineMessage(message, 'success', data.message + ' Halaman akan dimuat ulang...');
        setTimeout(() => window.location.reload(), 800);
    } catch (error) {
        showInlineMessage(message, 'error', 'Gagal memperbarui pelanggan: ' + error.message);
    } finally {
        btn.disabled = false;
        label.textContent = 'Perbarui';
    }
}

function applyGenieAcsDeviceInfo(device = {}) {
    const set = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value || '-';
    };

    set('acs-device-display', device.id || document.getElementById('quick-acs-device-id')?.value || '{{ $customer->acs_device_id }}');
    set('genieacs-manufacturer', device.manufacturer);
    set('genieacs-product-class', device.product_class);
    set('genieacs-serial-number', device.serial_number);
    set('genieacs-wifi-ssid', device.wifi_ssid);

    if (device.wifi_ssid) {
        const display = document.getElementById('wifi-ssid-display');
        const wifiInput = document.querySelector('#wifi-form [name="ssid"]');
        const editWifiInput = document.getElementById('edit-wifi-ssid');
        if (display) display.textContent = device.wifi_ssid;
        if (wifiInput) wifiInput.value = device.wifi_ssid;
        if (editWifiInput) editWifiInput.value = device.wifi_ssid;
    }

    if (device.serial_number) {
        const editSerialInput = document.getElementById('edit-ont-serial');
        if (editSerialInput) editSerialInput.value = device.serial_number;
    }
}

async function submitQuickAcsDevice(event) {
    event.preventDefault();
    const form = event.target;
    const btn = document.getElementById('quick-acs-save-btn');
    const text = document.getElementById('quick-acs-save-text');
    const message = document.getElementById('quick-acs-message');

    btn.disabled = true;
    text.textContent = 'Menyimpan & membaca GenieACS...';
    message?.classList.add('hidden');

    try {
        const body = new FormData(form);
        body.append('_method', 'PATCH');
        const response = await fetch('/customers/{{ $customer->id }}/acs-device', {
            method: 'POST',
            body,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
            showInlineMessage(message, 'error', data.message || 'Gagal menyimpan ACS Device ID.');
            return;
        }

        DETAIL_HAS_ACS = true;
        applyGenieAcsDeviceInfo(data.device || {});
        document.getElementById('edit-acs-device').value = data.customer?.acs_device_id || form.acs_device_id.value;
        document.getElementById('ont-refresh-btn').disabled = false;
        document.getElementById('wifi-save-btn').disabled = false;
        document.getElementById('quick-acs-card')?.classList.add('hidden');
        showInlineMessage(message, 'success', data.message);
        fetchOntInfo(true);
    } catch (error) {
        showInlineMessage(message, 'error', 'Gagal menyimpan ACS Device ID: ' + error.message);
    } finally {
        btn.disabled = false;
        text.textContent = 'Simpan & Ambil Data GenieACS';
    }
}

function formatUptime(raw) {
    if (!raw) return '—';
    const m = raw.match(/(?:(\d+)w)?(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/);
    if (!m) return raw;
    const parts = [];
    if (m[1]) parts.push(m[1] + ' minggu');
    if (m[2]) parts.push(m[2] + ' hari');
    if (m[3]) parts.push(m[3] + ' jam');
    if (m[4]) parts.push(m[4] + ' menit');
    if (m[5]) parts.push(m[5] + ' detik');
    return parts.length ? parts.join(' ') : '—';
}

document.addEventListener("DOMContentLoaded", function() {
    const wifiForm = document.getElementById('wifi-form');
    const wifiMessage = document.getElementById('wifi-message');
    const wifiBtn = document.getElementById('wifi-save-btn');
    const wifiBtnText = document.getElementById('wifi-save-text');
    const ontRefreshBtn = document.getElementById('ont-refresh-btn');
    document.getElementById('detail-pppoe-select')?.addEventListener('change', renderDetailPppoePreview);
    document.getElementById('detail-pppoe-search')?.addEventListener('input', renderDetailPppoeOptions);
    document.getElementById('detail-customer-form')?.addEventListener('submit', submitCustomerEdit);
    document.getElementById('quick-acs-form')?.addEventListener('submit', submitQuickAcsDevice);
    initCustomerEditModalMove();

    function setText(id, value, unit = '') {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = value === null || value === undefined || value === '' ? '-' : `${value}${unit}`;
    }

    function rxQuality(rxPower) {
        if (rxPower === null || rxPower === undefined || rxPower === '') {
            return { label: '-', cls: 'bg-gray-100 text-gray-500' };
        }

        const value = Number(rxPower);
        if (!Number.isFinite(value)) {
            return { label: '-', cls: 'bg-gray-100 text-gray-500' };
        }

        if (value >= -24 && value <= -8) {
            return { label: 'Bagus', cls: 'bg-green-50 text-green-700' };
        }

        if (value >= -27 && value < -24) {
            return { label: 'Sedang', cls: 'bg-amber-50 text-amber-700' };
        }

        return { label: 'Buruk', cls: 'bg-red-50 text-red-700' };
    }

    async function fetchOntInfo(refresh = false) {
        const statusEl = document.getElementById('ont-info-status');
        const badgeEl = document.getElementById('ont-rx-badge');

        if (!ontRefreshBtn || !DETAIL_HAS_ACS) {
            statusEl.textContent = 'ACS Device ID belum diisi.';
            return;
        }

        ontRefreshBtn.disabled = true;
        statusEl.textContent = 'Mengambil data dari GenieACS...';

        try {
            const response = await fetch(`/customers/{{ $customer->id }}/ont-info${refresh ? '?refresh=1' : ''}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                statusEl.textContent = data.message || 'Gagal mengambil data ONT.';
                return;
            }

            applyGenieAcsDeviceInfo(data.device || {});
            const optical = data.optical || {};
            setText('ont-rx-power', optical.rx_power, ' dBm');
            setText('ont-tx-power', optical.tx_power, ' dBm');
            setText('ont-temperature', optical.temperature, ' °C');
            setText('ont-distance', optical.distance, ' m');
            setText('ont-bias-current', optical.bias_current, ' uA');
            setText('ont-pon-status', optical.pon_status);

            const quality = rxQuality(optical.rx_power);
            badgeEl.textContent = quality.label;
            badgeEl.className = `inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${quality.cls}`;
            statusEl.textContent = data.has_optical_data
                ? `Data terakhir dari ${data.source === 'olt' ? 'OLT HisFocus' : 'GenieACS'}.`
                : (data.fallback_message || 'Data optik belum ditemukan. Pastikan ONU ID atau MAC ONT pelanggan sesuai tabel OLT.');
        } catch (err) {
            statusEl.textContent = 'Koneksi ke aplikasi bermasalah.';
        } finally {
            ontRefreshBtn.disabled = !DETAIL_HAS_ACS;
        }
    }

    ontRefreshBtn?.addEventListener('click', () => fetchOntInfo(true));

    function showWifiMessage(type, message) {
        wifiMessage.textContent = message;
        wifiMessage.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
        if (type === 'success') {
            wifiMessage.classList.add('bg-green-50', 'text-green-700');
        } else {
            wifiMessage.classList.add('bg-red-50', 'text-red-700');
        }
    }

    wifiForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!wifiBtn || wifiBtn.disabled) return;

        wifiBtn.disabled = true;
        wifiBtnText.textContent = 'Mengirim ke GenieACS...';
        wifiMessage.classList.add('hidden');

        try {
            const response = await fetch('/customers/{{ $customer->id }}/wifi', {
                method: 'POST',
                body: new FormData(wifiForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showWifiMessage('success', data.message);
                document.getElementById('wifi-ssid-display').textContent = data.wifi_ssid || '-';
                wifiForm.querySelector('[name="password"]').value = '';
            } else {
                showWifiMessage('error', data.message || 'Gagal mengirim perintah ubah WiFi.');
            }
        } catch (err) {
            showWifiMessage('error', 'Koneksi ke aplikasi bermasalah.');
        } finally {
            wifiBtn.disabled = !DETAIL_HAS_ACS;
            wifiBtnText.textContent = 'Kirim Perintah Ubah WiFi';
        }
    });
    // ─── Setup Traffic Chart (Live Simulation) ───
    fetchOntInfo();

    const ctxTraffic = document.getElementById('trafficChart').getContext('2d');
    
    // Gradient configs
    const gradientRx = ctxTraffic.createLinearGradient(0, 0, 0, 300);
    gradientRx.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
    gradientRx.addColorStop(1, 'rgba(34, 197, 94, 0.0)');
    
    const gradientTx = ctxTraffic.createLinearGradient(0, 0, 0, 300);
    gradientTx.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    gradientTx.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    const maxSpeedDL = {{ $customer->package ? $customer->package->speed_download : 20 }};
    const maxSpeedUL = {{ $customer->package ? $customer->package->speed_upload : 10 }};
    
    let labels = Array.from({length: 20}, (_, i) => i);
    let dataRx = Array.from({length: 20}, () => 0);
    let dataTx = Array.from({length: 20}, () => 0);

    const trafficChart = new Chart(ctxTraffic, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Download (Mbps)',
                    data: dataRx,
                    borderColor: '#22c55e',
                    backgroundColor: gradientRx,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Upload (Mbps)',
                    data: dataTx,
                    borderColor: '#3b82f6',
                    backgroundColor: gradientTx,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 500,
                easing: 'linear'
            },
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 11 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    displayColors: true,
                }
            },
            scales: {
                x: {
                    display: false, // hide x axis ticks to simulate moving window easily
                },
                y: {
                    beginAtZero: true,
                    max: maxSpeedDL > maxSpeedUL ? maxSpeedDL + 5 : maxSpeedUL + 5,
                    grid: { color: '#f3f4f6' },
                    border: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    let usageChartInstance = null;

    async function fetchLiveTraffic() {
        try {
            const res = await fetch(`/customers/{{ $customer->id }}/live-traffic`);
            const data = await res.json();
            
            if (!data.success) {
                document.getElementById('current-rx').textContent = '-';
                document.getElementById('current-tx').textContent = '-';
                document.getElementById('uptime-display').textContent = '—';
                return;
            }

            // Update uptime
            const uptimeEl = document.getElementById('uptime-display');
            if (data.uptime) {
                uptimeEl.textContent = formatUptime(data.uptime);
            } else {
                uptimeEl.textContent = 'Offline';
            }

            const rx = data.rx;
            const tx = data.tx;
            
            document.getElementById('current-rx').textContent = rx + ' Mbps';
            document.getElementById('current-tx').textContent = tx + ' Mbps';

            trafficChart.data.datasets[0].data.push(rx);
            trafficChart.data.datasets[1].data.push(tx);
            trafficChart.data.datasets[0].data.shift();
            trafficChart.data.datasets[1].data.shift();
            trafficChart.update('quiet');

            // Update usage chart data if not already set or we want to re-render
            if (!usageChartInstance && data.usage) {
                usageChartInstance = new Chart(document.getElementById('usageChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Download (GB)', 'Upload (GB)'],
                        datasets: [{
                            data: [data.usage.download_gb, data.usage.upload_gb],
                            backgroundColor: ['#22c55e', '#3b82f6'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { size: 12 } } },
                            tooltip: { callbacks: { label: (c) => c.label + ': ' + c.raw + ' GB' } }
                        },
                        cutout: '70%'
                    }
                });
            } else if (usageChartInstance && data.usage) {
                usageChartInstance.data.datasets[0].data = [data.usage.download_gb, data.usage.upload_gb];
                usageChartInstance.update('quiet');
            }
        } catch (err) {
            console.error('Failed to fetch traffic data', err);
        }
    }

    @if(!empty($customer->pppoe_user))
        // Fetch API every 2 seconds if PPPoE is mapped
        setInterval(fetchLiveTraffic, 2000);
        fetchLiveTraffic();
    @endif
});
</script>
@endpush
