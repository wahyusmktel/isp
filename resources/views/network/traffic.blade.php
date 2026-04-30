@extends('layouts.app')
@section('title', 'Monitoring Trafik')
@section('page-title', 'Monitoring Trafik')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Monitoring Trafik</h1>
        <p class="text-sm text-gray-400 mt-0.5">Pantau kecepatan internet pelanggan secara real-time — <span id="last-ts" class="text-gray-600 font-medium">belum dimuat</span></p>
    </div>
    <div class="flex items-center gap-2 self-start sm:self-auto">
        <label class="flex items-center gap-2 text-xs text-gray-500 bg-white border border-gray-100 rounded-xl px-3 py-2">
            <input type="checkbox" id="auto-toggle" class="accent-green-600 w-3.5 h-3.5" checked>
            <span>Auto <span id="cd" class="font-mono text-gray-700">5</span>s</span>
        </label>
        <button onclick="doFetch()" id="btn-fetch"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg id="fetch-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
        </button>
    </div>
</div>

{{-- Router Selector --}}
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-5">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Router</p>
            <select id="router-select" class="inp w-full sm:w-72" onchange="doFetch()">
                @foreach($routers as $r)
                <option value="{{ $r->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $r->name }} ({{ $r->host }})</option>
                @endforeach
                @if($routers->isEmpty())
                <option value="" disabled>Tidak ada router online</option>
                @endif
            </select>
        </div>
        {{-- Summary cards inline --}}
        <div class="flex items-center gap-3 ml-auto flex-wrap">
            <div class="bg-blue-50 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-base font-bold text-blue-700" id="s-dl">0</p>
                <p class="text-[9px] text-blue-400">↓ Download</p>
            </div>
            <div class="bg-emerald-50 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-base font-bold text-emerald-700" id="s-ul">0</p>
                <p class="text-[9px] text-emerald-400">↑ Upload</p>
            </div>
            <div class="bg-gray-100 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-base font-bold text-gray-700" id="s-queues">0</p>
                <p class="text-[9px] text-gray-400">Queue Aktif</p>
            </div>
            <div class="bg-orange-50 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-base font-bold text-orange-600" id="s-cpu">—</p>
                <p class="text-[9px] text-orange-400">CPU Router</p>
            </div>
        </div>
    </div>
</div>

{{-- Live Interface Monitor: ether4-Internet_In --}}
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-5" id="iface-monitor">
    <div class="flex flex-col lg:flex-row lg:items-start gap-4">
        {{-- Chart column --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Live Interface</p>
                    <p class="text-sm font-bold text-gray-800 font-mono">ether4-Internet_In</p>
                </div>
                <span id="iface-status" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                    <span id="iface-dot" class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                    <span id="iface-status-txt">Menghubungkan...</span>
                </span>
            </div>
            {{-- Sparkline SVG --}}
            <div class="relative h-24 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
                <svg id="iface-chart" class="w-full h-full absolute inset-0" preserveAspectRatio="none" viewBox="0 0 300 96">
                    <defs>
                        <linearGradient id="dlGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="ulGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.20"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    {{-- Grid lines --}}
                    <line x1="0" y1="32" x2="300" y2="32" stroke="#e5e7eb" stroke-width="0.5"/>
                    <line x1="0" y1="64" x2="300" y2="64" stroke="#e5e7eb" stroke-width="0.5"/>
                    {{-- Fill areas --}}
                    <polygon id="dl-fill" points="" fill="url(#dlGrad)"/>
                    <polygon id="ul-fill" points="" fill="url(#ulGrad)"/>
                    {{-- Lines --}}
                    <polyline id="dl-line" points="" fill="none" stroke="#3b82f6" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/>
                    <polyline id="ul-line" points="" fill="none" stroke="#10b981" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/>
                </svg>
                {{-- Legend overlay --}}
                <div class="absolute top-2 right-2.5 flex items-center gap-3 text-[9px] text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-blue-500 inline-block rounded-full"></span> Download
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-emerald-500 inline-block rounded-full"></span> Upload
                    </span>
                </div>
                {{-- Scale hint --}}
                <span id="iface-scale" class="absolute bottom-1.5 left-2.5 text-[9px] text-gray-400 font-mono"></span>
            </div>
        </div>

        {{-- Stats column --}}
        <div class="grid grid-cols-2 lg:grid-cols-1 gap-2.5 lg:min-w-[180px]">
            <div class="bg-blue-50 rounded-xl px-4 py-2.5">
                <p class="text-[9px] text-blue-400 uppercase tracking-wide mb-0.5">↓ Download</p>
                <p class="text-base font-bold text-blue-700 font-mono leading-none" id="iface-dl">—</p>
            </div>
            <div class="bg-emerald-50 rounded-xl px-4 py-2.5">
                <p class="text-[9px] text-emerald-400 uppercase tracking-wide mb-0.5">↑ Upload</p>
                <p class="text-base font-bold text-emerald-700 font-mono leading-none" id="iface-ul">—</p>
            </div>
            <div class="bg-indigo-50 rounded-xl px-4 py-2.5">
                <p class="text-[9px] text-indigo-400 uppercase tracking-wide mb-0.5">Uptime Router</p>
                <p class="text-sm font-bold text-indigo-700 font-mono leading-none" id="iface-uptime">—</p>
            </div>
            <div class="bg-gray-50 rounded-xl px-4 py-2.5 border border-gray-100">
                <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Total Bandwidth</p>
                <p class="text-xs font-semibold text-blue-600 leading-snug" id="iface-rx">— RX</p>
                <p class="text-xs font-semibold text-emerald-600 leading-snug" id="iface-tx">— TX</p>
            </div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="q-search" placeholder="Cari pelanggan atau queue..." class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1" oninput="filterRows()">
    </div>
    <div class="flex items-center gap-2 ml-auto">
        <select id="sort-by" onchange="renderTable()" class="inp text-xs py-2 px-3">
            <option value="download">↓ Download Tertinggi</option>
            <option value="upload">↑ Upload Tertinggi</option>
            <option value="name">Nama A-Z</option>
        </select>
    </div>
</div>

{{-- Traffic Table --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" id="traffic-panel">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Pelanggan / Queue</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Status</th>
                    <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">
                        <span class="text-blue-500">↓</span> Download
                    </th>
                    <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">
                        <span class="text-emerald-500">↑</span> Upload
                    </th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Bandwidth</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">IP / Uptime</th>
                </tr>
            </thead>
            <tbody id="traffic-tbody">
                <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                        </div>
                        <p class="text-gray-500 font-medium">Memuat data trafik...</p>
                    </div>
                </td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-5 right-5 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm">
        <span id="toast-msg"></span>
    </div>
</div>

<script>
let TRAFFIC = [];
let isFetching = false;
let cdVal = 5;
let cdTimer = null;

function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-inner').className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}

// ─── Format helpers ───────────────────────────────────────────────────────
function fmtSpeed(bps) {
    if (bps <= 0) return '0 bps';
    if (bps >= 1000000) return (bps / 1000000).toFixed(2) + ' Mbps';
    if (bps >= 1000) return (bps / 1000).toFixed(1) + ' Kbps';
    return bps + ' bps';
}
function fmtBytes(b) {
    if (b <= 0) return '0 B';
    if (b >= 1073741824) return (b / 1073741824).toFixed(2) + ' GB';
    if (b >= 1048576) return (b / 1048576).toFixed(1) + ' MB';
    if (b >= 1024) return (b / 1024).toFixed(0) + ' KB';
    return b + ' B';
}
function pct(val, max) { return max > 0 ? Math.min(Math.round((val / max) * 100), 100) : 0; }
function barColor(p) { return p > 80 ? 'bg-red-500' : p > 60 ? 'bg-amber-400' : p > 30 ? 'bg-blue-500' : 'bg-blue-400'; }

// ─── Fetch traffic ────────────────────────────────────────────────────────
async function doFetch() {
    const rid = document.getElementById('router-select').value;
    if (!rid) return;
    if (isFetching) return;
    isFetching = true;
    document.getElementById('fetch-icon').classList.add('animate-spin');

    try {
        const res = await fetch(`/traffic/${rid}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.success) { showToast(data.message, 'error'); return; }

        TRAFFIC = data.traffic;
        updateSummary(data.summary);
        renderTable();
        document.getElementById('last-ts').textContent = 'terakhir ' + data.timestamp;
    } catch (err) {
        showToast('Gagal: ' + err.message, 'error');
    } finally {
        isFetching = false;
        document.getElementById('fetch-icon').classList.remove('animate-spin');
        cdVal = 5;
        document.getElementById('cd').textContent = cdVal;
    }
}

function updateSummary(s) {
    document.getElementById('s-dl').textContent     = fmtSpeed(s.total_download);
    document.getElementById('s-ul').textContent     = fmtSpeed(s.total_upload);
    document.getElementById('s-queues').textContent = s.active_queues + '/' + s.total_queues;
    document.getElementById('s-cpu').textContent    = s.cpu_load + '%';
}

function renderTable() {
    const tbody = document.getElementById('traffic-tbody');
    const sort = document.getElementById('sort-by').value;

    let sorted = [...TRAFFIC];
    if (sort === 'download') sorted.sort((a, b) => b.download_bps - a.download_bps);
    else if (sort === 'upload') sorted.sort((a, b) => b.upload_bps - a.upload_bps);
    else sorted.sort((a, b) => (a.customer_name || a.queue_name).localeCompare(b.customer_name || b.queue_name));

    if (!sorted.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">Tidak ada queue ditemukan di router ini</td></tr>';
        return;
    }

    tbody.innerHTML = sorted.map(t => {
        const dlPct = pct(t.download_bps, t.max_download);
        const ulPct = pct(t.upload_bps, t.max_upload);
        const hasTraffic = t.download_bps > 0 || t.upload_bps > 0;
        const displayName = t.customer_name || t.pppoe_user || t.queue_name;
        const subtitle = t.customer_name ? t.pppoe_user : t.queue_name;

        return `<tr class="traf-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                    data-search="${(displayName + ' ' + subtitle).toLowerCase()}">
            <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl ${hasTraffic ? 'bg-blue-50' : 'bg-gray-100'} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 ${hasTraffic ? 'text-blue-600' : 'text-gray-400'}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">${displayName}</p>
                        <p class="text-[10px] text-gray-400 font-mono">${subtitle}${t.package_name ? ' · ' + t.package_name : ''}</p>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold ${hasTraffic ? 'bg-green-100 text-green-700' : t.online ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500'}">
                    <span class="w-1.5 h-1.5 rounded-full ${hasTraffic ? 'bg-green-500 animate-pulse' : t.online ? 'bg-blue-400' : 'bg-gray-400'}"></span>
                    ${hasTraffic ? 'Aktif' : t.online ? 'Idle' : 'Offline'}
                </span>
            </td>
            <td class="px-4 py-3 text-right">
                <p class="font-bold ${t.download_bps > 0 ? 'text-blue-600' : 'text-gray-400'} text-sm">${fmtSpeed(t.download_bps)}</p>
                <p class="text-[10px] text-gray-400">maks ${fmtSpeed(t.max_download)}</p>
            </td>
            <td class="px-4 py-3 text-right">
                <p class="font-bold ${t.upload_bps > 0 ? 'text-emerald-600' : 'text-gray-400'} text-sm">${fmtSpeed(t.upload_bps)}</p>
                <p class="text-[10px] text-gray-400">maks ${fmtSpeed(t.max_upload)}</p>
            </td>
            <td class="px-4 py-3 hidden lg:table-cell">
                <div class="w-full max-w-[140px] space-y-1.5">
                    <div>
                        <div class="flex items-center justify-between text-[9px] mb-0.5">
                            <span class="text-blue-500">↓ ${dlPct}%</span>
                            <span class="text-gray-400">${fmtBytes(t.bytes_down)}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full ${barColor(dlPct)} rounded-full transition-all duration-500" style="width:${dlPct}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-[9px] mb-0.5">
                            <span class="text-emerald-500">↑ ${ulPct}%</span>
                            <span class="text-gray-400">${fmtBytes(t.bytes_up)}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-400 rounded-full transition-all duration-500" style="width:${ulPct}%"></div>
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3 hidden md:table-cell">
                <p class="font-mono text-xs text-gray-600">${t.ip || '—'}</p>
                <p class="text-[10px] text-gray-400">${t.uptime || '—'}</p>
            </td>
        </tr>`;
    }).join('');
}

function filterRows() {
    const q = document.getElementById('q-search').value.toLowerCase();
    document.querySelectorAll('.traf-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q) ? '' : 'none';
    });
}

// Auto-refresh
function startAuto() {
    cdTimer = setInterval(() => {
        if (!document.getElementById('auto-toggle').checked) return;
        cdVal--;
        document.getElementById('cd').textContent = cdVal;
        if (cdVal <= 0) doFetch();
    }, 1000);
}

document.getElementById('auto-toggle').addEventListener('change', e => {
    if (e.target.checked) { cdVal = 5; document.getElementById('cd').textContent = cdVal; }
});

startAuto();
if (document.getElementById('router-select').value) doFetch();

// ─── Interface live monitor ───────────────────────────────────────────────
const CHART_POINTS = 30;
const CHART_W = 300, CHART_H = 96;
let ifaceDl = new Array(CHART_POINTS).fill(0);
let ifaceUl = new Array(CHART_POINTS).fill(0);

function setIfaceStatus(ok) {
    const dot = document.getElementById('iface-dot');
    const txt = document.getElementById('iface-status-txt');
    const wrap = document.getElementById('iface-status');
    if (ok) {
        dot.className = 'w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block';
        txt.textContent = 'Live';
        wrap.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-green-100 text-green-700';
    } else {
        dot.className = 'w-1.5 h-1.5 rounded-full bg-red-400 inline-block';
        txt.textContent = 'Offline';
        wrap.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-50 text-red-500';
    }
}

function buildSparkPoints(arr, maxVal) {
    if (maxVal <= 0) maxVal = 1;
    const step = CHART_W / (CHART_POINTS - 1);
    return arr.map((v, i) => {
        const x = i * step;
        const y = CHART_H - 4 - ((v / maxVal) * (CHART_H - 12));
        return x + ',' + y;
    }).join(' ');
}

function buildFillPoints(arr, maxVal) {
    if (maxVal <= 0) maxVal = 1;
    const step = CHART_W / (CHART_POINTS - 1);
    const top = arr.map((v, i) => {
        const x = i * step;
        const y = CHART_H - 4 - ((v / maxVal) * (CHART_H - 12));
        return x + ',' + y;
    });
    const bottom = [
        (CHART_W) + ',' + (CHART_H),
        '0,' + (CHART_H),
    ];
    return [...top, ...bottom].join(' ');
}

function updateIfaceChart() {
    const maxVal = Math.max(...ifaceDl, ...ifaceUl, 1);
    document.getElementById('dl-line').setAttribute('points', buildSparkPoints(ifaceDl, maxVal));
    document.getElementById('ul-line').setAttribute('points', buildSparkPoints(ifaceUl, maxVal));
    document.getElementById('dl-fill').setAttribute('points', buildFillPoints(ifaceDl, maxVal));
    document.getElementById('ul-fill').setAttribute('points', buildFillPoints(ifaceUl, maxVal));

    // Show scale hint
    const scaleEl = document.getElementById('iface-scale');
    scaleEl.textContent = 'maks ' + fmtSpeed(maxVal);
}

async function fetchIfaceStats() {
    const rid = document.getElementById('router-select').value;
    if (!rid) return;
    try {
        const res = await fetch(`/traffic/${rid}/interface`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.success) { setIfaceStatus(false); return; }

        setIfaceStatus(true);
        ifaceDl.push(data.download_bps); ifaceDl.shift();
        ifaceUl.push(data.upload_bps);   ifaceUl.shift();

        document.getElementById('iface-dl').textContent    = fmtSpeed(data.download_bps);
        document.getElementById('iface-ul').textContent    = fmtSpeed(data.upload_bps);
        document.getElementById('iface-uptime').textContent = data.uptime || '—';
        document.getElementById('iface-rx').textContent    = fmtBytes(data.rx_byte) + ' RX';
        document.getElementById('iface-tx').textContent    = fmtBytes(data.tx_byte) + ' TX';

        updateIfaceChart();
    } catch (e) {
        setIfaceStatus(false);
    }
}

// Sync with router select changes
document.getElementById('router-select').addEventListener('change', () => {
    ifaceDl = new Array(CHART_POINTS).fill(0);
    ifaceUl = new Array(CHART_POINTS).fill(0);
    updateIfaceChart();
    fetchIfaceStats();
});

// Fetch every 5 seconds alongside main traffic
setInterval(fetchIfaceStats, 5000);
if (document.getElementById('router-select').value) fetchIfaceStats();
</script>

@endsection
