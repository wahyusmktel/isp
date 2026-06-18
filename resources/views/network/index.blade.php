@extends('layouts.app')
@section('title', 'Router & ODP')
@section('page-title', 'Router & ODP')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .network-map { height: 280px; min-height: 280px; border-radius: 1rem; overflow: hidden; }
    .mapping-map { height: 420px; min-height: 420px; border-radius: 1rem; overflow: hidden; }
</style>
@endpush

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Router & ODP</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola infrastruktur jaringan Mikrotik dan titik distribusi optik</p>
    </div>
    <div class="flex items-center gap-2 self-start sm:self-auto">
        <button onclick="openOdcModal('create')"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Tambah ODC
        </button>
        <button onclick="openOdpModal('create')"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Tambah ODP
        </button>
        <button onclick="openRouterModal('create')"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Tambah Router
        </button>
    </div>
</div>

{{-- ===== Tabs ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-2.5 mb-4 flex items-center gap-1">
    <button id="tab-router" onclick="switchTab('router')"
            class="tab-btn px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-gray-900 text-white">
        <svg class="w-4 h-4 inline -mt-0.5 mr-1.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <rect x="2" y="6" width="20" height="8" rx="2"/>
            <path stroke-linecap="round" d="M6 10h.01M10 10h.01M6 14v3M12 14v3M18 14v3"/>
        </svg>
        Router
        <span id="tab-router-count" class="ml-1.5 bg-white/20 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $routerStats['total'] }}</span>
    </button>
    <button id="tab-odp" onclick="switchTab('odp')"
            class="tab-btn px-4 py-2 rounded-xl text-sm font-semibold transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
        <svg class="w-4 h-4 inline -mt-0.5 mr-1.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3m-4.22-7.78-2.12 2.12M8.34 15.66l-2.12 2.12m0-14.14 2.12 2.12M15.66 15.66l2.12 2.12"/>
        </svg>
        ODP
        <span id="tab-odp-count" class="ml-1.5 bg-gray-200 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $odpStats['total'] }}</span>
    </button>
    <button id="tab-odc" onclick="switchTab('odc')"
            class="tab-btn px-4 py-2 rounded-xl text-sm font-semibold transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
        <svg class="w-4 h-4 inline -mt-0.5 mr-1.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M7 7v10m10-10v10M5 17h14M9 11h6"/>
        </svg>
        ODC
        <span id="tab-odc-count" class="ml-1.5 bg-gray-200 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $odcStats['total'] }}</span>
    </button>
</div>

{{-- ============================================================ --}}
{{--                       ROUTER TAB                              --}}
{{-- ============================================================ --}}
<div id="panel-router">

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        @php
        $rStats = [
            ['id'=>'rs-total',   'label'=>'Total Router',  'value'=>$routerStats['total'],   'bg'=>'bg-gray-100',  'ic'=>'text-gray-500',  'ico'=>'M4 6h16M4 10h16M4 14h16M4 18h7'],
            ['id'=>'rs-online',  'label'=>'Online',         'value'=>$routerStats['online'],  'bg'=>'bg-green-50',  'ic'=>'text-green-600', 'ico'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['id'=>'rs-offline', 'label'=>'Offline',        'value'=>$routerStats['offline'], 'bg'=>'bg-red-50',    'ic'=>'text-red-500',   'ico'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        @endphp
        @foreach($rStats as $sc)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl {{ $sc['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $sc['ic'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['ico'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="{{ $sc['id'] }}">{{ $sc['value'] }}</p>
                <p class="text-xs text-gray-500">{{ $sc['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Toolbar --}}
    <div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex items-center gap-3">
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="router-search" placeholder="Cari nama atau host..."
                   class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1"
                   oninput="filterRouters()">
        </div>
        <button onclick="testAllRouters()"
                class="ml-auto flex-shrink-0 inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold px-3 py-2 rounded-xl transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Cek Semua
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="router-table">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Router</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Host & Port</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Lokasi</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">PPPoE</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">ODP</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Status</th>
                        <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="router-tbody">
                @forelse($routers as $r)
                <tr class="router-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                    data-id="{{ $r->id }}"
                    data-search="{{ strtolower($r->name . ' ' . $r->host . ' ' . $r->location) }}">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <rect x="2" y="6" width="20" height="8" rx="2"/>
                                    <path stroke-linecap="round" d="M6 10h.01M10 10h.01M6 14v3M12 14v3M18 14v3"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 router-name">{{ $r->name }}</p>
                                <p class="text-xs text-gray-400">{{ $r->model ?? 'Model tidak diketahui' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="font-mono text-xs text-gray-700">{{ $r->host }}</p>
                        <div class="flex gap-1.5 mt-0.5">
                            <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-medium">API:{{ $r->api_port }}</span>
                            @if($r->winbox_port)
                            <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-medium">Winbox:{{ $r->winbox_port }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="text-sm text-gray-600">{{ $r->location ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="pppoe-badge font-bold text-gray-700 text-sm">{{ $r->pppoe_online }}</span>
                        <p class="text-[10px] text-gray-400">aktif</p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="font-semibold text-gray-700">{{ $r->odps_count }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @php
                        $statusCfg = [
                            'online'  => ['bg'=>'bg-green-100','text'=>'text-green-700','dot'=>'bg-green-500','label'=>'Online'],
                            'offline' => ['bg'=>'bg-red-100',  'text'=>'text-red-700',  'dot'=>'bg-red-500',  'label'=>'Offline'],
                            'unknown' => ['bg'=>'bg-gray-100', 'text'=>'text-gray-600', 'dot'=>'bg-gray-400', 'label'=>'Belum Cek'],
                        ];
                        $sc = $statusCfg[$r->status] ?? $statusCfg['unknown'];
                        @endphp
                        <span class="router-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc['bg'] }} {{ $sc['text'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                            {{ $sc['label'] }}
                        </span>
                        @if($r->last_check_at)
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $r->last_check_at->diffForHumans() }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="testRouter({{ $r->id }})" title="Test Koneksi"
                                    class="p-1.5 rounded-lg text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            <button onclick="viewLiveData({{ $r->id }}, '{{ addslashes($r->name) }}')" title="Data Live"
                                    class="p-1.5 rounded-lg text-purple-400 hover:bg-purple-50 hover:text-purple-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </button>
                            <button onclick="openRouterModal('edit', {{ $r->id }})" title="Edit"
                                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="deleteRouter({{ $r->id }}, '{{ addslashes($r->name) }}')" title="Hapus"
                                    class="p-1.5 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="router-empty">
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="2" y="6" width="20" height="8" rx="2"/>
                                    <path stroke-linecap="round" d="M6 10h.01M10 10h.01M6 14v3M12 14v3M18 14v3"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700">Belum ada router</p>
                                <p class="text-sm text-gray-400 mt-1">Tambahkan router Mikrotik untuk memulai</p>
                            </div>
                            <button onclick="openRouterModal('create')"
                                    class="mt-1 inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                Tambah Router
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end #panel-router --}}


{{-- ============================================================ --}}
{{--                        ODP TAB                                --}}
{{-- ============================================================ --}}
<div id="panel-odp" class="hidden">

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3m-4.22-7.78-2.12 2.12M8.34 15.66l-2.12 2.12m0-14.14 2.12 2.12M15.66 15.66l2.12 2.12"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="odp-stat-total">{{ $odpStats['total'] }}</p>
                <p class="text-xs text-gray-500">Total ODP</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="odp-stat-cap">{{ $odpStats['cap_total'] }}</p>
                <p class="text-xs text-gray-500">Total Port</p>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex items-center gap-3">
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="odp-search" placeholder="Cari nama atau lokasi ODP..."
                   class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1"
                   oninput="filterOdps()">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="odp-table">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Nama ODP</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Lokasi</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Router / ODC</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Koordinat</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Kapasitas</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Catatan</th>
                        <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="odp-tbody">
                @forelse($odps as $odp)
                <tr class="odp-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                    data-id="{{ $odp->id }}"
                    data-search="{{ strtolower($odp->name . ' ' . $odp->location . ' ' . ($odp->router?->name ?? '') . ' ' . ($odp->odc?->name ?? '')) }}">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900 odp-name">{{ $odp->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">{{ $odp->location }}</td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        @if($odp->router)
                        <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-lg font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="6" width="20" height="8" rx="2"/>
                            </svg>
                            {{ $odp->router->name }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                        @if($odp->odc)
                        <span class="mt-1 inline-flex items-center gap-1 text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-lg font-medium">
                            ODC: {{ $odp->odc->name }}
                        </span>
                        @else
                        <p class="text-xs text-gray-400 mt-1">ODC belum dipilih</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($odp->latitude && $odp->longitude)
                        <p class="font-mono text-xs text-gray-700">{{ number_format($odp->latitude, 6) }}, {{ number_format($odp->longitude, 6) }}</p>
                        @else
                        <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-lg font-semibold">Belum diset</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="font-bold text-gray-700">{{ $odp->capacity }}</span>
                        <span class="text-xs text-gray-400"> port</span>
                        <p class="text-[10px] text-gray-400">{{ $odp->customers_count ?? 0 }} pelanggan</p>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell text-sm text-gray-500 max-w-[200px] truncate">{{ $odp->notes ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openOdpMappingModal({{ $odp->id }})" title="Mapping Pelanggan"
                                    class="p-1.5 rounded-lg text-emerald-500 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3zM9 3v15m6-12v15"/>
                                </svg>
                            </button>
                            <button onclick="openOdpModal('edit', {{ $odp->id }})" title="Edit"
                                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="deleteOdp({{ $odp->id }}, '{{ addslashes($odp->name) }}')" title="Hapus"
                                    class="p-1.5 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="odp-empty">
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3m-4.22-7.78-2.12 2.12M8.34 15.66l-2.12 2.12m0-14.14 2.12 2.12M15.66 15.66l2.12 2.12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700">Belum ada ODP</p>
                                <p class="text-sm text-gray-400 mt-1">Tambahkan titik distribusi optik (ODP)</p>
                            </div>
                            <button onclick="openOdpModal('create')"
                                    class="mt-1 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                Tambah ODP
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end #panel-odp --}}

{{-- ============================================================ --}}
{{--                        ODC TAB                                --}}
{{-- ============================================================ --}}
<div id="panel-odc" class="hidden">
    <div class="grid grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M7 7v10m10-10v10M5 17h14M9 11h6"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="odc-stat-total">{{ $odcStats['total'] }}</p>
                <p class="text-xs text-gray-500">Total ODC</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16M8 8h8M8 16h8"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900" id="odc-stat-cap">{{ $odcStats['cap_total'] }}</p>
                <p class="text-xs text-gray-500">Total Kapasitas</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex items-center gap-3">
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="odc-search" placeholder="Cari nama atau lokasi ODC..."
                   class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1"
                   oninput="filterOdcs()">
        </div>
        <button onclick="openOdcModal('create')"
                class="ml-auto inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            Tambah ODC
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Nama ODC</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Lokasi</th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Koordinat</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Kapasitas</th>
                        <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">ODP</th>
                        <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="odc-tbody">
                @forelse($odcs as $odc)
                    <tr class="odc-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                        data-id="{{ $odc->id }}"
                        data-search="{{ strtolower($odc->name . ' ' . $odc->location) }}">
                        <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $odc->name }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ $odc->location ?? 'Belum diisi' }}</td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($odc->latitude && $odc->longitude)
                            <p class="font-mono text-xs text-gray-700">{{ number_format($odc->latitude, 6) }}, {{ number_format($odc->longitude, 6) }}</p>
                            @else
                            <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-lg font-semibold">Belum diset</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center font-bold text-gray-700">{{ $odc->capacity }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-gray-700">{{ $odc->odps_count }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="openOdcModal('edit', {{ $odc->id }})" title="Edit"
                                        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="deleteOdc({{ $odc->id }}, '{{ addslashes($odc->name) }}')" title="Hapus"
                                        class="p-1.5 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-gray-400">Belum ada ODC</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>{{-- end #panel-odc --}}


{{-- ============================================================ --}}
{{--                    MODAL: ROUTER                              --}}
{{-- ============================================================ --}}
<div id="router-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRouterModal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900" id="router-modal-title">Tambah Router</h3>
                <button onclick="closeRouterModal()" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="router-form" onsubmit="submitRouter(event)">
                <input type="hidden" id="rf-id">
                <div class="overflow-y-auto px-6 py-5 space-y-5 flex-1">
                    {{-- Info Utama --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Informasi Router</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="lbl">Nama Router <span class="text-red-500">*</span></label>
                                <input type="text" id="rf-name" name="name" placeholder="cth. Caelum Core Router"
                                       class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-rf-name"></p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="lbl">Host (IP / Domain) <span class="text-red-500">*</span></label>
                                <input type="text" id="rf-host" name="host" placeholder="cth. 192.168.1.1 atau caelum.s-net.id"
                                       class="inp w-full font-mono text-sm">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-rf-host"></p>
                            </div>
                            <div>
                                <label class="lbl">Port API <span class="text-red-500">*</span>
                                    <span class="text-gray-400 font-normal text-[10px]">(default 8728)</span>
                                </label>
                                <input type="number" id="rf-api-port" name="api_port" value="8728" min="1" max="65535"
                                       class="inp w-full font-mono text-sm">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-rf-api_port"></p>
                            </div>
                            <div>
                                <label class="lbl">Port Winbox
                                    <span class="text-gray-400 font-normal text-[10px]">(opsional)</span>
                                </label>
                                <input type="number" id="rf-winbox-port" name="winbox_port" placeholder="cth. 8343" min="1" max="65535"
                                       class="inp w-full font-mono text-sm">
                            </div>
                        </div>
                    </div>
                    {{-- Autentikasi --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Autentikasi</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="lbl">Username <span class="text-red-500">*</span></label>
                                <input type="text" id="rf-username" name="username" placeholder="admin"
                                       class="inp w-full font-mono text-sm">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-rf-username"></p>
                            </div>
                            <div>
                                <label class="lbl">Password <span class="text-red-500">*</span></label>
                                <input type="password" id="rf-password" name="password" placeholder="••••••••"
                                       class="inp w-full font-mono text-sm">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-rf-password"></p>
                            </div>
                        </div>
                    </div>
                    {{-- Lokasi & Catatan --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Keterangan</p>
                        <div class="space-y-4">
                            <div>
                                <label class="lbl">Lokasi</label>
                                <input type="text" id="rf-location" name="location" placeholder="cth. Server Room Lt. 2"
                                       class="inp w-full">
                            </div>
                            <div>
                                <label class="lbl">Catatan</label>
                                <textarea id="rf-notes" name="notes" rows="2" placeholder="Catatan tambahan..."
                                          class="inp w-full resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button type="button" onclick="closeRouterModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="router-save-btn"
                            class="px-5 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 hidden" id="router-save-spinner" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span id="router-save-label">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{--                    MODAL: ODP                                 --}}
{{-- ============================================================ --}}
<div id="odp-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeOdpModal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900" id="odp-modal-title">Tambah ODP</h3>
                <button onclick="closeOdpModal()" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="odp-form" onsubmit="submitOdp(event)">
                <input type="hidden" id="of-id">
                <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1">
                    <div>
                        <label class="lbl">Nama ODP <span class="text-red-500">*</span></label>
                        <input type="text" id="of-name" name="name" placeholder="cth. ODP-CAELUM-01"
                               class="inp w-full">
                        <p class="err hidden text-xs text-red-500 mt-1" id="err-of-name"></p>
                    </div>
                    <div>
                        <label class="lbl">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" id="of-location" name="location" placeholder="cth. Jl. Merdeka No. 5"
                               class="inp w-full">
                        <p class="err hidden text-xs text-red-500 mt-1" id="err-of-location"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="lbl">Router</label>
                            <select id="of-router" name="router_id" class="inp w-full">
                                <option value="">— Pilih Router —</option>
                                @foreach($routers as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="lbl">ODC</label>
                            <select id="of-odc" name="odc_id" class="inp w-full">
                                <option value="">Pilih ODC</option>
                                @foreach($odcs as $odc)
                                <option value="{{ $odc->id }}">{{ $odc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="lbl">Kapasitas Port <span class="text-red-500">*</span></label>
                            <select id="of-capacity" name="capacity" class="inp w-full">
                                @foreach([4, 8, 12, 16, 24, 32] as $cap)
                                <option value="{{ $cap }}" {{ $cap === 8 ? 'selected' : '' }}>{{ $cap }} Port</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="lbl">Latitude ODP</label>
                            <input type="number" step="any" id="of-latitude" name="latitude" class="inp w-full" placeholder="-6.200000">
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-of-latitude"></p>
                        </div>
                        <div>
                            <label class="lbl">Longitude ODP</label>
                            <input type="number" step="any" id="of-longitude" name="longitude" class="inp w-full" placeholder="106.816666">
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-of-longitude"></p>
                        </div>
                    </div>
                    <div>
                        <label class="lbl">Titik Lokasi ODP</label>
                        <div id="odp-location-map" class="network-map border border-gray-200"></div>
                        <p class="text-xs text-gray-400 mt-2">Klik peta untuk menentukan titik ODP.</p>
                    </div>
                    <div>
                        <label class="lbl">Catatan</label>
                        <textarea id="of-notes" name="notes" rows="2" placeholder="Catatan lokasi ODP..."
                                  class="inp w-full resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button type="button" onclick="closeOdpModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="odp-save-btn"
                            class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 hidden animate-spin" id="odp-save-spinner" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span id="odp-save-label">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{--                    MODAL: ODC                                 --}}
{{-- ============================================================ --}}
<div id="odc-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeOdcModal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900" id="odc-modal-title">Tambah ODC</h3>
                <button onclick="closeOdcModal()" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="odc-form" onsubmit="submitOdc(event)">
                <input type="hidden" id="oc-id">
                <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="lbl">Nama ODC <span class="text-red-500">*</span></label>
                            <input type="text" id="oc-name" class="inp w-full" placeholder="cth. ODC-UTAMA-01">
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-oc-name"></p>
                        </div>
                        <div>
                            <label class="lbl">Kapasitas</label>
                            <input type="number" min="0" id="oc-capacity" class="inp w-full" value="0">
                        </div>
                    </div>
                    <div>
                        <label class="lbl">Lokasi</label>
                        <input type="text" id="oc-location" class="inp w-full" placeholder="Alamat atau patokan ODC">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="lbl">Latitude ODC</label>
                            <input type="number" step="any" id="oc-latitude" class="inp w-full" placeholder="-6.200000">
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-oc-latitude"></p>
                        </div>
                        <div>
                            <label class="lbl">Longitude ODC</label>
                            <input type="number" step="any" id="oc-longitude" class="inp w-full" placeholder="106.816666">
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-oc-longitude"></p>
                        </div>
                    </div>
                    <div>
                        <label class="lbl">Titik Lokasi ODC</label>
                        <div id="odc-location-map" class="network-map border border-gray-200"></div>
                    </div>
                    <div>
                        <label class="lbl">Catatan</label>
                        <textarea id="oc-notes" rows="2" class="inp w-full resize-none" placeholder="Catatan ODC..."></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button type="button" onclick="closeOdcModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" id="odc-save-btn" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{--                    MODAL: MAPPING ODP                         --}}
{{-- ============================================================ --}}
<div id="odp-mapping-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeOdpMappingModal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-bold text-gray-900" id="mapping-modal-title">Mapping Pelanggan ke ODP</h3>
                    <p class="text-xs text-gray-400" id="mapping-modal-sub">Pilih pelanggan untuk melihat garis kabel dan estimasi jarak.</p>
                </div>
                <button onclick="closeOdpMappingModal()" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-[360px_1fr] gap-0 overflow-y-auto">
                <div class="p-6 border-r border-gray-100 space-y-4">
                    <input type="hidden" id="mapping-odp-id">
                    <div>
                        <label class="lbl">Cari Pelanggan</label>
                        <input type="text" id="mapping-customer-search" class="inp w-full" placeholder="Cari nama, nomor, telepon..." oninput="renderMappingCustomerOptions()">
                    </div>
                    <div>
                        <label class="lbl">Pelanggan dengan Koordinat</label>
                        <select id="mapping-customer" class="inp w-full" size="9" onchange="previewOdpCustomerLine()"></select>
                    </div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-xs text-gray-400">Jarak kabel</p>
                        <p class="text-2xl font-bold text-gray-900" id="mapping-distance">-</p>
                        <p class="text-xs text-gray-500 mt-1" id="mapping-selected-info">Belum ada pelanggan dipilih.</p>
                    </div>
                    <button onclick="saveOdpCustomerMapping()" id="mapping-save-btn"
                            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        Simpan Mapping
                    </button>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Pelanggan di ODP ini</p>
                        <div id="mapping-current-list" class="space-y-2 max-h-48 overflow-y-auto"></div>
                    </div>
                </div>
                <div class="p-6">
                    <div id="odp-mapping-map" class="mapping-map border border-gray-200"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{--                  MODAL: LIVE DATA                             --}}
{{-- ============================================================ --}}
<div id="live-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeLiveModal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900" id="live-modal-title">Data Live Router</h3>
                        <p class="text-xs text-gray-400" id="live-modal-sub">Mengambil data dari router...</p>
                    </div>
                </div>
                <button onclick="closeLiveModal()" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-1 px-6 py-5" id="live-modal-body">
                {{-- Loading state --}}
                <div id="live-loading" class="flex flex-col items-center justify-center py-12 gap-3">
                    <svg class="w-8 h-8 text-purple-500 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Menghubungkan ke router...</p>
                </div>
                {{-- Error state --}}
                <div id="live-error" class="hidden">
                    <div class="flex flex-col items-center justify-center py-12 gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-700">Gagal terhubung</p>
                            <p class="text-sm text-gray-400 mt-1" id="live-error-msg"></p>
                        </div>
                    </div>
                </div>
                {{-- Data state --}}
                <div id="live-data" class="hidden space-y-5">
                    {{-- System Resource --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Sumber Daya Sistem</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="live-resource-grid"></div>
                    </div>
                    {{-- Active PPPoE Sessions --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">
                            Sesi PPPoE Aktif
                            <span class="normal-case text-gray-600 font-semibold ml-1" id="live-pppoe-count"></span>
                        </p>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold">User</th>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold">IP Address</th>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold hidden sm:table-cell">Uptime</th>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold hidden md:table-cell">Service</th>
                                    </tr>
                                </thead>
                                <tbody id="live-pppoe-tbody">
                                    <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Tidak ada sesi aktif</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Interfaces --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">
                            Interface
                            <span class="normal-case text-gray-600 font-semibold ml-1" id="live-iface-count"></span>
                        </p>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold">Nama</th>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold hidden sm:table-cell">Tipe</th>
                                        <th class="text-center px-3 py-2 text-gray-500 font-semibold">Status</th>
                                        <th class="text-left px-3 py-2 text-gray-500 font-semibold hidden md:table-cell">Comment</th>
                                    </tr>
                                </thead>
                                <tbody id="live-iface-tbody">
                                    <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Tidak ada data interface</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400" id="live-ts"></p>
                <button onclick="closeLiveModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== Toast ===== --}}
<div id="toast" class="fixed bottom-5 right-5 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm">
        <svg id="toast-icon" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
        <span id="toast-msg"></span>
    </div>
</div>

{{-- ─── Data for JS ─── --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const ROUTERS = @json($routers->map(fn($r) => $r->toJsonData()));
const ODPS    = @json($odps->map(fn($o) => $o->toJsonData()));
const ODCS    = @json($odcs->map(fn($o) => $o->toJsonData()));
const CUSTOMERS = @json($customers->map(fn($c) => $c->toJsonData()));
const CSRF    = '{{ csrf_token() }}';
const DEFAULT_MAP_CENTER = [-6.2, 106.816666];
let odpLocationMap, odpLocationMarker, odcLocationMap, odcLocationMarker, mappingMap, mappingMarkers = [], mappingLine = null;

// ─── Tabs ─────────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.getElementById('panel-router').classList.toggle('hidden', tab !== 'router');
    document.getElementById('panel-odp').classList.toggle('hidden', tab !== 'odp');
    document.getElementById('panel-odc').classList.toggle('hidden', tab !== 'odc');

    ['router','odp','odc'].forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (t === tab) {
            btn.className = 'tab-btn px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-gray-900 text-white';
            document.getElementById('tab-' + t + '-count').className = 'ml-1.5 bg-white/20 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold';
        } else {
            btn.className = 'tab-btn px-4 py-2 rounded-xl text-sm font-semibold transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50';
            document.getElementById('tab-' + t + '-count').className = 'ml-1.5 bg-gray-200 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold';
        }
    });
}

// ─── Filter ───────────────────────────────────────────────────────────────
function filterRouters() {
    const q = document.getElementById('router-search').value.toLowerCase();
    document.querySelectorAll('.router-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q) ? '' : 'none';
    });
}
function filterOdps() {
    const q = document.getElementById('odp-search').value.toLowerCase();
    document.querySelectorAll('.odp-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q) ? '' : 'none';
    });
}
function filterOdcs() {
    const q = document.getElementById('odc-search').value.toLowerCase();
    document.querySelectorAll('.odc-row').forEach(r => {
        r.style.display = r.dataset.search.includes(q) ? '' : 'none';
    });
}

// ─── Toast ────────────────────────────────────────────────────────────────
let toastTimer;
function showToast(msg, type = 'success') {
    clearTimeout(toastTimer);
    const el = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const icon  = document.getElementById('toast-icon');
    document.getElementById('toast-msg').textContent = msg;
    inner.className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm ${
        type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    icon.innerHTML = type === 'success'
        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
    el.classList.remove('hidden');
    toastTimer = setTimeout(() => el.classList.add('hidden'), 3500);
}

function clearErrors() {
    document.querySelectorAll('.err').forEach(e => e.classList.add('hidden'));
}
function showErrors(errors) {
    Object.entries(errors).forEach(([field, msgs]) => {
        const el = document.getElementById('err-rf-' + field) || document.getElementById('err-of-' + field) || document.getElementById('err-oc-' + field);
        if (el) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
    });
}

function hasCoords(item) {
    return item && item.latitude !== null && item.latitude !== undefined && item.longitude !== null && item.longitude !== undefined;
}
function toLatLng(item) {
    return [parseFloat(item.latitude), parseFloat(item.longitude)];
}
function setMarker(map, marker, lat, lng, label = '') {
    if (marker) marker.setLatLng([lat, lng]);
    else marker = L.marker([lat, lng]).addTo(map);
    if (label) marker.bindPopup(label);
    map.setView([lat, lng], 17);
    return marker;
}
function syncMapInputs(prefix, lat, lng) {
    document.getElementById(`${prefix}-latitude`).value = Number(lat).toFixed(7);
    document.getElementById(`${prefix}-longitude`).value = Number(lng).toFixed(7);
}
function initPickerMap(kind, lat = null, lng = null) {
    const isOdp = kind === 'odp';
    const elId = isOdp ? 'odp-location-map' : 'odc-location-map';
    const prefix = isOdp ? 'of' : 'oc';
    const start = lat && lng ? [parseFloat(lat), parseFloat(lng)] : DEFAULT_MAP_CENTER;
    let map = isOdp ? odpLocationMap : odcLocationMap;
    if (!map) {
        map = L.map(elId).setView(start, lat && lng ? 17 : 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        map.on('click', e => {
            syncMapInputs(prefix, e.latlng.lat, e.latlng.lng);
            if (isOdp) odpLocationMarker = setMarker(map, odpLocationMarker, e.latlng.lat, e.latlng.lng);
            else odcLocationMarker = setMarker(map, odcLocationMarker, e.latlng.lat, e.latlng.lng);
        });
        if (isOdp) odpLocationMap = map; else odcLocationMap = map;
    }
    setTimeout(() => {
        map.invalidateSize();
        map.setView(start, lat && lng ? 17 : 12);
        if (lat && lng) {
            if (isOdp) odpLocationMarker = setMarker(map, odpLocationMarker, lat, lng);
            else odcLocationMarker = setMarker(map, odcLocationMarker, lat, lng);
        }
    }, 120);
}
function distanceMeters(a, b) {
    const R = 6371000;
    const dLat = (b[0] - a[0]) * Math.PI / 180;
    const dLng = (b[1] - a[1]) * Math.PI / 180;
    const lat1 = a[0] * Math.PI / 180;
    const lat2 = b[0] * Math.PI / 180;
    const x = Math.sin(dLat / 2) ** 2 + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLng / 2) ** 2;
    return Math.round(R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x)));
}

// ─── Router Modal ─────────────────────────────────────────────────────────
function openRouterModal(mode, id = null) {
    clearErrors();
    document.getElementById('rf-id').value = '';
    document.getElementById('rf-name').value = '';
    document.getElementById('rf-host').value = '';
    document.getElementById('rf-api-port').value = '8728';
    document.getElementById('rf-winbox-port').value = '';
    document.getElementById('rf-username').value = 'admin';
    document.getElementById('rf-password').value = '';
    document.getElementById('rf-location').value = '';
    document.getElementById('rf-notes').value = '';

    if (mode === 'edit' && id) {
        const r = ROUTERS.find(x => x.id == id);
        if (!r) return;
        document.getElementById('router-modal-title').textContent = 'Edit Router';
        document.getElementById('router-save-label').textContent  = 'Simpan Perubahan';
        document.getElementById('rf-id').value           = r.id;
        document.getElementById('rf-name').value         = r.name;
        document.getElementById('rf-host').value         = r.host;
        document.getElementById('rf-api-port').value     = r.api_port;
        document.getElementById('rf-winbox-port').value  = r.winbox_port ?? '';
        document.getElementById('rf-username').value     = r.username;
        document.getElementById('rf-password').value     = r.password ?? '';
        document.getElementById('rf-location').value     = r.location ?? '';
        document.getElementById('rf-notes').value        = r.notes ?? '';
    } else {
        document.getElementById('router-modal-title').textContent = 'Tambah Router';
        document.getElementById('router-save-label').textContent  = 'Simpan';
    }
    document.getElementById('router-modal').classList.remove('hidden');
}
function closeRouterModal() {
    document.getElementById('router-modal').classList.add('hidden');
}

async function submitRouter(e) {
    e.preventDefault();
    clearErrors();
    const id  = document.getElementById('rf-id').value;
    const url = id ? `/routers/${id}` : '/routers';
    const method = id ? 'PUT' : 'POST';
    const btn = document.getElementById('router-save-btn');
    btn.disabled = true;
    document.getElementById('router-save-spinner').classList.remove('hidden');
    document.getElementById('router-save-spinner').classList.add('animate-spin');

    const body = {
        name:        document.getElementById('rf-name').value,
        host:        document.getElementById('rf-host').value,
        api_port:    parseInt(document.getElementById('rf-api-port').value),
        winbox_port: document.getElementById('rf-winbox-port').value || null,
        username:    document.getElementById('rf-username').value,
        password:    document.getElementById('rf-password').value,
        location:    document.getElementById('rf-location').value || null,
        notes:       document.getElementById('rf-notes').value || null,
    };

    try {
        const res  = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify(body) });
        const data = await res.json();
        if (res.status === 422) { showErrors(data.errors); return; }
        if (!data.success) { showToast(data.message, 'error'); return; }

        closeRouterModal();
        showToast(data.message, 'success');
        upsertRouterRow(data.router, !id);
    } catch(err) {
        showToast('Terjadi kesalahan jaringan', 'error');
    } finally {
        btn.disabled = false;
        document.getElementById('router-save-spinner').classList.add('hidden');
        document.getElementById('router-save-spinner').classList.remove('animate-spin');
    }
}

// ─── ODP Modal ────────────────────────────────────────────────────────────
function openOdpModal(mode, id = null) {
    clearErrors();
    document.getElementById('of-id').value = '';
    document.getElementById('of-name').value = '';
    document.getElementById('of-location').value = '';
    document.getElementById('of-router').value = '';
    document.getElementById('of-odc').value = '';
    document.getElementById('of-latitude').value = '';
    document.getElementById('of-longitude').value = '';
    document.getElementById('of-capacity').value = '8';
    document.getElementById('of-notes').value = '';

    if (mode === 'edit' && id) {
        const o = ODPS.find(x => x.id == id);
        if (!o) return;
        document.getElementById('odp-modal-title').textContent = 'Edit ODP';
        document.getElementById('odp-save-label').textContent  = 'Simpan Perubahan';
        document.getElementById('of-id').value       = o.id;
        document.getElementById('of-name').value     = o.name;
        document.getElementById('of-location').value = o.location;
        document.getElementById('of-router').value   = o.router_id ?? '';
        document.getElementById('of-odc').value      = o.odc_id ?? '';
        document.getElementById('of-latitude').value = o.latitude ?? '';
        document.getElementById('of-longitude').value = o.longitude ?? '';
        document.getElementById('of-capacity').value = o.capacity;
        document.getElementById('of-notes').value    = o.notes ?? '';
    } else {
        document.getElementById('odp-modal-title').textContent = 'Tambah ODP';
        document.getElementById('odp-save-label').textContent  = 'Simpan';
    }
    document.getElementById('odp-modal').classList.remove('hidden');
    initPickerMap('odp', document.getElementById('of-latitude').value, document.getElementById('of-longitude').value);
}
function closeOdpModal() {
    document.getElementById('odp-modal').classList.add('hidden');
}

async function submitOdp(e) {
    e.preventDefault();
    clearErrors();
    const id  = document.getElementById('of-id').value;
    const url = id ? `/odps/${id}` : '/odps';
    const method = id ? 'PUT' : 'POST';
    const btn = document.getElementById('odp-save-btn');
    btn.disabled = true;
    document.getElementById('odp-save-spinner').classList.remove('hidden');
    document.getElementById('odp-save-spinner').classList.add('animate-spin');

    const body = {
        name:      document.getElementById('of-name').value,
        location:  document.getElementById('of-location').value,
        router_id: document.getElementById('of-router').value || null,
        odc_id:    document.getElementById('of-odc').value || null,
        latitude:  document.getElementById('of-latitude').value || null,
        longitude: document.getElementById('of-longitude').value || null,
        capacity:  parseInt(document.getElementById('of-capacity').value),
        notes:     document.getElementById('of-notes').value || null,
    };

    try {
        const res  = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify(body) });
        const data = await res.json();
        if (res.status === 422) { showErrors(data.errors); return; }
        if (!data.success) { showToast(data.message, 'error'); return; }

        closeOdpModal();
        showToast(data.message, 'success');
        upsertOdpRow(data.odp, !id);
    } catch(err) {
        showToast('Terjadi kesalahan jaringan', 'error');
    } finally {
        btn.disabled = false;
        document.getElementById('odp-save-spinner').classList.add('hidden');
        document.getElementById('odp-save-spinner').classList.remove('animate-spin');
    }
}

// ─── Test Connection ──────────────────────────────────────────────────────
// ODC Modal
function openOdcModal(mode, id = null) {
    clearErrors();
    document.getElementById('oc-id').value = '';
    document.getElementById('oc-name').value = '';
    document.getElementById('oc-location').value = '';
    document.getElementById('oc-latitude').value = '';
    document.getElementById('oc-longitude').value = '';
    document.getElementById('oc-capacity').value = '0';
    document.getElementById('oc-notes').value = '';
    if (mode === 'edit' && id) {
        const o = ODCS.find(x => x.id == id);
        if (!o) return;
        document.getElementById('odc-modal-title').textContent = 'Edit ODC';
        document.getElementById('oc-id').value = o.id;
        document.getElementById('oc-name').value = o.name;
        document.getElementById('oc-location').value = o.location ?? '';
        document.getElementById('oc-latitude').value = o.latitude ?? '';
        document.getElementById('oc-longitude').value = o.longitude ?? '';
        document.getElementById('oc-capacity').value = o.capacity ?? 0;
        document.getElementById('oc-notes').value = o.notes ?? '';
    } else {
        document.getElementById('odc-modal-title').textContent = 'Tambah ODC';
    }
    document.getElementById('odc-modal').classList.remove('hidden');
    initPickerMap('odc', document.getElementById('oc-latitude').value, document.getElementById('oc-longitude').value);
}
function closeOdcModal() {
    document.getElementById('odc-modal').classList.add('hidden');
}
async function submitOdc(e) {
    e.preventDefault();
    clearErrors();
    const id = document.getElementById('oc-id').value;
    const btn = document.getElementById('odc-save-btn');
    btn.disabled = true;
    const body = {
        name: document.getElementById('oc-name').value,
        location: document.getElementById('oc-location').value || null,
        latitude: document.getElementById('oc-latitude').value || null,
        longitude: document.getElementById('oc-longitude').value || null,
        capacity: parseInt(document.getElementById('oc-capacity').value || '0'),
        notes: document.getElementById('oc-notes').value || null,
    };
    try {
        const res = await fetch(id ? `/odcs/${id}` : '/odcs', { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify(body) });
        const data = await res.json();
        if (res.status === 422) { showErrors(data.errors); return; }
        if (!data.success) return showToast(data.message, 'error');
        showToast(data.message, 'success');
        closeOdcModal();
        location.reload();
    } catch (err) {
        showToast('Gagal menyimpan ODC', 'error');
    } finally {
        btn.disabled = false;
    }
}

// Mapping pelanggan ke ODP
function openOdpMappingModal(id) {
    const odp = ODPS.find(x => x.id == id);
    if (!odp) return;
    document.getElementById('mapping-odp-id').value = id;
    document.getElementById('mapping-modal-title').textContent = `Mapping Pelanggan - ${odp.name}`;
    document.getElementById('mapping-modal-sub').textContent = odp.location ?? '';
    document.getElementById('mapping-customer-search').value = '';
    document.getElementById('mapping-distance').textContent = '-';
    document.getElementById('mapping-selected-info').textContent = hasCoords(odp) ? 'Pilih pelanggan untuk melihat garis kabel.' : 'Koordinat ODP belum diset. Edit ODP dulu untuk mengisi titik lokasi.';
    document.getElementById('odp-mapping-modal').classList.remove('hidden');
    renderMappingCustomerOptions();
    renderCurrentMappedCustomers();
    setTimeout(() => initMappingMap(odp), 120);
}
function closeOdpMappingModal() {
    document.getElementById('odp-mapping-modal').classList.add('hidden');
}
function initMappingMap(odp) {
    const start = hasCoords(odp) ? toLatLng(odp) : DEFAULT_MAP_CENTER;
    if (!mappingMap) {
        mappingMap = L.map('odp-mapping-map').setView(start, hasCoords(odp) ? 17 : 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(mappingMap);
    }
    mappingMap.invalidateSize();
    mappingMarkers.forEach(m => m.remove());
    mappingMarkers = [];
    if (mappingLine) { mappingLine.remove(); mappingLine = null; }
    mappingMap.setView(start, hasCoords(odp) ? 17 : 12);
    if (hasCoords(odp)) mappingMarkers.push(L.marker(start).addTo(mappingMap).bindPopup(odp.name));
}
function renderMappingCustomerOptions() {
    const odpId = document.getElementById('mapping-odp-id').value;
    const q = document.getElementById('mapping-customer-search').value.toLowerCase();
    const rows = CUSTOMERS.filter(c => hasCoords(c))
        .filter(c => (`${c.name} ${c.customer_number ?? ''} ${c.phone ?? ''} ${c.address ?? ''}`).toLowerCase().includes(q))
        .sort((a, b) => (a.odp_id == odpId ? -1 : 0) - (b.odp_id == odpId ? -1 : 0));
    document.getElementById('mapping-customer').innerHTML = rows.map(c => `<option value="${c.id}">${c.name}${c.customer_number ? ' - ' + c.customer_number : ''}${c.odp_name ? ' | ' + c.odp_name : ''}</option>`).join('') || '<option value="">Tidak ada pelanggan berkoordinat</option>';
}
function previewOdpCustomerLine() {
    const odp = ODPS.find(x => x.id == document.getElementById('mapping-odp-id').value);
    const customer = CUSTOMERS.find(x => x.id == document.getElementById('mapping-customer').value);
    if (!odp || !customer || !hasCoords(odp) || !hasCoords(customer)) return;
    const a = toLatLng(odp);
    const b = toLatLng(customer);
    const meters = distanceMeters(a, b);
    if (mappingLine) mappingLine.remove();
    mappingMarkers.slice(1).forEach(m => m.remove());
    mappingMarkers = mappingMarkers.slice(0, 1);
    mappingMarkers.push(L.marker(b).addTo(mappingMap).bindPopup(customer.name));
    mappingLine = L.polyline([a, b], { color: '#059669', weight: 4, dashArray: '8 8' }).addTo(mappingMap);
    mappingMap.fitBounds(mappingLine.getBounds(), { padding: [40, 40] });
    document.getElementById('mapping-distance').textContent = `${meters.toLocaleString('id-ID')} m`;
    document.getElementById('mapping-selected-info').textContent = `${customer.name} - ${customer.address ?? 'Alamat belum diisi'}`;
}
async function saveOdpCustomerMapping() {
    const odp = ODPS.find(x => x.id == document.getElementById('mapping-odp-id').value);
    const customer = CUSTOMERS.find(x => x.id == document.getElementById('mapping-customer').value);
    if (!odp || !customer) return showToast('Pilih pelanggan dulu', 'error');
    if (!hasCoords(odp)) return showToast('Koordinat ODP belum diset', 'error');
    const meters = distanceMeters(toLatLng(odp), toLatLng(customer));
    try {
        const res = await fetch(`/odps/${odp.id}/customers`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify({ customer_id: customer.id, cable_distance_meters: meters }) });
        const data = await res.json();
        if (!data.success) return showToast(data.message, 'error');
        const oi = ODPS.findIndex(x => x.id == data.odp.id);
        if (oi > -1) ODPS[oi] = data.odp;
        const ci = CUSTOMERS.findIndex(x => x.id == data.customer.id);
        if (ci > -1) CUSTOMERS[ci] = data.customer;
        showToast(data.message, 'success');
        renderMappingCustomerOptions();
        renderCurrentMappedCustomers();
    } catch (err) {
        showToast('Gagal menyimpan mapping', 'error');
    }
}
function renderCurrentMappedCustomers() {
    const odpId = document.getElementById('mapping-odp-id').value;
    const rows = CUSTOMERS.filter(c => c.odp_id == odpId);
    document.getElementById('mapping-current-list').innerHTML = rows.length ? rows.map(c => `
        <div class="rounded-xl border border-gray-100 p-3 flex items-center justify-between gap-3">
            <div><p class="text-sm font-semibold text-gray-800">${c.name}</p><p class="text-xs text-gray-400">${c.cable_distance_meters ? Number(c.cable_distance_meters).toLocaleString('id-ID') + ' m' : 'Jarak belum tersimpan'}</p></div>
            <button onclick="unmapOdpCustomer(${odpId}, ${c.id})" class="text-xs font-semibold text-red-500 hover:text-red-600">Lepas</button>
        </div>`).join('') : '<p class="text-sm text-gray-400">Belum ada pelanggan.</p>';
}
async function unmapOdpCustomer(odpId, customerId) {
    try {
        const res = await fetch(`/odps/${odpId}/customers/${customerId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.success) return showToast(data.message, 'error');
        const oi = ODPS.findIndex(x => x.id == data.odp.id);
        if (oi > -1) ODPS[oi] = data.odp;
        const ci = CUSTOMERS.findIndex(x => x.id == customerId);
        if (ci > -1) { CUSTOMERS[ci].odp_id = null; CUSTOMERS[ci].odp_name = null; CUSTOMERS[ci].cable_distance_meters = null; }
        showToast(data.message, 'success');
        renderMappingCustomerOptions();
        renderCurrentMappedCustomers();
    } catch (err) {
        showToast('Gagal melepas mapping', 'error');
    }
}

async function testRouter(id) {
    showToast('Menguji koneksi...', 'info');
    try {
        const res  = await fetch(`/routers/${id}/test`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            showToast(`Router online — PPPoE aktif: ${data.router.pppoe_online}`, 'success');
        } else {
            showToast('Offline: ' + data.message, 'error');
        }
        updateRouterRowStatus(data.router);
    } catch(err) {
        showToast('Gagal menguji koneksi', 'error');
    }
}

async function testAllRouters() {
    const rows = document.querySelectorAll('.router-row');
    if (!rows.length) return;
    showToast(`Memeriksa ${rows.length} router...`, 'info');
    for (const row of rows) {
        await testRouter(row.dataset.id);
    }
    showToast('Pengecekan selesai', 'success');
}

// ─── Live Data ────────────────────────────────────────────────────────────
async function viewLiveData(id, name) {
    document.getElementById('live-modal-title').textContent = name;
    document.getElementById('live-modal-sub').textContent   = 'Menghubungkan ke router...';
    document.getElementById('live-loading').classList.remove('hidden');
    document.getElementById('live-error').classList.add('hidden');
    document.getElementById('live-data').classList.add('hidden');
    document.getElementById('live-ts').textContent = '';
    document.getElementById('live-modal').classList.remove('hidden');

    try {
        const res  = await fetch(`/routers/${id}/live`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        document.getElementById('live-loading').classList.add('hidden');

        if (!data.success) {
            document.getElementById('live-error-msg').textContent = data.message;
            document.getElementById('live-error').classList.remove('hidden');
            document.getElementById('live-modal-sub').textContent = 'Gagal terhubung';
            return;
        }

        document.getElementById('live-modal-sub').textContent = `${data.router.model ?? 'RouterOS'} v${(data.resource.version ?? '').split(' ')[0]}`;
        document.getElementById('live-ts').textContent = 'Diperbarui: ' + new Date().toLocaleTimeString('id-ID');

        // Resource grid
        const res2 = data.resource;
        const cpuLoad = res2['cpu-load'] ?? '—';
        const freeM   = res2['free-memory'] ? Math.round(parseInt(res2['free-memory']) / 1024 / 1024) + ' MB' : '—';
        const totalM  = res2['total-memory'] ? Math.round(parseInt(res2['total-memory']) / 1024 / 1024) + ' MB' : '—';
        const uptime  = res2['uptime'] ?? '—';

        document.getElementById('live-resource-grid').innerHTML = [
            { label: 'CPU Load', value: cpuLoad + '%', color: 'text-orange-600', bg: 'bg-orange-50' },
            { label: 'Free Memory', value: freeM, color: 'text-blue-600', bg: 'bg-blue-50' },
            { label: 'Total Memory', value: totalM, color: 'text-gray-600', bg: 'bg-gray-100' },
            { label: 'Uptime', value: uptime, color: 'text-green-700', bg: 'bg-green-50' },
        ].map(c => `
            <div class="rounded-xl ${c.bg} p-3 text-center">
                <p class="text-base font-bold ${c.color}">${c.value}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">${c.label}</p>
            </div>`).join('');

        // PPPoE sessions
        const actives = data.actives ?? [];
        document.getElementById('live-pppoe-count').textContent = `(${actives.length})`;
        document.getElementById('live-pppoe-tbody').innerHTML = actives.length
            ? actives.map(a => `<tr class="border-t border-gray-50">
                <td class="px-3 py-2 font-mono font-medium text-gray-800">${a.name ?? '—'}</td>
                <td class="px-3 py-2 font-mono text-gray-600">${a.address ?? '—'}</td>
                <td class="px-3 py-2 text-gray-600 hidden sm:table-cell">${a.uptime ?? '—'}</td>
                <td class="px-3 py-2 text-gray-500 hidden md:table-cell">${a.service ?? '—'}</td>
              </tr>`).join('')
            : '<tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Tidak ada sesi aktif</td></tr>';

        // Interfaces
        const ifaces = data.interfaces ?? [];
        document.getElementById('live-iface-count').textContent = `(${ifaces.length})`;
        document.getElementById('live-iface-tbody').innerHTML = ifaces.length
            ? ifaces.map(i => {
                const running = i.running === 'true' || i.running === true;
                return `<tr class="border-t border-gray-50">
                    <td class="px-3 py-2 font-mono font-medium text-gray-800">${i.name ?? '—'}</td>
                    <td class="px-3 py-2 text-gray-600 hidden sm:table-cell">${i.type ?? '—'}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold ${running ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">
                            <span class="w-1.5 h-1.5 rounded-full ${running ? 'bg-green-500' : 'bg-gray-400'}"></span>
                            ${running ? 'Up' : 'Down'}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-gray-500 hidden md:table-cell">${i.comment ?? '—'}</td>
                </tr>`;
            }).join('')
            : '<tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Tidak ada data interface</td></tr>';

        document.getElementById('live-data').classList.remove('hidden');
        updateRouterRowStatus(data.router);
    } catch(err) {
        document.getElementById('live-loading').classList.add('hidden');
        document.getElementById('live-error-msg').textContent = 'Kesalahan jaringan atau server';
        document.getElementById('live-error').classList.remove('hidden');
    }
}
function closeLiveModal() {
    document.getElementById('live-modal').classList.add('hidden');
}

// ─── Delete ───────────────────────────────────────────────────────────────
function deleteRouter(id, name) {
    if (!confirm(`Hapus router "${name}"?\nSemua ODP yang terhubung akan dilepas dari router ini.`)) return;
    fetch(`/routers/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return showToast(data.message, 'error');
            showToast(data.message, 'success');
            const idx = ROUTERS.findIndex(r => r.id == id);
            if (idx > -1) ROUTERS.splice(idx, 1);
            const row = document.querySelector(`.router-row[data-id="${id}"]`);
            if (row) row.remove();
            updateRouterStats();
            updateTabCounts();
        }).catch(() => showToast('Gagal menghapus router', 'error'));
}

function deleteOdp(id, name) {
    if (!confirm(`Hapus ODP "${name}"?`)) return;
    fetch(`/odps/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return showToast(data.message, 'error');
            showToast(data.message, 'success');
            const idx = ODPS.findIndex(o => o.id == id);
            if (idx > -1) ODPS.splice(idx, 1);
            const row = document.querySelector(`.odp-row[data-id="${id}"]`);
            if (row) row.remove();
            updateOdpStats();
            updateTabCounts();
        }).catch(() => showToast('Gagal menghapus ODP', 'error'));
}

function deleteOdc(id, name) {
    if (!confirm(`Hapus ODC "${name}"? ODP yang terhubung akan dilepas dari ODC ini.`)) return;
    fetch(`/odcs/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return showToast(data.message, 'error');
            showToast(data.message, 'success');
            const idx = ODCS.findIndex(o => o.id == id);
            if (idx > -1) ODCS.splice(idx, 1);
            const row = document.querySelector(`.odc-row[data-id="${id}"]`);
            if (row) row.remove();
            updateOdcStats();
            updateTabCounts();
        }).catch(() => showToast('Gagal menghapus ODC', 'error'));
}

// ─── DOM helpers ─────────────────────────────────────────────────────────
function upsertRouterRow(r, isNew) {
    const idx = ROUTERS.findIndex(x => x.id == r.id);
    if (idx > -1) ROUTERS[idx] = r; else ROUTERS.push(r);
    updateRouterRowStatus(r);
    if (isNew) location.reload(); // simplest for new rows
    updateTabCounts();
}

function upsertOdpRow(o, isNew) {
    const idx = ODPS.findIndex(x => x.id == o.id);
    if (idx > -1) ODPS[idx] = o; else ODPS.push(o);
    if (isNew) location.reload();
    updateOdpStats();
    updateTabCounts();
}

function updateRouterRowStatus(r) {
    const row = document.querySelector(`.router-row[data-id="${r.id}"]`);
    if (!row) return;

    const cfgs = {
        online:  { bg: 'bg-green-100', text: 'text-green-700', dot: 'bg-green-500',  label: 'Online' },
        offline: { bg: 'bg-red-100',   text: 'text-red-700',   dot: 'bg-red-500',    label: 'Offline' },
        unknown: { bg: 'bg-gray-100',  text: 'text-gray-600',  dot: 'bg-gray-400',   label: 'Belum Cek' },
    };
    const c = cfgs[r.status] ?? cfgs.unknown;
    const badge = row.querySelector('.router-status');
    if (badge) {
        badge.className = `router-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${c.bg} ${c.text}`;
        badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${c.dot}"></span>${c.label}`;
    }
    const pppoe = row.querySelector('.pppoe-badge');
    if (pppoe) pppoe.textContent = r.pppoe_online;

    updateRouterStats();
}

function updateRouterStats() {
    const total   = document.querySelectorAll('.router-row').length;
    const online  = [...document.querySelectorAll('.router-row')].filter(r => r.querySelector('.router-status')?.textContent.includes('Online')).length;
    document.getElementById('rs-total').textContent   = total;
    document.getElementById('rs-online').textContent  = online;
    document.getElementById('rs-offline').textContent = total - online;
}

function updateOdpStats() {
    document.getElementById('odp-stat-total').textContent = ODPS.length;
    document.getElementById('odp-stat-cap').textContent   = ODPS.reduce((s, o) => s + (o.capacity || 0), 0);
}

function updateOdcStats() {
    document.getElementById('odc-stat-total').textContent = ODCS.length;
    document.getElementById('odc-stat-cap').textContent   = ODCS.reduce((s, o) => s + (o.capacity || 0), 0);
}

function updateTabCounts() {
    document.getElementById('tab-router-count').textContent = document.querySelectorAll('.router-row').length;
    document.getElementById('tab-odp-count').textContent    = document.querySelectorAll('.odp-row').length;
    document.getElementById('tab-odc-count').textContent    = document.querySelectorAll('.odc-row').length;
}
</script>

@endsection
