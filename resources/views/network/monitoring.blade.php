@extends('layouts.app')
@section('title', 'Monitoring')
@section('page-title', 'Monitoring')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Monitoring Jaringan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Status real-time router & pelanggan PPPoE — <span id="last-update" class="text-gray-600 font-medium">belum dimuat</span></p>
    </div>
    <div class="flex items-center gap-2 self-start sm:self-auto">
        <label class="flex items-center gap-2 text-xs text-gray-500 bg-white border border-gray-100 rounded-xl px-3 py-2">
            <input type="checkbox" id="auto-refresh-toggle" class="accent-green-600 w-3.5 h-3.5" checked>
            <span>Auto-refresh <span id="countdown" class="font-mono text-gray-700">30</span>s</span>
        </label>
        <button onclick="doRefresh()" id="btn-refresh"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg id="refresh-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
        </button>
    </div>
</div>

{{-- Summary Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-5" id="summary-grid">
    @php
    $stats = [
        ['id'=>'s-routers',     'label'=>'Router',            'value'=>$routers->count(),                      'bg'=>'bg-gray-100',   'ic'=>'text-gray-500'],
        ['id'=>'s-r-online',    'label'=>'Router Online',     'value'=>$routers->where('status','online')->count(), 'bg'=>'bg-green-50',  'ic'=>'text-green-600'],
        ['id'=>'s-r-offline',   'label'=>'Router Offline',    'value'=>$routers->where('status','offline')->count(),'bg'=>'bg-red-50',    'ic'=>'text-red-500'],
        ['id'=>'s-pppoe',       'label'=>'PPPoE Aktif',       'value'=>$routers->sum('pppoe_online'),            'bg'=>'bg-blue-50',   'ic'=>'text-blue-600'],
        ['id'=>'s-mapped',      'label'=>'Mapped',            'value'=>$mappedCustomers->count(),                 'bg'=>'bg-indigo-50', 'ic'=>'text-indigo-600'],
        ['id'=>'s-c-online',    'label'=>'Cust. Online',      'value'=>'—',                                       'bg'=>'bg-emerald-50','ic'=>'text-emerald-600'],
        ['id'=>'s-c-offline',   'label'=>'Cust. Offline',     'value'=>'—',                                       'bg'=>'bg-amber-50',  'ic'=>'text-amber-600'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="bg-white rounded-2xl border border-gray-100 p-3.5 text-center">
        <p class="text-2xl font-bold text-gray-900" id="{{ $s['id'] }}">{{ $s['value'] }}</p>
        <p class="text-[10px] text-gray-500 mt-0.5">{{ $s['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Router Cards --}}
<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Router</p>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" id="router-grid">
    @forelse($routers as $r)
    <div class="router-card bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-md transition-shadow" data-id="{{ $r->id }}">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $r->status === 'online' ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $r->status === 'online' ? 'text-green-600' : 'text-red-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="2" y="6" width="20" height="8" rx="2"/><path stroke-linecap="round" d="M6 10h.01M10 10h.01M6 14v3M12 14v3M18 14v3"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm rc-name">{{ $r->name }}</p>
                    <p class="text-[10px] text-gray-400 font-mono">{{ $r->host }}</p>
                </div>
            </div>
            <span class="rc-status inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $r->status === 'online' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $r->status === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $r->status === 'online' ? 'Online' : 'Offline' }}
            </span>
        </div>
        {{-- Metrics --}}
        <div class="grid grid-cols-4 gap-2">
            <div class="rounded-xl bg-gray-50 p-2 text-center">
                <p class="text-sm font-bold text-gray-700 rc-cpu">—</p>
                <p class="text-[9px] text-gray-400">CPU</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-2 text-center">
                <p class="text-sm font-bold text-gray-700 rc-mem">—</p>
                <p class="text-[9px] text-gray-400">RAM</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-2 text-center">
                <p class="text-sm font-bold text-blue-600 rc-pppoe">{{ $r->pppoe_online }}</p>
                <p class="text-[9px] text-gray-400">PPPoE</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-2 text-center">
                <p class="text-sm font-bold text-gray-700 rc-uptime" title="">—</p>
                <p class="text-[9px] text-gray-400">Uptime</p>
            </div>
        </div>
        {{-- CPU bar --}}
        <div class="mt-3">
            <div class="flex items-center justify-between text-[9px] text-gray-400 mb-1">
                <span>CPU Load</span><span class="rc-cpu-val">—</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="rc-cpu-bar h-full bg-green-500 rounded-full transition-all duration-700" style="width:0%"></div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <p class="text-gray-500">Belum ada router terdaftar.</p>
    </div>
    @endforelse
</div>

{{-- Customer Monitoring --}}
<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">
    Pelanggan Ter-mapping
    <span class="text-gray-600 font-semibold ml-1" id="cust-count">({{ $mappedCustomers->count() }})</span>
</p>

{{-- Toolbar --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="cust-search" placeholder="Cari nama atau PPPoE..." class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1" oninput="filterCust()">
    </div>
    <div class="flex items-center gap-2 ml-auto">
        <select id="filter-cust-status" onchange="filterCust()" class="inp text-xs py-2 px-3">
            <option value="all">Semua</option>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Pelanggan</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">PPPoE User</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">IP Address</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Uptime</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Paket</th>
                </tr>
            </thead>
            <tbody id="cust-tbody">
                @forelse($mappedCustomers as $c)
                <tr class="cust-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                    data-id="{{ $c->id }}" data-pppoe="{{ $c->pppoe_user }}" data-online="0"
                    data-search="{{ strtolower($c->name . ' ' . $c->pppoe_user) }}">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center flex-shrink-0 cust-avatar">
                                <span class="text-white text-[10px] font-bold">{{ strtoupper(substr($c->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $c->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $c->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3"><span class="font-mono text-xs text-gray-700">{{ $c->pppoe_user }}</span></td>
                    <td class="px-4 py-3 text-center">
                        <span class="cust-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Menunggu...
                        </span>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell"><span class="cust-ip font-mono text-xs text-gray-400">—</span></td>
                    <td class="px-4 py-3 hidden lg:table-cell"><span class="cust-uptime text-xs text-gray-400">—</span></td>
                    <td class="px-4 py-3 hidden md:table-cell"><span class="text-xs text-gray-600">{{ $c->package?->name ?? '—' }}</span></td>
                </tr>
                @empty
                <tr id="cust-empty"><td colspan="6" class="px-5 py-16 text-center text-gray-400">Belum ada pelanggan yang di-mapping PPPoE. <a href="{{ route('pppoe-mapping.index') }}" class="text-green-600 underline">Mapping sekarang →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-5 right-5 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm">
        <svg id="toast-icon" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
        <span id="toast-msg"></span>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let refreshTimer = null;
let countdownTimer = null;
let countdown = 30;
let isRefreshing = false;

// ─── Toast ────────────────────────────────────────────────────────────────
let toastT;
function showToast(msg, type = 'success') {
    clearTimeout(toastT);
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-inner').className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    document.getElementById('toast').classList.remove('hidden');
    toastT = setTimeout(() => document.getElementById('toast').classList.add('hidden'), 3500);
}

// ─── Refresh ──────────────────────────────────────────────────────────────
async function doRefresh() {
    if (isRefreshing) return;
    isRefreshing = true;
    const btn = document.getElementById('btn-refresh');
    const icon = document.getElementById('refresh-icon');
    btn.disabled = true;
    icon.classList.add('animate-spin');

    try {
        const res = await fetch('/monitoring/refresh', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.success) { showToast('Gagal refresh: ' + (data.message || ''), 'error'); return; }

        updateSummary(data.summary);
        updateRouterCards(data.routers);
        updateCustomerRows(data.customers);
        document.getElementById('last-update').textContent = 'terakhir ' + data.timestamp;
        showToast('Data diperbarui', 'success');
    } catch (err) {
        showToast('Gagal refresh: ' + err.message, 'error');
    } finally {
        isRefreshing = false;
        btn.disabled = false;
        icon.classList.remove('animate-spin');
        resetCountdown();
    }
}

function updateSummary(s) {
    document.getElementById('s-routers').textContent    = s.total_routers;
    document.getElementById('s-r-online').textContent   = s.online_routers;
    document.getElementById('s-r-offline').textContent  = s.offline_routers;
    document.getElementById('s-pppoe').textContent      = s.total_pppoe;
    document.getElementById('s-mapped').textContent     = s.total_mapped;
    document.getElementById('s-c-online').textContent   = s.online_customers;
    document.getElementById('s-c-offline').textContent  = s.offline_customers;
}

function updateRouterCards(routers) {
    routers.forEach(r => {
        const card = document.querySelector(`.router-card[data-id="${r.id}"]`);
        if (!card) return;

        const isOnline = r.status === 'online';
        const iconWrap = card.querySelector('.w-10');
        if (iconWrap) {
            iconWrap.className = `w-10 h-10 rounded-xl ${isOnline ? 'bg-green-50' : 'bg-red-50'} flex items-center justify-center flex-shrink-0`;
            iconWrap.querySelector('svg').className = `w-5 h-5 ${isOnline ? 'text-green-600' : 'text-red-400'}`;
        }

        const badge = card.querySelector('.rc-status');
        if (badge) {
            badge.className = `rc-status inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold ${isOnline ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`;
            badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'}"></span>${isOnline ? 'Online' : 'Offline'}`;
        }

        card.querySelector('.rc-cpu').textContent   = isOnline ? r.cpu_load + '%' : '—';
        card.querySelector('.rc-mem').textContent    = isOnline ? r.mem_pct + '%' : '—';
        card.querySelector('.rc-pppoe').textContent  = r.pppoe_online;
        card.querySelector('.rc-cpu-val').textContent = isOnline ? r.cpu_load + '%' : '—';

        // Uptime: shorten display
        const ut = r.uptime ?? '—';
        card.querySelector('.rc-uptime').textContent = shortenUptime(ut);
        card.querySelector('.rc-uptime').title = ut;

        // CPU bar
        const bar = card.querySelector('.rc-cpu-bar');
        const cpuPct = isOnline ? r.cpu_load : 0;
        bar.style.width = cpuPct + '%';
        bar.className = `rc-cpu-bar h-full rounded-full transition-all duration-700 ${cpuPct > 80 ? 'bg-red-500' : cpuPct > 50 ? 'bg-amber-400' : 'bg-green-500'}`;
    });
}

function updateCustomerRows(customers) {
    // Build lookup
    const lookup = {};
    customers.forEach(c => { lookup[c.pppoe_user] = c; });

    document.querySelectorAll('.cust-row').forEach(row => {
        const pppoe = row.dataset.pppoe;
        const cData = lookup[pppoe];
        const isOnline = cData?.online ?? false;

        row.dataset.online = isOnline ? '1' : '0';

        const badge = row.querySelector('.cust-status');
        badge.className = `cust-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${isOnline ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`;
        badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'}"></span>${isOnline ? 'Online' : 'Offline'}`;

        const avatar = row.querySelector('.cust-avatar');
        avatar.className = `w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 cust-avatar bg-gradient-to-br ${isOnline ? 'from-green-400 to-green-600' : 'from-gray-300 to-gray-400'}`;

        row.querySelector('.cust-ip').textContent     = cData?.ip || '—';
        row.querySelector('.cust-uptime').textContent  = cData?.uptime || '—';
    });
}

function shortenUptime(ut) {
    if (!ut || ut === '—') return '—';
    // "1w2d3h4m5s" → "1w2d"
    const match = ut.match(/(\d+w)?(\d+d)?(\d+h)?(\d+m)?/);
    if (!match) return ut;
    const parts = [match[1], match[2], match[3]].filter(Boolean);
    return parts.length ? parts.slice(0, 2).join('') : ut.replace(/s$/, '') ;
}

// ─── Filter ───────────────────────────────────────────────────────────────
function filterCust() {
    const q = document.getElementById('cust-search').value.toLowerCase();
    const status = document.getElementById('filter-cust-status').value;
    document.querySelectorAll('.cust-row').forEach(r => {
        const matchQ = r.dataset.search.includes(q);
        let matchS = true;
        if (status === 'online') matchS = r.dataset.online === '1';
        else if (status === 'offline') matchS = r.dataset.online === '0';
        r.style.display = matchQ && matchS ? '' : 'none';
    });
}

// ─── Auto-refresh ─────────────────────────────────────────────────────────
function resetCountdown() {
    countdown = 30;
    document.getElementById('countdown').textContent = countdown;
}

function startAutoRefresh() {
    countdownTimer = setInterval(() => {
        if (!document.getElementById('auto-refresh-toggle').checked) return;
        countdown--;
        document.getElementById('countdown').textContent = countdown;
        if (countdown <= 0) { doRefresh(); }
    }, 1000);
}

document.getElementById('auto-refresh-toggle').addEventListener('change', (e) => {
    if (e.target.checked) resetCountdown();
});

// Init: auto-refresh on page load
startAutoRefresh();
doRefresh();
</script>

@endsection
