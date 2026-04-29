@extends('layouts.app')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@php
$s = fn(string $key, string $default = '') => $settings[$key] ?? $default;
@endphp

@section('content')
<div class="flex gap-0 -m-4 lg:-m-6 min-h-[calc(100vh-3.5rem)]">

    {{-- ===== LEFT: Tab Navigation ===== --}}
    <aside class="w-56 flex-shrink-0 bg-white border-r border-gray-100 flex flex-col pt-6 pb-4 px-3">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 px-3 mb-3">Pengaturan</p>
        <nav class="space-y-0.5" id="settings-nav">
            @php
            $tabs = [
                ['id'=>'identitas','label'=>'Identitas Perusahaan','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','color'=>'text-green-600'],
                ['id'=>'kontak','label'=>'Kontak & Dukungan','icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z','color'=>'text-blue-600'],
                ['id'=>'tagihan','label'=>'Konfigurasi Tagihan','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','color'=>'text-amber-600'],
                ['id'=>'jaringan','label'=>'Informasi Jaringan','icon'=>'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0','color'=>'text-purple-600'],
            ];
            @endphp
            @foreach($tabs as $tab)
            <button type="button" onclick="showTab('{{ $tab['id'] }}')"
                    id="tab-btn-{{ $tab['id'] }}"
                    class="tab-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all text-left {{ $tab['id'] === $activeTab ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $tab['color'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/>
                </svg>
                <span class="leading-tight">{{ $tab['label'] }}</span>
            </button>
            @endforeach
        </nav>

        <div class="mt-auto px-3 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 p-3 bg-green-50 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                <div>
                    <p class="text-xs font-semibold text-green-800">Sistem Aktif</p>
                    <p class="text-[10px] text-green-600">v1.0.0</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ===== RIGHT: Form Content ===== --}}
    <div class="flex-1 overflow-y-auto p-6 lg:p-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900" id="tab-title"></h1>
                <p class="text-sm text-gray-400 mt-0.5" id="tab-desc"></p>
            </div>
            <div class="flex gap-2">
                <button type="button" id="btn-reset"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Reset
                </button>
                <button type="submit" form="settings-form"
                        id="btn-save"
                        class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="btn-save-text">Simpan</span>
                </button>
            </div>
        </div>

        <form id="settings-form" action="{{ route('settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="active_tab" id="active_tab" value="{{ $activeTab }}">

        {{-- TAB: Identitas --}}
        <div id="tab-identitas" class="tab-panel {{ $activeTab !== 'identitas' ? 'hidden' : '' }}">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Perusahaan / ISP <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" value="{{ $s('company_name', 'PT Nusantara Net Sejahtera') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Singkat / Brand</label>
                    <input type="text" name="brand_name" value="{{ $s('brand_name', 'NusaNet') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">NPWP</label>
                    <input type="text" name="npwp" value="{{ $s('npwp', '01.234.567.8-901.000') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Izin / NIB</label>
                    <input type="text" name="license_no" value="{{ $s('license_no', 'NIB: 1234567890123') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Berdiri</label>
                    <input type="text" name="founded_year" value="{{ $s('founded_year', '2019') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat Kantor <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white resize-none">{{ $s('address', 'Jl. Sudirman No. 45, Kel. Gedong Meneng, Kec. Rajabasa, Bandar Lampung 35145') }}</textarea>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi Singkat ISP</label>
                    <textarea name="description" rows="2"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white resize-none">{{ $s('description', 'Penyedia layanan internet berbasis fiber optik untuk kebutuhan rumah dan bisnis di wilayah Bandar Lampung.') }}</textarea>
                </div>
            </div>
        </div>

        {{-- TAB: Kontak --}}
        <div id="tab-kontak" class="tab-panel {{ $activeTab !== 'kontak' ? 'hidden' : '' }}">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon Kantor</label>
                    <input type="text" name="phone" value="{{ $s('phone', '(0721) 123-4567') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. WhatsApp CS</label>
                    <input type="text" name="whatsapp" value="{{ $s('whatsapp', '0812-3456-7890') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Operasional CS</label>
                    <input type="text" name="support_hours" value="{{ $s('support_hours', 'Senin–Jumat, 08.00–17.00 WIB') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Utama</label>
                    <input type="email" name="email" value="{{ $s('email', 'info@nusanet.id') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Tagihan</label>
                    <input type="email" name="billing_email" value="{{ $s('billing_email', 'tagihan@nusanet.id') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Website</label>
                    <input type="url" name="website" value="{{ $s('website', 'https://nusanet.id') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
            </div>
        </div>

        {{-- TAB: Tagihan --}}
        <div id="tab-tagihan" class="tab-panel {{ $activeTab !== 'tagihan' ? 'hidden' : '' }}">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Tagih (tiap bulan)</label>
                    <select name="billing_date" class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        @for($i=1;$i<=28;$i++)
                        <option value="{{ $i }}" {{ (int)$s('billing_date', '1') === $i ? 'selected' : '' }}>Tanggal {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Tenggang (hari)</label>
                    <input type="number" name="grace_period" value="{{ $s('grace_period', '7') }}" min="1" max="30"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Denda Keterlambatan (%)</label>
                    <input type="number" name="late_fee" value="{{ $s('late_fee', '0') }}" min="0" max="100"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Rekening Bank</label>
                    <input type="text" name="bank_account" value="{{ $s('bank_account', 'BRI - 1234-5678-9012-345 (PT Nusantara Net)') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mata Uang</label>
                    <select name="currency" class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        <option value="IDR" {{ $s('currency', 'IDR') === 'IDR' ? 'selected' : '' }}>IDR – Rupiah</option>
                        <option value="USD" {{ $s('currency', 'IDR') === 'USD' ? 'selected' : '' }}>USD – Dollar</option>
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. DANA / GoPay / QRIS</label>
                    <input type="text" name="ewallet" value="{{ $s('ewallet', '0812-3456-7890 (NusaNet)') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>

                {{-- Custom Billing Option --}}
                <div class="lg:col-span-3">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <div class="relative flex-shrink-0 mt-0.5">
                                <input type="checkbox" name="custom_billing_enabled" id="custom_billing_enabled" value="1"
                                       {{ $s('custom_billing_enabled', '0') === '1' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-5 h-5 rounded-md border-2 border-amber-300 bg-white peer-checked:bg-amber-500 peer-checked:border-amber-500 transition-all flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 hidden peer-checked:block" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-amber-900">Aktifkan Metode Penagihan Custom</p>
                                <p class="text-xs text-amber-700 mt-0.5 leading-relaxed">
                                    Pelanggan dapat mengatur jadwal dan metode penagihan secara mandiri sesuai kebutuhan mereka.
                                    Sistem akan menagih berdasarkan konfigurasi yang ditentukan oleh masing-masing pelanggan.
                                </p>
                            </div>
                        </label>
                        <div id="custom-billing-note" class="{{ $s('custom_billing_enabled', '0') === '1' ? '' : 'hidden' }} mt-3 pt-3 border-t border-amber-200">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-amber-700">
                                    Fitur ini aktif. Pelanggan yang mengaktifkan penagihan custom akan ditagih sesuai jadwal yang mereka konfigurasi, bukan siklus tagihan global.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: Jaringan --}}
        <div id="tab-jaringan" class="tab-panel {{ $activeTab !== 'jaringan' ? 'hidden' : '' }}">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe Jaringan</label>
                    <select name="network_type" class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        @php $nt = $s('network_type', 'ftth'); @endphp
                        <option value="ftth"     {{ $nt === 'ftth'     ? 'selected' : '' }}>FTTH (Fiber to the Home)</option>
                        <option value="fttb"     {{ $nt === 'fttb'     ? 'selected' : '' }}>FTTB (Fiber to the Building)</option>
                        <option value="fttc"     {{ $nt === 'fttc'     ? 'selected' : '' }}>FTTC (Fiber to the Cabinet)</option>
                        <option value="wireless" {{ $nt === 'wireless' ? 'selected' : '' }}>Fixed Wireless</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Upstream Provider</label>
                    <input type="text" name="upstream" value="{{ $s('upstream', 'Biznet / IIX') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kapasitas Bandwidth Total</label>
                    <input type="text" name="total_bandwidth" value="{{ $s('total_bandwidth', '1 Gbps') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">IP Range / Subnet Pelanggan</label>
                    <input type="text" name="ip_range" value="{{ $s('ip_range', '192.168.1.0/24, 192.168.2.0/24') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">DNS Server</label>
                    <input type="text" name="dns" value="{{ $s('dns', '8.8.8.8, 1.1.1.1') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rasio Kontention</label>
                    <input type="text" name="contention" value="{{ $s('contention', '1:8') }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                </div>
            </div>
        </div>

        </form>
    </div>
</div>

{{-- ===== TOAST CONTAINER ===== --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

@push('scripts')
<script>
// ─── Tab metadata ────────────────────────────────────────────────────────────
const tabMeta = {
    identitas: { title: 'Identitas Perusahaan', desc: 'Informasi dasar ISP yang tampil pada tagihan & laporan' },
    kontak:    { title: 'Kontak & Dukungan',    desc: 'Nomor dan email yang dapat dihubungi pelanggan' },
    tagihan:   { title: 'Konfigurasi Tagihan',  desc: 'Pengaturan siklus dan metode pembayaran' },
    jaringan:  { title: 'Informasi Jaringan',   desc: 'Konfigurasi teknis infrastruktur ISP' },
};

// ─── Show Tab ────────────────────────────────────────────────────────────────
function showTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-gray-100', 'text-gray-900', 'font-semibold');
        b.classList.add('text-gray-500');
    });

    document.getElementById('tab-' + id).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + id);
    btn.classList.add('bg-gray-100', 'text-gray-900', 'font-semibold');
    btn.classList.remove('text-gray-500');

    document.getElementById('tab-title').textContent = tabMeta[id].title;
    document.getElementById('tab-desc').textContent  = tabMeta[id].desc;
    document.getElementById('active_tab').value      = id;

    // Update URL without reload so back-button and copy-link work
    const url = new URL(window.location);
    url.searchParams.set('tab', id);
    history.replaceState(null, '', url);
}

// ─── Toast ───────────────────────────────────────────────────────────────────
function showToast(type, message) {
    const container = document.getElementById('toast-container');

    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
    };

    const colors = {
        success: 'bg-gray-900 text-white',
        error:   'bg-red-600 text-white',
        warning: 'bg-amber-500 text-white',
    };

    const iconColors = {
        success: 'text-green-400',
        error:   'text-red-200',
        warning: 'text-amber-100',
    };

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl text-sm font-medium
        transition-all duration-300 translate-y-4 opacity-0 min-w-[260px] max-w-sm ${colors[type]}`;

    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 ${iconColors[type]}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            ${icons[type]}
        </svg>
        <span class="flex-1 leading-snug">${message}</span>
        <button onclick="this.closest('[data-toast]').remove()" class="ml-1 opacity-60 hover:opacity-100 transition-opacity flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

    toast.setAttribute('data-toast', '');
    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });
    });

    // Auto-dismiss after 4 s
    const dismissTimer = setTimeout(() => dismissToast(toast), 4000);
    toast.addEventListener('mouseenter', () => clearTimeout(dismissTimer));
    toast.addEventListener('mouseleave', () => setTimeout(() => dismissToast(toast), 1500));
}

function dismissToast(toast) {
    if (!toast.parentElement) return;
    toast.classList.add('translate-y-4', 'opacity-0');
    setTimeout(() => toast.remove(), 300);
}

// ─── Save Button state ────────────────────────────────────────────────────────
function setSaving(saving) {
    const btn  = document.getElementById('btn-save');
    const text = document.getElementById('btn-save-text');
    if (saving) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        text.textContent = 'Menyimpan…';
    } else {
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        text.textContent = 'Simpan';
    }
}

// ─── Form Submit (AJAX) ───────────────────────────────────────────────────────
document.getElementById('settings-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    setSaving(true);

    try {
        const formData = new FormData(this);

        // Ensure checkbox off-state is sent correctly
        if (!document.getElementById('custom_billing_enabled').checked) {
            formData.set('custom_billing_enabled', '0');
        }

        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const data = await response.json();

        if (response.ok && data.success) {
            showToast('success', data.message);
        } else {
            const msg = data.message || 'Gagal menyimpan pengaturan.';
            showToast('error', msg);
        }
    } catch (err) {
        showToast('error', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
    } finally {
        setSaving(false);
    }
});

// ─── Reset Button ─────────────────────────────────────────────────────────────
document.getElementById('btn-reset').addEventListener('click', function () {
    const tab = document.getElementById('active_tab').value;
    if (confirm('Reset perubahan yang belum disimpan pada tab ini?')) {
        window.location.href = '{{ route("settings") }}?tab=' + tab;
    }
});

// ─── Checkbox toggle note ─────────────────────────────────────────────────────
const cbCustom = document.getElementById('custom_billing_enabled');
const noteEl   = document.getElementById('custom-billing-note');
const checkBox = cbCustom?.closest('label')?.querySelector('.w-5');

function syncCheckboxUI() {
    if (!cbCustom) return;
    const checked = cbCustom.checked;

    // Show/hide info note
    if (noteEl) noteEl.classList.toggle('hidden', !checked);

    // Sync visual checkbox box
    if (checkBox) {
        const tick = checkBox.querySelector('svg');
        if (checked) {
            checkBox.classList.add('bg-amber-500', 'border-amber-500');
            checkBox.classList.remove('border-amber-300');
            if (tick) tick.classList.remove('hidden');
        } else {
            checkBox.classList.remove('bg-amber-500', 'border-amber-500');
            checkBox.classList.add('border-amber-300');
            if (tick) tick.classList.add('hidden');
        }
    }
}

cbCustom?.addEventListener('change', syncCheckboxUI);

// ─── Init ─────────────────────────────────────────────────────────────────────
(function init() {
    const initTab = '{{ $activeTab }}';
    showTab(initTab);
    syncCheckboxUI();
})();
</script>
@endpush

@endsection
