@extends('layouts.app')
@section('title', 'Isolir Pelanggan')
@section('page-title', 'Isolir')

@php
$s = fn(string $key, string $default = '') => $settings[$key] ?? $default;
$autoEnabled = $s('isolation_auto_enabled', '0') === '1';
$portalUrl = route('isolation.portal');
@endphp

@section('content')
<div class="space-y-5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Isolir Pelanggan</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kontrol isolir pelanggan menunggak dan halaman pembayaran kustom.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('isolation.portal') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3h7v7M10 14L21 3M21 14v5a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h5"/>
                </svg>
                Preview Portal
            </a>
            <button onclick="runAutoIsolation()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Jalankan Otomatis
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Mode Otomatis</p>
            <p class="text-2xl font-bold mt-2 {{ $autoEnabled ? 'text-green-600' : 'text-gray-900' }}">{{ $autoEnabled ? 'Aktif' : 'Nonaktif' }}</p>
            <p class="text-xs text-gray-500 mt-1">Default aman untuk uji coba manual.</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kandidat Isolir</p>
            <p class="text-2xl font-bold text-amber-600 mt-2">{{ $candidates }}</p>
            <p class="text-xs text-gray-500 mt-1">Lewat masa tenggang.</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sedang Diisolir</p>
            <p class="text-2xl font-bold text-red-600 mt-2">{{ $isolated }}</p>
            <p class="text-xs text-gray-500 mt-1">Masuk address-list Mikrotik.</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Tunggakan</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalOverdueAmount, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Belum dibayar / jatuh tempo.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 bg-white border border-gray-100 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Pelanggan Menunggak</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Manual isolir diprioritaskan untuk masa uji coba.</p>
                </div>
                <input id="customer-search" type="text" oninput="filterRows()" placeholder="Cari pelanggan..."
                       class="w-56 px-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px]">
                    <thead class="bg-gray-50/70 border-b border-gray-100">
                        <tr>
                            <th class="text-left text-xs font-semibold text-gray-400 px-5 py-3">Pelanggan</th>
                            <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">PPPoE / IP</th>
                            <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Tagihan</th>
                            <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Jatuh Tempo</th>
                            <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Status</th>
                            <th class="text-right text-xs font-semibold text-gray-400 px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="isolation-rows" class="divide-y divide-gray-50">
                        @forelse($customers as $customer)
                        <tr data-row data-name="{{ strtolower($customer->name.' '.$customer->pppoe_user.' '.$customer->ip_address) }}" class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $customer->phone }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-xs font-mono text-gray-800">{{ $customer->pppoe_user ?: '-' }}</p>
                                <p class="text-xs font-mono text-gray-400">{{ $customer->ip_address ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($customer->overdue_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">{{ $customer->overdue_count }} tagihan</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-700">{{ $customer->oldest_due_date?->format('d M Y') ?? '-' }}</p>
                                <p class="text-xs {{ $customer->is_candidate ? 'text-amber-600' : 'text-gray-400' }}">
                                    {{ $customer->is_candidate ? 'Siap isolir' : 'Belum lewat tenggang' }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                @if($customer->is_isolated)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Diisolir
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Normal
                                </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if($customer->is_isolated)
                                    <button onclick="releaseCustomer({{ $customer->id }}, this)"
                                            class="px-3 py-2 rounded-xl text-xs font-semibold bg-green-600 text-white hover:bg-green-500 transition-colors">
                                        Buka Isolir
                                    </button>
                                    @else
                                    <button onclick="isolateCustomer({{ $customer->id }}, this)"
                                            class="px-3 py-2 rounded-xl text-xs font-semibold bg-red-600 text-white hover:bg-red-500 transition-colors disabled:opacity-50">
                                        Isolir
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="text-sm font-semibold text-gray-500">Belum ada pelanggan menunggak.</p>
                                <p class="text-xs text-gray-400 mt-1">Data akan muncul setelah tagihan unpaid atau overdue tersedia.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-5">
            <form id="isolation-settings" class="bg-white border border-gray-100 rounded-2xl p-5 space-y-4">
                @csrf
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Pengaturan Isolir</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Otomatis default nonaktif untuk tahap uji coba.</p>
                </div>
                <label class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3 cursor-pointer">
                    <input type="checkbox" name="isolation_auto_enabled" value="1" class="mt-1 rounded border-gray-300 text-green-600" {{ $autoEnabled ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-semibold text-gray-900">Aktifkan isolir otomatis</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Jika aktif, command otomatis dapat mengisolir pelanggan lewat masa tenggang.</span>
                    </span>
                </label>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa tenggang setelah jatuh tempo</label>
                    <input type="number" name="isolation_grace_days" value="{{ $s('isolation_grace_days', '0') }}" min="0" max="60"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white">
                </div>
                <div class="grid grid-cols-1 gap-3">
                    <input type="text" name="isolation_bank_name" value="{{ $s('isolation_bank_name') }}" placeholder="Nama bank"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white">
                    <input type="text" name="isolation_bank_account" value="{{ $s('isolation_bank_account') }}" placeholder="Nomor rekening"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white">
                    <input type="text" name="isolation_account_name" value="{{ $s('isolation_account_name') }}" placeholder="Atas nama"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white">
                    <textarea name="isolation_cash_note" rows="3" placeholder="Info pembayaran tunai"
                              class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white resize-none">{{ $s('isolation_cash_note', 'Pembayaran tunai dapat dilakukan langsung ke kantor atau petugas resmi.') }}</textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-500 transition-colors">
                    Simpan Pengaturan
                </button>
            </form>

            <div class="bg-[#101827] text-white rounded-2xl p-5 overflow-hidden">
                <h2 class="text-sm font-bold">Seting Mikrotik</h2>
                <p class="text-xs text-slate-300 mt-1">Gunakan address-list <span class="font-mono text-emerald-300">isolir</span>. Sesuaikan IP server aplikasi.</p>
                <pre class="mt-4 text-[11px] leading-relaxed whitespace-pre-wrap bg-black/30 rounded-xl p-4 text-slate-100">/ip proxy
set enabled=yes port=8080

/ip proxy access
add action=deny redirect-to="{{ $portalUrl }}"

/ip firewall nat
add chain=dstnat src-address-list=isolir protocol=tcp dst-port=80 action=redirect to-ports=8080 comment="Redirect HTTP pelanggan isolir ke portal"

/ip firewall filter
add chain=forward src-address-list=isolir dst-address=IP_SERVER_APP protocol=tcp dst-port=80,443 action=accept comment="Allow portal isolir"
add chain=forward src-address-list=isolir protocol=udp dst-port=53 action=accept comment="Allow DNS pelanggan isolir"
add chain=forward src-address-list=isolir action=drop comment="Drop internet pelanggan isolir"</pre>
                <p class="text-[11px] text-slate-300 mt-3">Portal: <span class="font-mono text-emerald-300">{{ $portalUrl }}</span></p>
                <p class="text-[11px] text-slate-400 mt-2">Catatan: HTTPS tidak bisa dipaksa redirect transparan tanpa sertifikat. Rule ini menangkap HTTP dan memblokir trafik lain sampai pembayaran dibuka.</p>
            </div>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

document.getElementById('isolation-settings').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    try {
        const res = await fetch('{{ route('isolation.settings') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
        });
        const data = await res.json();
        showToast(data.success ? 'success' : 'error', data.message || 'Gagal menyimpan pengaturan.');
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    } finally {
        btn.disabled = false;
    }
});

async function isolateCustomer(id, btn) {
    await isolationRequest(`/isolir/customers/${id}`, 'POST', btn);
}

async function releaseCustomer(id, btn) {
    await isolationRequest(`/isolir/customers/${id}`, 'DELETE', btn);
}

async function runAutoIsolation() {
    await isolationRequest('{{ route('isolation.run_auto') }}', 'POST', null);
}

async function isolationRequest(url, method, btn) {
    if (btn) btn.disabled = true;
    try {
        const res = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();
        showToast(data.success ? 'success' : 'error', data.message || 'Operasi isolir gagal.');
        if (data.success) setTimeout(() => window.location.reload(), 700);
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    } finally {
        if (btn) btn.disabled = false;
    }
}

function filterRows() {
    const q = document.getElementById('customer-search').value.toLowerCase();
    document.querySelectorAll('[data-row]').forEach(row => {
        row.style.display = row.dataset.name.includes(q) ? '' : 'none';
    });
}

function showToast(type, message) {
    const el = document.createElement('div');
    el.className = `pointer-events-auto px-4 py-3 rounded-2xl shadow-xl text-sm font-semibold text-white ${type === 'success' ? 'bg-gray-900' : 'bg-red-600'}`;
    el.textContent = message;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
</script>
@endpush
