@extends('layouts.app')

@section('title', 'Manajemen WhatsApp')
@section('page-title', 'Manajemen WhatsApp')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen WhatsApp</h1>
                    <p class="text-sm text-gray-500">Hubungkan nomor WhatsApp dengan scan QR, lalu uji kirim pesan dari aplikasi web.</p>
                </div>
            </div>
        </div>
        <div id="wa-status-pill" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-600">
            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
            Memuat status
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Scan QR WhatsApp</h2>
                    <p class="text-sm text-gray-500">Buka WhatsApp di HP, pilih perangkat tertaut, lalu scan QR di bawah.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="connectWhatsapp()" class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Mulai Koneksi
                    </button>
                    <button type="button" onclick="refreshStatus()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M20 8A8 8 0 006.34 4.34M4 16a8 8 0 0013.66 3.66"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            <div class="p-5 grid grid-cols-1 lg:grid-cols-[360px_1fr] gap-6">
                <div class="bg-gray-50 border border-gray-100 rounded-2xl min-h-[360px] flex items-center justify-center p-5">
                    <div id="qr-empty" class="text-center">
                        <div class="w-20 h-20 rounded-2xl bg-white border border-gray-100 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-9 h-9 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h6v6H3V4zm12 0h6v6h-6V4zM3 16h6v4H3v-4zm12 0h2m2 0h2m-6 4h6M12 4v4m0 4h4m-4 4v4"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">QR belum tersedia</p>
                        <p class="text-xs text-gray-500 mt-1">Klik Mulai Koneksi atau tunggu beberapa detik.</p>
                    </div>
                    <img id="qr-image" src="" alt="QR WhatsApp" class="hidden w-full max-w-[320px] rounded-xl bg-white p-3 shadow-sm">
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Status Service</p>
                        <p id="status-message" class="text-sm font-semibold text-gray-900">Memuat...</p>
                        <p class="text-xs text-gray-500 mt-2">Bridge URL: <span class="font-mono">{{ $bridgeUrl }}</span></p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Nomor Terhubung</p>
                        <p id="connected-number" class="text-2xl font-bold text-gray-900">-</p>
                        <p class="text-xs text-gray-500 mt-1">Nomor akan muncul setelah QR berhasil discan.</p>
                    </div>
                    <button type="button" onclick="logoutWhatsapp()" class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Putuskan Sesi
                    </button>
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Uji Kirim Pesan</h2>
                <p class="text-sm text-gray-500">Gunakan untuk memastikan koneksi WhatsApp sudah bisa mengirim pesan.</p>
            </div>
            <form id="test-form" class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Nomor Tujuan</label>
                    <input type="text" name="to" required placeholder="Contoh: 082279122727" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Pesan</label>
                    <textarea name="message" required rows="6" maxlength="2000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none">Halo, ini pesan uji coba dari aplikasi ISP Manager.</textarea>
                </div>
                <button type="submit" id="btn-send" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white hover:bg-green-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                    Kirim Pesan Test
                </button>
                <div id="send-result" class="hidden rounded-xl px-4 py-3 text-sm"></div>
            </form>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
const WA_ROUTES = {
    status: @json('/whatsapp/status'),
    connect: @json('/whatsapp/connect'),
    logout: @json('/whatsapp/logout'),
    test: @json('/whatsapp/test-message'),
};
const WA_CSRF = document.querySelector('meta[name="csrf-token"]').content;
let waPoll = null;

function setStatusPill(state) {
    const pill = document.getElementById('wa-status-pill');
    const connected = state.connected === true;
    const qr = state.status === 'qr';
    const offline = state.status === 'offline' || state.success === false;
    const dot = connected ? 'bg-green-500' : (qr ? 'bg-amber-500' : (offline ? 'bg-red-500' : 'bg-gray-400'));
    const text = connected ? 'Terhubung' : (qr ? 'Menunggu Scan QR' : (offline ? 'Bridge Offline' : 'Belum Terhubung'));
    const style = connected ? 'bg-green-50 text-green-700' : (qr ? 'bg-amber-50 text-amber-700' : (offline ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600'));
    pill.className = `inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ${style}`;
    pill.innerHTML = `<span class="w-2 h-2 rounded-full ${dot}"></span>${text}`;
}

function renderStatus(data) {
    setStatusPill(data);
    const suffix = data.connecting_for_seconds ? ` (${data.connecting_for_seconds} detik)` : '';
    document.getElementById('status-message').textContent = (data.message || 'Status tidak diketahui.') + suffix;
    document.getElementById('connected-number').textContent = data.number || '-';

    const qrImage = document.getElementById('qr-image');
    const qrEmpty = document.getElementById('qr-empty');
    if (data.qr) {
        qrImage.src = data.qr;
        qrImage.classList.remove('hidden');
        qrEmpty.classList.add('hidden');
    } else {
        qrImage.src = '';
        qrImage.classList.add('hidden');
        qrEmpty.classList.remove('hidden');
    }
}

async function requestJson(url, options = {}) {
    const res = await fetch(url, {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': WA_CSRF,
            ...(options.headers || {}),
        },
        ...options,
    });
    const data = await res.json().catch(() => ({ success: false, message: 'Respons server tidak valid.' }));
    if (!res.ok) data.success = false;
    return data;
}

async function refreshStatus() {
    const data = await requestJson(WA_ROUTES.status);
    renderStatus(data);
}

async function connectWhatsapp() {
    renderStatus({ status: 'connecting', message: 'Menyiapkan koneksi WhatsApp...', connected: false });
    const data = await requestJson(WA_ROUTES.connect, { method: 'POST', body: '{}' });
    renderStatus(data);
}

async function logoutWhatsapp() {
    if (!confirm('Putuskan sesi WhatsApp dari aplikasi ini?')) return;
    const data = await requestJson(WA_ROUTES.logout, { method: 'POST', body: '{}' });
    renderStatus(data);
}

document.getElementById('test-form').addEventListener('submit', async function (event) {
    event.preventDefault();
    const btn = document.getElementById('btn-send');
    const result = document.getElementById('send-result');
    const form = new FormData(this);
    btn.disabled = true;
    btn.classList.add('opacity-70');
    result.classList.add('hidden');

    const data = await requestJson(WA_ROUTES.test, {
        method: 'POST',
        body: JSON.stringify({
            to: form.get('to'),
            message: form.get('message'),
        }),
    });

    result.textContent = data.message || (data.success ? 'Pesan berhasil dikirim.' : 'Pesan gagal dikirim.');
    result.className = `rounded-xl px-4 py-3 text-sm ${data.success ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100'}`;
    btn.disabled = false;
    btn.classList.remove('opacity-70');
});

refreshStatus();
waPoll = setInterval(refreshStatus, 5000);
window.addEventListener('beforeunload', () => clearInterval(waPoll));
</script>
@endpush
