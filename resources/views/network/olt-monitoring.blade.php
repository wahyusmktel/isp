@extends('layouts.app')
@section('title', 'Monitoring OLT')
@section('page-title', 'Monitoring OLT')

@php
$fmt = fn($value, $fallback = '-') => filled($value ?? null) ? $value : $fallback;
$statusClass = function ($status) {
    $s = strtolower(trim((string) $status));
    if ($s !== '' && !str_contains($s, 'offline') && !str_contains($s, 'down') && !str_contains($s, 'los') && !str_contains($s, 'timeout') && !str_contains($s, 'dying')) {
        return 'bg-green-50 text-green-700';
    }
    return 'bg-red-50 text-red-700';
};
$rxBadge = function ($rx) {
    if (!preg_match('/-?\d+(?:\.\d+)?/', (string) $rx, $match)) {
        return [
            'label' => 'Tidak terbaca',
            'class' => 'bg-gray-100 text-gray-600 border-gray-200',
        ];
    }

    $value = (float) $match[0];

    if ($value >= -22) {
        return [
            'label' => 'Excellent',
            'class' => 'bg-green-700 text-white border-green-700',
        ];
    }

    if ($value < -22 && $value >= -25) {
        return [
            'label' => 'Good',
            'class' => 'bg-green-100 text-green-800 border-green-200',
        ];
    }

    if ($value < -25) {
        return [
            'label' => 'Critical',
            'class' => 'bg-red-100 text-red-700 border-red-200',
        ];
    }

    return [
        'label' => 'Critical',
        'class' => 'bg-red-100 text-red-700 border-red-200',
    ];
};
@endphp

@section('content')
<div class="space-y-5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Monitoring OLT</h1>
            <p class="text-sm text-gray-400 mt-0.5">Daftar seluruh client ONU/ONT dari OLT HisFocus yang sudah terhubung.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('network.olt-monitoring.export') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('settings', ['tab' => 'jaringan']) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                Setting OLT
            </a>
            <button type="button" onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5M19 5h-5M5 19h5"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    @if(!($result['success'] ?? false))
    <div class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-bold">Gagal membaca data OLT</p>
        <p class="mt-1">{{ $result['message'] ?? 'Tidak diketahui.' }}</p>
        @foreach(($result['errors'] ?? []) as $error)
        <p class="text-xs mt-1 font-mono">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php
        $cards = [
            ['label' => 'Total ONU', 'value' => $stats['total'], 'sub' => 'dari OLT', 'bg' => 'bg-slate-50', 'text' => 'text-slate-700'],
            ['label' => 'Online', 'value' => $stats['online'], 'sub' => 'status aktif', 'bg' => 'bg-green-50', 'text' => 'text-green-700'],
            ['label' => 'Offline', 'value' => $stats['offline'], 'sub' => 'butuh cek', 'bg' => 'bg-red-50', 'text' => 'text-red-700'],
            ['label' => 'Mapped', 'value' => $stats['mapped'], 'sub' => 'ke pelanggan', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700'],
            ['label' => 'Rx Warning', 'value' => $stats['rx_warning'], 'sub' => 'di luar normal', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700'],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white border border-gray-100 rounded-2xl p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold {{ $card['text'] }} mt-2">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-sm font-bold text-gray-900">Visual Port Redaman Optik</h2>
                <p class="text-xs text-gray-400 mt-0.5">Klik port untuk melihat detail ONU/ONT dan distribusi redaman.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-[11px]">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-700 text-white font-semibold"><span class="w-2 h-2 rounded-full bg-white"></span>Excellent</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 text-green-800 font-semibold"><span class="w-2 h-2 rounded-full bg-green-500"></span>Good</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-semibold"><span class="w-2 h-2 rounded-full bg-red-500"></span>Critical</span>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
            @forelse($portGroups as $group)
            @php
            $dominant = $group['critical'] > 0 ? 'critical' : ($group['excellent'] >= $group['good'] ? 'excellent' : 'good');
            $portClass = match($dominant) {
                'critical' => 'border-red-200 bg-red-50 hover:bg-red-100 text-red-700',
                'excellent' => 'border-green-700 bg-green-700 hover:bg-green-800 text-white',
                default => 'border-green-200 bg-green-50 hover:bg-green-100 text-green-800',
            };
            @endphp
            <button type="button"
                    onclick='openPortDetail(@json($group))'
                    class="text-left rounded-2xl border p-4 transition-colors {{ $portClass }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide opacity-75">{{ $group['label'] }}</p>
                        <p class="text-2xl font-black mt-1">{{ $group['total'] }}</p>
                        <p class="text-xs opacity-75">{{ $group['online'] }} online · Avg Rx {{ $group['avg_rx'] ?: '-' }} dBm</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-white/25 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h5m6 0h5M9 12a3 3 0 106 0 3 3 0 00-6 0zM12 4v5m0 6v5"/>
                        </svg>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                    <div class="rounded-xl bg-white/35 px-2 py-2"><p class="text-sm font-black">{{ $group['excellent'] }}</p><p class="text-[10px] opacity-75">Excellent</p></div>
                    <div class="rounded-xl bg-white/35 px-2 py-2"><p class="text-sm font-black">{{ $group['good'] }}</p><p class="text-[10px] opacity-75">Good</p></div>
                    <div class="rounded-xl bg-white/35 px-2 py-2"><p class="text-sm font-black">{{ $group['critical'] }}</p><p class="text-[10px] opacity-75">Critical</p></div>
                </div>
            </button>
            @empty
            <div class="col-span-full rounded-2xl border border-gray-100 bg-gray-50 p-6 text-center">
                <p class="text-sm font-semibold text-gray-500">Belum ada port yang dapat divisualisasikan.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="flex-1">
                <h2 class="text-sm font-bold text-gray-900">Client ONU / ONT</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Source: {{ $result['base_url'] ?? '-' }}
                    @foreach(($result['sources'] ?? []) as $source)
                    <span class="ml-2 font-mono">{{ $source['url'] }} ({{ $source['count'] }})</span>
                    @endforeach
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <input id="olt-search" type="text" oninput="filterOltRows()" placeholder="Cari ID, nama, MAC, pelanggan..."
                       class="w-full sm:w-72 px-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500 focus:bg-white">
                <select id="olt-status-filter" onchange="filterOltRows()"
                        class="px-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500">
                    <option value="">Semua status</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="mapped">Mapped</option>
                    <option value="unmapped">Belum mapped</option>
                </select>
                <select id="olt-rx-filter" onchange="filterOltRows()"
                        class="px-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 outline-none focus:border-green-500">
                    <option value="">Semua redaman</option>
                    <option value="excellent">Excellent (&gt;= -22)</option>
                    <option value="good">Good (&lt; -22 s/d -25)</option>
                    <option value="critical">Critical (&lt; -25)</option>
                    <option value="unknown">Tidak terbaca</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px]">
                <thead class="bg-gray-50/70 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-400 px-5 py-3">ONU</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Pelanggan</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">MAC / Chip</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Optik</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Port / Versi</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Runtime</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Register</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Deregister</th>
                        <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Source</th>
                    </tr>
                </thead>
                <tbody id="olt-tbody" class="divide-y divide-gray-50">
                    @forelse($clients as $client)
                    @php
                    $customer = $client['_customer'] ?? null;
                    $isOnline = strtolower((string)($client['status'] ?? '')) !== '' && !str_contains(strtolower((string)($client['status'] ?? '')), 'offline') && !str_contains(strtolower((string)($client['status'] ?? '')), 'down') && !str_contains(strtolower((string)($client['status'] ?? '')), 'timeout') && !str_contains(strtolower((string)($client['status'] ?? '')), 'dying');
                    $search = strtolower(implode(' ', array_filter([
                        $client['id'] ?? '',
                        $client['name'] ?? '',
                        $client['mac_address'] ?? '',
                        $client['chip_id'] ?? '',
                        $customer['name'] ?? '',
                        $customer['pppoe_user'] ?? '',
                        $customer['ip_address'] ?? '',
                    ])));
                    @endphp
                    <tr data-row
                        data-search="{{ e($search) }}"
                        data-status="{{ $isOnline ? 'online' : 'offline' }}"
                        data-mapped="{{ $customer ? 'mapped' : 'unmapped' }}"
                        data-rx="{{ $client['_rx_category'] ?? 'unknown' }}"
                        class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-gray-900 font-mono">{{ $fmt($client['id'] ?? null) }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $fmt($client['name'] ?? null, 'Tanpa nama') }}</p>
                        </td>
                        <td class="px-4 py-4">
                            @if($customer)
                            <a href="{{ route('customers.show', $customer['id']) }}" class="text-sm font-semibold text-blue-600 hover:underline">{{ $customer['name'] }}</a>
                            <p class="text-xs text-gray-400 font-mono">{{ $fmt($customer['pppoe_user'] ?? null) }} / {{ $fmt($customer['ip_address'] ?? null) }}</p>
                            @else
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Belum mapped</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs font-mono text-gray-800">{{ $fmt($client['mac_address'] ?? $client['macaddress'] ?? null) }}</p>
                            <p class="text-xs font-mono text-gray-400">{{ $fmt($client['chip_id'] ?? null) }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass($client['status'] ?? null) }}">
                                {{ $fmt($client['status'] ?? null) }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">RTT: {{ $fmt($client['rtt'] ?? null) }}</p>
                        </td>
                        <td class="px-4 py-4">
                            @php $rx = $rxBadge($client['rx_power'] ?? null); @endphp
                            <div class="inline-flex items-center gap-2 rounded-xl border px-2.5 py-1.5 {{ $rx['class'] }}">
                                <span class="text-[10px] font-bold uppercase tracking-wide">{{ $rx['label'] }}</span>
                                <span class="text-xs font-mono font-bold">{{ $fmt($client['rx_power'] ?? null) }}</span>
                            </div>
                            <p class="text-xs text-gray-600">Tx: <span class="font-semibold">{{ $fmt($client['tx_power'] ?? null) }}</span></p>
                            <p class="text-xs text-gray-400">Temp: {{ $fmt($client['temperature'] ?? null) }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs text-gray-700">Ports: {{ $fmt($client['ports'] ?? null) }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $fmt($client['version'] ?? null) }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs font-mono text-gray-700">{{ $fmt($client['running_time_seconds'] ?? null) }}</p>
                            <p class="text-xs text-gray-400">detik</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs text-gray-700 whitespace-nowrap">{{ $fmt($client['register_time'] ?? null) }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs text-gray-700 whitespace-nowrap">{{ $fmt($client['deregister_time'] ?? null) }}</p>
                            <p class="text-xs text-gray-400">{{ $fmt($client['offline_reason'] ?? null) }} / {{ $fmt($client['deregister_count'] ?? null, '0') }}x</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs font-mono text-gray-500">{{ $fmt($client['_source_url'] ?? null) }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-16 text-center">
                            <p class="text-sm font-semibold text-gray-500">Belum ada data client OLT.</p>
                            <p class="text-xs text-gray-400 mt-1">Cek konfigurasi OLT di Settings &gt; Informasi Jaringan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-gray-50 text-xs text-gray-500">
            Menampilkan <span id="olt-visible">{{ $clients->count() }}</span> dari {{ $clients->count() }} client.
        </div>
    </div>
</div>

<div id="port-detail-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePortDetail()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="port-detail-card" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[88vh] overflow-hidden flex flex-col scale-95 opacity-0 transition-all duration-200">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <h2 id="port-detail-title" class="text-base font-bold text-gray-900">Detail Port</h2>
                    <p id="port-detail-subtitle" class="text-xs text-gray-400 mt-0.5">-</p>
                </div>
                <button type="button" onclick="closePortDetail()" class="w-9 h-9 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto">
                <div id="port-detail-summary" class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-5"></div>
                <div class="overflow-x-auto rounded-2xl border border-gray-100">
                    <table class="w-full min-w-[900px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">ONU</th>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Pelanggan</th>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">MAC</th>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Status</th>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Rx</th>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Tx / Temp</th>
                                <th class="text-left text-xs font-semibold text-gray-400 px-4 py-3">Register</th>
                            </tr>
                        </thead>
                        <tbody id="port-detail-rows" class="divide-y divide-gray-50"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterOltRows() {
    const q = document.getElementById('olt-search').value.trim().toLowerCase();
    const filter = document.getElementById('olt-status-filter').value;
    const rxFilter = document.getElementById('olt-rx-filter').value;
    let visible = 0;

    document.querySelectorAll('#olt-tbody [data-row]').forEach(row => {
        const matchSearch = !q || row.dataset.search.includes(q);
        const matchFilter = !filter || row.dataset.status === filter || row.dataset.mapped === filter;
        const matchRx = !rxFilter || row.dataset.rx === rxFilter;
        const show = matchSearch && matchFilter && matchRx;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('olt-visible').textContent = visible;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
}

function rxBadgeClass(category) {
    if (category === 'excellent') return 'bg-green-700 text-white';
    if (category === 'good') return 'bg-green-100 text-green-800';
    if (category === 'critical') return 'bg-red-100 text-red-700';
    return 'bg-gray-100 text-gray-600';
}

function openPortDetail(group) {
    const modal = document.getElementById('port-detail-modal');
    const card = document.getElementById('port-detail-card');
    document.getElementById('port-detail-title').textContent = group.label;
    document.getElementById('port-detail-subtitle').textContent = `${group.total} ONU/ONT · ${group.online} online · Avg Rx ${group.avg_rx || '-'} dBm`;
    document.getElementById('port-detail-summary').innerHTML = [
        ['Total', group.total, 'bg-slate-50 text-slate-700'],
        ['Online', group.online, 'bg-green-50 text-green-700'],
        ['Excellent', group.excellent, 'bg-green-700 text-white'],
        ['Good', group.good, 'bg-green-50 text-green-700'],
        ['Critical', group.critical, 'bg-red-50 text-red-700'],
    ].map(([label, value, cls]) => `
        <div class="rounded-2xl p-4 ${cls}">
            <p class="text-xs font-bold uppercase tracking-wide opacity-75">${label}</p>
            <p class="text-2xl font-black mt-1">${value}</p>
        </div>
    `).join('');

    document.getElementById('port-detail-rows').innerHTML = (group.clients || []).map(client => {
        const customer = client._customer;
        return `
            <tr>
                <td class="px-4 py-3"><p class="text-sm font-bold font-mono text-gray-900">${escapeHtml(client.id || '-')}</p><p class="text-xs text-gray-400">${escapeHtml(client.name || 'Tanpa nama')}</p></td>
                <td class="px-4 py-3">${customer ? `<p class="text-sm font-semibold text-blue-600">${escapeHtml(customer.name)}</p><p class="text-xs font-mono text-gray-400">${escapeHtml(customer.pppoe_user || '-')} / ${escapeHtml(customer.ip_address || '-')}</p>` : '<span class="text-xs font-semibold text-gray-500 bg-gray-100 rounded-full px-2.5 py-1">Belum mapped</span>'}</td>
                <td class="px-4 py-3"><p class="text-xs font-mono text-gray-700">${escapeHtml(client.mac_address || client.macaddress || '-')}</p><p class="text-xs font-mono text-gray-400">${escapeHtml(client.chip_id || '-')}</p></td>
                <td class="px-4 py-3"><span class="text-xs font-semibold rounded-full px-2.5 py-1 ${client._is_online ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">${escapeHtml(client.status || '-')}</span></td>
                <td class="px-4 py-3"><span class="text-xs font-bold rounded-full px-2.5 py-1 ${rxBadgeClass(client._rx_category)}">${escapeHtml(client._rx_label || '-')} · ${escapeHtml(client.rx_power || '-')}</span></td>
                <td class="px-4 py-3"><p class="text-xs text-gray-700">Tx: <b>${escapeHtml(client.tx_power || '-')}</b></p><p class="text-xs text-gray-400">Temp: ${escapeHtml(client.temperature || '-')}</p></td>
                <td class="px-4 py-3"><p class="text-xs text-gray-700">${escapeHtml(client.register_time || '-')}</p><p class="text-xs text-gray-400">${escapeHtml(client.offline_reason || '')}</p></td>
            </tr>
        `;
    }).join('');

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }));
}

function closePortDetail() {
    const modal = document.getElementById('port-detail-modal');
    const card = document.getElementById('port-detail-card');
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 180);
}
</script>
@endpush
