@extends('layouts.app')
@section('title', 'Pelanggan')
@section('page-title', 'Pelanggan')

@php
$avatarGradient = function(string $name): string {
    $g = ['from-blue-400 to-blue-700','from-green-400 to-green-700','from-purple-400 to-purple-700',
          'from-amber-400 to-amber-700','from-red-400 to-red-700','from-pink-400 to-pink-700',
          'from-teal-400 to-teal-700','from-indigo-400 to-indigo-700'];
    return $g[ord(mb_strtoupper(mb_substr($name, 0, 1))) % count($g)];
};
$catCfg = [
    'home'      => ['badge' => 'bg-blue-100 text-blue-700'],
    'bisnis'    => ['badge' => 'bg-purple-100 text-purple-700'],
    'dedicated' => ['badge' => 'bg-amber-100 text-amber-700'],
];
$statusCfg = [
    'aktif'     => ['label' => 'Aktif',     'badge' => 'bg-green-50 text-green-700',  'dot' => 'bg-green-500'],
    'suspend'   => ['label' => 'Suspend',   'badge' => 'bg-amber-50 text-amber-700',  'dot' => 'bg-amber-500'],
    'terminate' => ['label' => 'Terminate', 'badge' => 'bg-red-50 text-red-600',     'dot' => 'bg-red-500'],
];
@endphp

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pelanggan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola data pelanggan aktif dan riwayat layanan</p>
    </div>
    <div class="flex items-center gap-2 self-start sm:self-auto">
        @if(auth()->user()->role === 'admin')
        <button onclick="openDummyModal()"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            Generate Dummy
        </button>
        <button onclick="openModal('create')"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Tambah Pelanggan
        </button>
        @endif
    </div>

</div>

{{-- ===== Stats ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $statCards = [
        ['id'=>'stat-total',     'label'=>'Total Pelanggan', 'value'=>$stats['total'],     'ico'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75', 'bg'=>'bg-blue-50',  'ic'=>'text-blue-600'],
        ['id'=>'stat-aktif',     'label'=>'Aktif',           'value'=>$stats['aktif'],     'ico'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                              'bg'=>'bg-green-50', 'ic'=>'text-green-600'],
        ['id'=>'stat-suspend',   'label'=>'Suspend',         'value'=>$stats['suspend'],   'ico'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                          'bg'=>'bg-amber-50', 'ic'=>'text-amber-600'],
        ['id'=>'stat-terminate', 'label'=>'Terminate',       'value'=>$stats['terminate'], 'ico'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',                             'bg'=>'bg-red-50',   'ic'=>'text-red-500'],
    ];
    @endphp
    @foreach($statCards as $sc)
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

{{-- ===== Toolbar ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    {{-- Search --}}
    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-64">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="cust-search" placeholder="Cari nama, telepon, email..."
               class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1"
               oninput="applyFilters()">
    </div>

    {{-- Status pills --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 sm:pb-0 scrollbar-none flex-shrink-0">
        <button onclick="setFilter('all')" id="filter-all"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors bg-gray-900 text-white">
            Semua&nbsp;<span id="cnt-all">{{ $stats['total'] }}</span>
        </button>
        <button onclick="setFilter('aktif')" id="filter-aktif"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Aktif&nbsp;<span id="cnt-aktif">{{ $stats['aktif'] }}</span>
        </button>
        <button onclick="setFilter('suspend')" id="filter-suspend"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Suspend&nbsp;<span id="cnt-suspend">{{ $stats['suspend'] }}</span>
        </button>
        <button onclick="setFilter('terminate')" id="filter-terminate"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Terminate&nbsp;<span id="cnt-terminate">{{ $stats['terminate'] }}</span>
        </button>
    </div>

    {{-- Package filter --}}
    <div class="sm:ml-auto">
        <select id="pkg-filter" onchange="applyFilters()"
                class="text-xs text-gray-600 border border-gray-200 rounded-xl px-3 py-2 outline-none focus:border-green-400 bg-white cursor-pointer">
            <option value="">Semua Paket</option>
            @foreach($packages as $pkg)
            <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- ===== Table ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[820px]">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/60">
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pl-5 pr-4">Pelanggan</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Telepon / IP</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Paket</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Status</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Bergabung</th>
                <th class="text-right text-xs font-semibold text-gray-400 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody id="cust-tbody" class="divide-y divide-gray-50">
            @forelse($customers as $cust)
            @php
            $pkg = $cust->package;
            $pkgBadge = $pkg ? ($catCfg[$pkg->category]['badge'] ?? 'bg-gray-100 text-gray-600') : '';
            $sCfg = $statusCfg[$cust->status] ?? $statusCfg['aktif'];
            @endphp
            <tr data-cust-row
                data-id="{{ $cust->id }}"
                data-name="{{ strtolower($cust->name) }}"
                data-email="{{ strtolower($cust->email ?? '') }}"
                data-phone="{{ $cust->phone }}"
                data-status="{{ $cust->status }}"
                data-package="{{ $cust->package_id }}"
                class="hover:bg-gray-50/50 transition-colors group">

                {{-- Pelanggan --}}
                <td class="py-3.5 pl-5 pr-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $avatarGradient($cust->name) }} flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ strtoupper(mb_substr($cust->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $cust->name }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $cust->email ?: '—' }}</p>
                        </div>
                    </div>
                </td>

                {{-- Telepon / IP --}}
                <td class="py-3.5 pr-4">
                    <p class="text-sm text-gray-700 whitespace-nowrap">{{ $cust->phone }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $cust->ip_address ?: '—' }}</p>
                </td>

                {{-- Paket --}}
                <td class="py-3.5 pr-4">
                    @if($pkg)
                    <span class="inline-flex text-xs font-semibold {{ $pkgBadge }} px-2.5 py-1 rounded-full whitespace-nowrap">
                        {{ $pkg->name }}
                    </span>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>

                {{-- Status dropdown --}}
                <td class="py-3.5 pr-4" data-status-cell>
                    <div class="relative" @if(auth()->user()->role === 'admin') data-status-dd @endif>
                        <button @if(auth()->user()->role === 'admin') onclick="toggleStatusDd(this)" @else disabled @endif data-id="{{ $cust->id }}"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors {{ auth()->user()->role === 'admin' ? 'cursor-pointer hover:opacity-80' : 'cursor-default' }} select-none
                                       {{ $sCfg['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sCfg['dot'] }}"></span>
                            {{ $sCfg['label'] }}
                            @if(auth()->user()->role === 'admin')
                            <svg class="w-2.5 h-2.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                            @endif
                        </button>
                        @if(auth()->user()->role === 'admin')
                        <div class="dd-menu hidden absolute top-full left-0 z-30 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden min-w-[130px] py-1">
                            <button onclick="setStatus({{ $cust->id }}, 'aktif', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $cust->status==='aktif' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>Aktif
                                @if($cust->status==='aktif')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                            <button onclick="setStatus({{ $cust->id }}, 'suspend', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $cust->status==='suspend' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>Suspend
                                @if($cust->status==='suspend')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                            <button onclick="setStatus({{ $cust->id }}, 'terminate', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $cust->status==='terminate' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>Terminate
                                @if($cust->status==='terminate')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                        </div>
                        @endif
                    </div>
                </td>

                {{-- Bergabung --}}
                <td class="py-3.5 pr-4">
                    <p class="text-sm text-gray-600 whitespace-nowrap">{{ $cust->join_date?->format('d M Y') ?? '—' }}</p>
                </td>

                {{-- Aksi --}}
                <td class="py-3.5 pr-5">
                    <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('customers.show', $cust->id) }}"
                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-green-50 hover:text-green-600 text-gray-500 flex items-center justify-center transition-colors"
                           title="Detail & Monitoring">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <button onclick="openModal('edit', getCust({{ $cust->id }}))"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-colors"
                                title="Edit Pelanggan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="confirmDelete({{ $cust->id }}, {{ json_encode($cust->name) }})"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-500 flex items-center justify-center transition-colors"
                                title="Hapus Pelanggan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr id="empty-row">
                <td colspan="6" class="py-16 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-400">Belum ada pelanggan</p>
                        <p class="text-xs text-gray-300">Klik "Tambah Pelanggan" untuk memulai</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Filter empty state --}}
    <div id="filter-empty" class="hidden py-12 text-center">
        <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <p class="text-sm font-medium text-gray-400">Tidak ada pelanggan yang sesuai filter</p>
        <p class="text-xs text-gray-300 mt-0.5">Coba kata kunci atau filter lain</p>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-50 px-5 py-3">
        <p class="text-xs text-gray-400">
            Menampilkan <span id="row-count" class="font-semibold text-gray-600">{{ $customers->count() }}</span> pelanggan
        </p>
    </div>
</div>

{{-- ===== Create / Edit Modal ===== --}}
<div id="cust-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div id="modal-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4
                    transition-all duration-200 scale-95 opacity-0">

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
                <div>
                    <h2 id="modal-title" class="text-base font-bold text-gray-900">Tambah Pelanggan</h2>
                    <p id="modal-subtitle" class="text-xs text-gray-400 mt-0.5">Isi informasi lengkap pelanggan baru</p>
                </div>
                <button onclick="closeModal()"
                        class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="cust-form" onsubmit="submitForm(event)" novalidate>
                @csrf
                <input type="hidden" id="cust-id">

                <div class="px-6 py-5 overflow-y-auto max-h-[65vh] space-y-5">

                    {{-- ─── Identitas ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Identitas</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2 hidden" id="wrap-customer-number">
                                <label class="lbl">Nomor Pelanggan</label>
                                <input type="text" id="f-customer-number" readonly disabled
                                       class="inp w-full bg-gray-50 text-gray-500 border-gray-200 cursor-not-allowed">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="lbl">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="f-name" name="name" placeholder="cth. Budi Santoso"
                                       class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-name"></p>
                            </div>
                            <div>
                                <label class="lbl">No. Telepon / WhatsApp</label>
                                <input type="text" id="f-phone" name="phone" placeholder="cth. 0812-3456-7890"
                                       class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-phone"></p>
                            </div>
                            <div>
                                <label class="lbl">Email</label>
                                <input type="email" id="f-email" name="email" placeholder="cth. budi@gmail.com"
                                       class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-email"></p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="lbl">Alamat Instalasi</label>
                                <textarea id="f-address" name="address" rows="2"
                                          placeholder="Alamat lengkap lokasi pemasangan"
                                          class="inp w-full resize-none"></textarea>
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-address"></p>
                            </div>
                            <div class="sm:col-span-2">
                                {{-- Header + Tab Switcher --}}
                                <div class="flex items-center justify-between mb-2">
                                    <label class="lbl mb-0">
                                        Titik Lokasi
                                        <span class="text-gray-400 font-normal">(opsional)</span>
                                    </label>
                                    <div class="flex items-center gap-0.5 bg-gray-100 p-0.5 rounded-xl">
                                        <button type="button" id="mtab-map" onclick="switchMapView('map')"
                                                class="mtab-btn flex items-center gap-1 px-2.5 py-1 rounded-[10px] text-[11px] font-semibold transition-all bg-white text-gray-800 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                            Peta
                                        </button>
                                        <button type="button" id="mtab-satellite" onclick="switchMapView('satellite')"
                                                class="mtab-btn flex items-center gap-1 px-2.5 py-1 rounded-[10px] text-[11px] font-semibold transition-all text-gray-500 hover:text-gray-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.188 10.934a8 8 0 01.005 2.132M3.807 13.066a8 8 0 01-.005-2.132M10.934 3.812a8 8 0 012.132-.005M13.066 20.193a8 8 0 01-2.132.005M5.636 5.636l1.414 1.414M16.95 16.95l1.414 1.414M5.636 18.364l1.414-1.414M16.95 7.05l1.414-1.414"/>
                                            </svg>
                                            Satelit
                                        </button>
                                        <button type="button" id="mtab-street" onclick="switchMapView('street')"
                                                class="mtab-btn flex items-center gap-1 px-2.5 py-1 rounded-[10px] text-[11px] font-semibold transition-all text-gray-500 hover:text-gray-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="12" cy="7" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 9c-2 0-4 1-4 3v1h8v-1c0-2-2-3-4-3zm-7 9l2-4m14 4l-2-4M5 21l7-7 7 7"/>
                                            </svg>
                                            Street View
                                        </button>
                                    </div>
                                </div>

                                {{-- Map --}}
                                <div id="customer-map" style="height:280px;"
                                     class="w-full rounded-xl border border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <p class="text-xs text-gray-400" id="map-loading-msg">Memuat peta…</p>
                                </div>

                                {{-- Street View --}}
                                <div id="customer-streetview" style="height:280px; display:none;"
                                     class="w-full rounded-xl border border-gray-200 overflow-hidden bg-gray-100 relative">
                                    <div id="sv-no-coverage" style="display:none;"
                                         class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-gray-100 z-10">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <circle cx="12" cy="7" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 9c-2 0-4 1-4 3v1h8v-1c0-2-2-3-4-3zm-7 9l2-4m14 4l-2-4M5 21l7-7 7 7"/>
                                        </svg>
                                        <p class="text-xs font-medium text-gray-400">Street View tidak tersedia di lokasi ini</p>
                                        <p class="text-[10px] text-gray-300">Coba geser pin ke lokasi lain</p>
                                    </div>
                                </div>

                                {{-- Coordinates --}}
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div>
                                        <label class="lbl">Latitude</label>
                                        <input type="text" id="f-lat" name="latitude" readonly
                                               placeholder="Klik peta untuk menetapkan"
                                               class="inp w-full text-xs font-mono bg-gray-50 cursor-default">
                                    </div>
                                    <div>
                                        <label class="lbl">Longitude</label>
                                        <input type="text" id="f-lng" name="longitude" readonly
                                               placeholder="Klik peta untuk menetapkan"
                                               class="inp w-full text-xs font-mono bg-gray-50 cursor-default">
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-1.5">
                                    <p class="text-[10px] text-gray-400">Klik atau seret pin untuk mengatur titik lokasi</p>
                                    <div class="flex gap-3">
                                        <button type="button" onclick="clearMapLocation()"
                                                class="text-[10px] text-gray-400 hover:text-red-500 font-medium transition-colors">
                                            Hapus Koordinat
                                        </button>
                                        <button type="button" onclick="resetMapLocation()"
                                                class="text-[10px] text-blue-500 hover:text-blue-700 font-medium transition-colors">
                                            Reset ke Default
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Layanan & Status ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Layanan & Status</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="lbl">Paket Internet <span class="text-red-500">*</span></label>
                                <select id="f-package" name="package_id" class="inp w-full">
                                    <option value="">Pilih paket...</option>
                                    @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}">{{ $pkg->name }} — Rp {{ number_format($pkg->price,0,',','.') }}</option>
                                    @endforeach
                                </select>
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-package_id"></p>
                            </div>
                            <div>
                                <label class="lbl">Status Akun <span class="text-red-500">*</span></label>
                                <select id="f-status" name="status" class="inp w-full">
                                    <option value="aktif">Aktif</option>
                                    <option value="suspend">Suspend</option>
                                    <option value="terminate">Terminate</option>
                                </select>
                            </div>
                            <div>
                                <label class="lbl">Tanggal Bergabung <span class="text-red-500">*</span></label>
                                <input type="date" id="f-join" name="join_date" class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-join_date"></p>
                            </div>
                            <div>
                                <label class="lbl">Tanggal Tagihan <span class="text-gray-400 font-normal">(tiap bulan)</span></label>
                                <select id="f-billing-date" name="billing_date" class="inp w-full">
                                    @for($d = 1; $d <= 28; $d++)
                                    <option value="{{ $d }}">Tanggal {{ $d }}</option>
                                    @endfor
                                </select>
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-billing_date"></p>
                                <p class="text-[10px] text-gray-400 mt-1">Tanggal penerbitan tagihan setiap periode</p>
                            </div>
                            <div>
                                <label class="lbl">IP Address</label>
                                <input type="text" id="f-ip" name="ip_address" placeholder="cth. 192.168.1.101"
                                       class="inp w-full font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ─── Jaringan ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Jaringan</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="lbl">Username PPPoE</label>
                                <input type="text" id="f-pppoe" name="pppoe_user" placeholder="cth. pelanggan001"
                                       class="inp w-full font-mono text-sm">
                            </div>
                            <div>
                                <label class="lbl">ID ONT / ONU</label>
                                <input type="text" id="f-onu" name="onu_id" placeholder="cth. HWTC12345678"
                                       class="inp w-full font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ─── Catatan ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Catatan</p>
                        <textarea id="f-notes" name="notes" rows="2"
                                  placeholder="Catatan tambahan (opsional)"
                                  class="inp w-full resize-none"></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="modal-save-btn"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-500 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="modal-save-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Generate Dummy Modal ===== --}}
<div id="dummy-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDummyModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="dummy-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm
                    transition-all duration-200 scale-95 opacity-0">

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Generate Pelanggan Dummy</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Buat data pelanggan contoh secara otomatis</p>
                </div>
                <button onclick="closeDummyModal()"
                        class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="dummy-form" onsubmit="submitDummy(event)">
                <div class="px-6 py-5">
                    <label class="lbl">Jumlah Data (1 - 100)</label>
                    <input type="number" id="f-dummy-count" min="1" max="100" value="10"
                           class="inp w-full" required>
                    <p class="text-[10px] text-gray-400 mt-2 italic">Data akan dibuat dengan identitas acak dan status Aktif.</p>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button type="button" onclick="closeDummyModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="dummy-save-btn"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded-xl transition-colors disabled:opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <span id="dummy-save-text">Generate</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Delete Confirm Modal ===== --}}

<div id="del-modal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDelModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="del-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6
                    transition-all duration-200 scale-95 opacity-0">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Hapus Pelanggan?</h3>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                        Data <strong id="del-name" class="text-gray-800"></strong> akan dihapus permanen beserta seluruh riwayatnya.
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button onclick="closeDelModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button id="del-confirm-btn" onclick="executeDelete()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span id="del-btn-text">Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[60] flex flex-col gap-2 pointer-events-none"></div>

@endsection

@push('styles')
<style>
.lbl  { display:block; font-size:.7rem; font-weight:600; color:#4b5563; margin-bottom:.375rem; }
.inp  { padding:.625rem .875rem; font-size:.875rem; border:1px solid #e5e7eb; border-radius:.75rem;
        outline:none; background:#f9fafb; transition:all .15s;
        /* focus styles applied via JS class injection */ }
.inp:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); background:#fff; }
textarea.inp { resize:none; }
</style>
@endpush

@push('scripts')
<script>
// ─── Server data ─────────────────────────────────────────────────────────────
const __custs    = @json($customers->map->toJsonData());
const __packages = @json($packages);
const CSRF       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ─── Config ──────────────────────────────────────────────────────────────────
const STATUS = {
    aktif:     { label:'Aktif',     badge:'bg-green-50 text-green-700',  dot:'bg-green-500'  },
    suspend:   { label:'Suspend',   badge:'bg-amber-50 text-amber-700',  dot:'bg-amber-500'  },
    terminate: { label:'Terminate', badge:'bg-red-50 text-red-600',      dot:'bg-red-500'    },
};
const PKG_CAT = {
    home:      'bg-blue-100 text-blue-700',
    bisnis:    'bg-purple-100 text-purple-700',
    dedicated: 'bg-amber-100 text-amber-700',
};
const AVATAR_GRADIENTS = [
    'from-blue-400 to-blue-700','from-green-400 to-green-700','from-purple-400 to-purple-700',
    'from-amber-400 to-amber-700','from-red-400 to-red-700','from-pink-400 to-pink-700',
    'from-teal-400 to-teal-700','from-indigo-400 to-indigo-700',
];

// ─── State ───────────────────────────────────────────────────────────────────
let activeFilter   = 'all';
let deleteTargetId = null;

// ─── Helpers ─────────────────────────────────────────────────────────────────
function getCust(id)    { return __custs.find(c => c.id === id) || null; }
function getPkg(id)     { return __packages.find(p => p.id === id) || null; }
function esc(s)         { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function avatarGrad(nm) { return AVATAR_GRADIENTS[nm.charCodeAt(0) % AVATAR_GRADIENTS.length]; }
function fmtDate(d)     {
    if (!d) return '—';
    const parts = d.split('-');
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    return `${parseInt(parts[2])} ${months[parseInt(parts[1])-1]} ${parts[0]}`;
}

// ─── Build Status Dropdown HTML ───────────────────────────────────────────────
function buildStatusCell(id, status) {
    const s = STATUS[status] || STATUS.aktif;
    const opts = Object.entries(STATUS).map(([k,v]) => `
        <button onclick="setStatus(${id},'${k}',this)"
                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors ${status===k?'font-semibold text-gray-900':'text-gray-600'}">
            <span class="w-1.5 h-1.5 rounded-full ${v.dot} flex-shrink-0"></span>${v.label}
            ${status===k ? '<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' : ''}
        </button>`).join('');
    return `<div class="relative" data-status-dd>
        <button onclick="toggleStatusDd(this)" data-id="${id}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors cursor-pointer select-none ${s.badge} hover:opacity-80">
            <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>${s.label}
            <svg class="w-2.5 h-2.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div class="dd-menu hidden absolute top-full left-0 z-30 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden min-w-[130px] py-1">
            ${opts}
        </div>
    </div>`;
}

// ─── Build Package Badge ──────────────────────────────────────────────────────
function buildPkgBadge(pkgId) {
    const p = getPkg(pkgId);
    if (!p) return '<span class="text-xs text-gray-400">—</span>';
    const cls = PKG_CAT[p.category] || 'bg-gray-100 text-gray-600';
    return `<span class="inline-flex text-xs font-semibold ${cls} px-2.5 py-1 rounded-full whitespace-nowrap">${esc(p.name)}</span>`;
}

// ─── Build Table Row ──────────────────────────────────────────────────────────
function buildRow(c) {
    const grad  = avatarGrad(c.name);
    const cData = esc(JSON.stringify(c));
    return `<tr data-cust-row data-id="${c.id}"
        data-name="${esc(c.name.toLowerCase())}"
        data-email="${esc(c.email.toLowerCase())}"
        data-phone="${esc(c.phone)}"
        data-status="${esc(c.status)}"
        data-package="${c.package_id||''}"
        class="hover:bg-gray-50/50 transition-colors group border-b border-gray-50">
        <td class="py-3.5 pl-5 pr-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br ${grad} flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    ${esc(c.name.charAt(0).toUpperCase())}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">${esc(c.name)}</p>
                    <p class="text-xs text-gray-400 truncate max-w-[160px]">${c.email ? esc(c.email) : '—'}</p>
                </div>
            </div>
        </td>
        <td class="py-3.5 pr-4">
            <p class="text-sm text-gray-700 whitespace-nowrap">${esc(c.phone)}</p>
            <p class="text-xs text-gray-400 font-mono">${c.ip_address ? esc(c.ip_address) : '—'}</p>
        </td>
        <td class="py-3.5 pr-4">${buildPkgBadge(c.package_id)}</td>
        <td class="py-3.5 pr-4" data-status-cell>${buildStatusCell(c.id, c.status)}</td>
        <td class="py-3.5 pr-4">
            <p class="text-sm text-gray-600 whitespace-nowrap">${fmtDate(c.join_date)}</p>
        </td>
        <td class="py-3.5 pr-5">
            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick='openModal("edit",JSON.parse(this.dataset.c))' data-c="${cData}"
                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-colors" title="Edit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick='confirmDelete(${c.id},${JSON.stringify(c.name)})'
                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-500 flex items-center justify-center transition-colors" title="Hapus">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>`;
}

// ─── Filter & Search ─────────────────────────────────────────────────────────
function setFilter(f) {
    activeFilter = f;
    document.querySelectorAll('.filter-pill').forEach(b => {
        b.classList.remove('bg-gray-900','text-white');
        b.classList.add('text-gray-500','hover:bg-gray-100');
    });
    const btn = document.getElementById('filter-' + f);
    btn.classList.add('bg-gray-900','text-white');
    btn.classList.remove('text-gray-500','hover:bg-gray-100');
    applyFilters();
}

function applyFilters() {
    const q      = document.getElementById('cust-search').value.toLowerCase();
    const pkgId  = document.getElementById('pkg-filter').value;
    let visible  = 0;

    document.querySelectorAll('[data-cust-row]').forEach(row => {
        const matchSearch  = !q
            || row.dataset.name.includes(q)
            || row.dataset.phone.includes(q)
            || row.dataset.email.includes(q);
        const matchStatus  = activeFilter === 'all' || row.dataset.status === activeFilter;
        const matchPkg     = !pkgId || row.dataset.package === pkgId;
        const show         = matchSearch && matchStatus && matchPkg;
        row.style.display  = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('filter-empty').classList.toggle('hidden', visible > 0);
    document.getElementById('row-count').textContent = visible;
}

// ─── Stats update ─────────────────────────────────────────────────────────────
function updateStats() {
    const rows = [...document.querySelectorAll('[data-cust-row]')];
    const total    = rows.length;
    const aktif    = rows.filter(r => r.dataset.status === 'aktif').length;
    const suspend  = rows.filter(r => r.dataset.status === 'suspend').length;
    const term     = rows.filter(r => r.dataset.status === 'terminate').length;
    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-aktif').textContent     = aktif;
    document.getElementById('stat-suspend').textContent   = suspend;
    document.getElementById('stat-terminate').textContent = term;
    document.getElementById('cnt-all').textContent        = total;
    document.getElementById('cnt-aktif').textContent      = aktif;
    document.getElementById('cnt-suspend').textContent    = suspend;
    document.getElementById('cnt-terminate').textContent  = term;
}

// ─── Status Dropdown ─────────────────────────────────────────────────────────
function toggleStatusDd(btn) {
    const dd = btn.nextElementSibling;
    document.querySelectorAll('.dd-menu').forEach(d => { if (d !== dd) d.classList.add('hidden'); });
    dd.classList.toggle('hidden');
}

document.addEventListener('click', e => {
    if (!e.target.closest('[data-status-dd]')) {
        document.querySelectorAll('.dd-menu').forEach(d => d.classList.add('hidden'));
    }
});

async function setStatus(id, status, btn) {
    btn.closest('.dd-menu').classList.add('hidden');
    try {
        const res  = await fetch(`/customers/${id}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ status }),
        });
        const data = await res.json();
        if (data.success) {
            const row  = document.querySelector(`[data-cust-row][data-id="${id}"]`);
            row.dataset.status = status;
            row.querySelector('[data-status-cell]').innerHTML = buildStatusCell(id, status);
            const c = __custs.find(c => c.id === id);
            if (c) c.status = status;
            showToast('success', data.message);
            updateStats();
        } else {
            showToast('error', data.message || 'Gagal mengubah status.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    }
}

// ─── Dummy Generator ─────────────────────────────────────────────────────────
function openDummyModal() {
    const modal = document.getElementById('dummy-modal');
    const card  = document.getElementById('dummy-card');
    document.getElementById('dummy-form').reset();
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95','opacity-0');
        card.classList.add('scale-100','opacity-100');
    }));
}

function closeDummyModal() {
    const card  = document.getElementById('dummy-card');
    const modal = document.getElementById('dummy-modal');
    card.classList.add('scale-95','opacity-0');
    card.classList.remove('scale-100','opacity-100');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 200);
}

async function submitDummy(e) {
    e.preventDefault();
    const btn   = document.getElementById('dummy-save-btn');
    const txt   = document.getElementById('dummy-save-text');
    const count = document.getElementById('f-dummy-count').value;

    btn.disabled    = true;
    txt.textContent = 'Generating…';

    try {
        const res  = await fetch('/customers/generate-dummy', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ count }),
        });
        const data = await res.json();

        if (data.success) {
            closeDummyModal();
            showToast('success', data.message);

            const tbody  = document.getElementById('cust-tbody');
            const emptyR = document.getElementById('empty-row');
            if (emptyR) emptyR.remove();

            // Add new rows to table and state
            data.customers.forEach(c => {
                tbody.insertAdjacentHTML('afterbegin', buildRow(c));
                __custs.unshift(c);
            });

            updateStats();
            applyFilters();
        } else {
            showToast('error', data.message || 'Gagal generate data.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    } finally {
        btn.disabled    = false;
        txt.textContent = 'Generate';
    }
}

// ─── Google Maps ─────────────────────────────────────────────────────────────
const MAP_DEFAULT_LAT = -5.3747438302510755;
const MAP_DEFAULT_LNG = 105.0802923602811;
let custMap        = null;
let custMarker     = null;
let custStreetView = null;
let currentMapView = 'map';
// undefined = no pending; null = create mode; number = edit mode coords
let _mapPendingLat = undefined;
let _mapPendingLng = undefined;

window.initCustomerMapLib = function () {
    window._mapsApiLoaded = true;
    if (_mapPendingLat !== undefined) {
        const lat = _mapPendingLat, lng = _mapPendingLng;
        _mapPendingLat = undefined; _mapPendingLng = undefined;
        setTimeout(() => { initCustMap(); setupMapForModal(lat, lng); }, 100);
    }
};

function initCustMap() {
    if (custMap || typeof google === 'undefined' || !google.maps) return;
    const loadingMsg = document.getElementById('map-loading-msg');
    if (loadingMsg) loadingMsg.remove();

    custMap = new google.maps.Map(document.getElementById('customer-map'), {
        center: { lat: MAP_DEFAULT_LAT, lng: MAP_DEFAULT_LNG },
        zoom: 15,
        mapTypeId: 'roadmap',
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_BOTTOM },
        gestureHandling: 'cooperative',
    });

    custMarker = new google.maps.Marker({
        map: null,
        draggable: true,
        title: 'Lokasi Pelanggan',
        animation: google.maps.Animation.DROP,
    });

    custMarker.addListener('dragend', () => {
        syncCoordsFromMarker();
        if (custStreetView) custStreetView.setPosition(custMarker.getPosition());
    });
    custMap.addListener('click', function (e) {
        custMarker.setPosition(e.latLng);
        custMarker.setMap(custMap);
        syncCoordsFromMarker();
        if (custStreetView) custStreetView.setPosition(e.latLng);
    });
}

function syncCoordsFromMarker() {
    const pos = custMarker.getPosition();
    if (!pos) return;
    document.getElementById('f-lat').value = pos.lat().toFixed(7);
    document.getElementById('f-lng').value = pos.lng().toFixed(7);
}

function setupMapForModal(lat, lng) {
    if (!custMap) return;
    const hasCoords = lat != null && lng != null;
    const targetLat = hasCoords ? parseFloat(lat) : MAP_DEFAULT_LAT;
    const targetLng = hasCoords ? parseFloat(lng) : MAP_DEFAULT_LNG;
    const pos = new google.maps.LatLng(targetLat, targetLng);

    custMap.setMapTypeId('roadmap');
    google.maps.event.trigger(custMap, 'resize');
    custMap.setCenter(pos);

    if (hasCoords) {
        custMarker.setPosition(pos);
        custMarker.setMap(custMap);
        document.getElementById('f-lat').value = parseFloat(lat).toFixed(7);
        document.getElementById('f-lng').value = parseFloat(lng).toFixed(7);
    } else {
        custMarker.setMap(null);
        document.getElementById('f-lat').value = '';
        document.getElementById('f-lng').value = '';
    }
}

// ─── Map View Tabs ────────────────────────────────────────────────────────────
function switchMapView(view) {
    currentMapView = view;
    document.querySelectorAll('.mtab-btn').forEach(b => {
        b.classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
        b.classList.add('text-gray-500');
    });
    const active = document.getElementById('mtab-' + view);
    if (active) { active.classList.add('bg-white', 'text-gray-800', 'shadow-sm'); active.classList.remove('text-gray-500'); }

    const mapEl = document.getElementById('customer-map');
    const svEl  = document.getElementById('customer-streetview');

    if (view === 'street') {
        mapEl.style.display = 'none';
        svEl.style.display  = '';
        openStreetView();
    } else {
        mapEl.style.display = '';
        svEl.style.display  = 'none';
        if (custMap) {
            custMap.setMapTypeId(view === 'satellite' ? 'hybrid' : 'roadmap');
            google.maps.event.trigger(custMap, 'resize');
            const c = (custMarker && custMarker.getMap()) ? custMarker.getPosition()
                      : new google.maps.LatLng(MAP_DEFAULT_LAT, MAP_DEFAULT_LNG);
            custMap.setCenter(c);
        }
    }
}

function openStreetView() {
    if (!custMap || typeof google === 'undefined') return;
    const pos = (custMarker && custMarker.getMap()) ? custMarker.getPosition() : custMap.getCenter();

    // Check coverage first
    const svc = new google.maps.StreetViewService();
    svc.getPanorama({ location: pos, radius: 100 }, function (data, status) {
        const noEl = document.getElementById('sv-no-coverage');
        if (status !== google.maps.StreetViewStatus.OK) {
            if (noEl) noEl.style.display = '';
            return;
        }
        if (noEl) noEl.style.display = 'none';

        if (!custStreetView) {
            custStreetView = new google.maps.StreetViewPanorama(
                document.getElementById('customer-streetview'), {
                    position: pos,
                    pov: { heading: 0, pitch: 5 },
                    zoom: 1,
                    addressControl: true,
                    addressControlOptions: { position: google.maps.ControlPosition.BOTTOM_CENTER },
                    fullscreenControl: false,
                    motionTracking: false,
                    motionTrackingControl: false,
                    zoomControl: true,
                }
            );
            custMap.setStreetView(custStreetView);
        } else {
            custStreetView.setPosition(pos);
            google.maps.event.trigger(custStreetView, 'resize');
        }
    });
}

function resetMapTabs() {
    currentMapView = 'map';
    document.querySelectorAll('.mtab-btn').forEach(b => {
        b.classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
        b.classList.add('text-gray-500');
    });
    const mapTab = document.getElementById('mtab-map');
    if (mapTab) { mapTab.classList.add('bg-white', 'text-gray-800', 'shadow-sm'); mapTab.classList.remove('text-gray-500'); }
    document.getElementById('customer-map').style.display = '';
    document.getElementById('customer-streetview').style.display = 'none';
}

function openMapOnModalShow(lat, lng) {
    resetMapTabs();
    setTimeout(() => {
        if (window._mapsApiLoaded) {
            initCustMap();
            setupMapForModal(lat, lng);
        } else {
            _mapPendingLat = lat;
            _mapPendingLng = lng;
        }
    }, 250);
}

function resetMapLocation() {
    if (!custMap || !custMarker) return;
    const pos = new google.maps.LatLng(MAP_DEFAULT_LAT, MAP_DEFAULT_LNG);
    custMap.setCenter(pos);
    custMarker.setPosition(pos);
    custMarker.setMap(custMap);
    syncCoordsFromMarker();
    if (custStreetView) custStreetView.setPosition(pos);
}

function clearMapLocation() {
    if (custMarker) custMarker.setMap(null);
    document.getElementById('f-lat').value = '';
    document.getElementById('f-lng').value = '';
}

// ─── Modal open / close ───────────────────────────────────────────────────────

function openModal(mode, cust = null) {
    const modal = document.getElementById('cust-modal');
    const card  = document.getElementById('modal-card');

    document.getElementById('cust-form').reset();
    clearErrors();
    document.getElementById('cust-id').value = '';

    // Default join date to today
    document.getElementById('f-join').value = new Date().toISOString().split('T')[0];

    if (mode === 'edit' && cust) {
        document.getElementById('modal-title').textContent    = 'Edit Pelanggan';
        document.getElementById('modal-subtitle').textContent = 'Ubah data pelanggan';
        document.getElementById('modal-save-text').textContent = 'Perbarui';
        document.getElementById('cust-id').value  = cust.id;
        document.getElementById('f-name').value   = cust.name;
        document.getElementById('f-phone').value  = cust.phone;
        document.getElementById('f-email').value  = cust.email || '';
        document.getElementById('f-address').value = cust.address || '';
        document.getElementById('f-package').value = cust.package_id || '';
        document.getElementById('f-status').value  = cust.status;
        document.getElementById('f-join').value    = cust.join_date || '';
        document.getElementById('f-ip').value      = cust.ip_address || '';
        document.getElementById('f-pppoe').value   = cust.pppoe_user || '';
        document.getElementById('f-onu').value     = cust.onu_id || '';
        document.getElementById('f-notes').value   = cust.notes || '';
        document.getElementById('f-billing-date').value = cust.billing_date || 1;
        document.getElementById('f-lat').value     = cust.latitude != null ? cust.latitude : '';
        document.getElementById('f-lng').value     = cust.longitude != null ? cust.longitude : '';

        document.getElementById('wrap-customer-number').classList.remove('hidden');
        document.getElementById('f-customer-number').value = cust.customer_number || '';
    } else {
        document.getElementById('modal-title').textContent    = 'Tambah Pelanggan';
        document.getElementById('modal-subtitle').textContent = 'Isi informasi lengkap pelanggan baru';
        document.getElementById('modal-save-text').textContent = 'Simpan';

        document.getElementById('wrap-customer-number').classList.add('hidden');
        document.getElementById('f-customer-number').value = '';
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95','opacity-0');
        card.classList.add('scale-100','opacity-100');
    }));

    const mapLat = (mode === 'edit' && cust && cust.latitude != null) ? cust.latitude : null;
    const mapLng = (mode === 'edit' && cust && cust.longitude != null) ? cust.longitude : null;
    openMapOnModalShow(mapLat, mapLng);
}

function closeModal() {
    const card  = document.getElementById('modal-card');
    const modal = document.getElementById('cust-modal');
    card.classList.add('scale-95','opacity-0');
    card.classList.remove('scale-100','opacity-100');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 200);
}

// ─── Form submit ─────────────────────────────────────────────────────────────
async function submitForm(e) {
    e.preventDefault();
    const id    = document.getElementById('cust-id').value;
    const isNew = !id;
    const url   = isNew ? '/customers' : `/customers/${id}`;
    const btn   = document.getElementById('modal-save-btn');
    const txt   = document.getElementById('modal-save-text');

    clearErrors();
    btn.disabled    = true;
    txt.textContent = 'Menyimpan…';

    const fd = new FormData(document.getElementById('cust-form'));
    if (!isNew) fd.append('_method', 'PUT');

    try {
        const res  = await fetch(url, {
            method: 'POST',
            body: fd,
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();

        if (res.ok && data.success) {
            closeModal();
            showToast('success', data.message);

            const tbody  = document.getElementById('cust-tbody');
            const emptyR = document.getElementById('empty-row');

            if (isNew) {
                if (emptyR) emptyR.remove();
                tbody.insertAdjacentHTML('afterbegin', buildRow(data.customer));
                __custs.unshift(data.customer);
            } else {
                const existing = document.querySelector(`[data-cust-row][data-id="${id}"]`);
                if (existing) existing.outerHTML = buildRow(data.customer);
                const idx = __custs.findIndex(c => c.id === data.customer.id);
                if (idx !== -1) __custs[idx] = data.customer;
            }
            updateStats();
            applyFilters();
        } else if (res.status === 422 && data.errors) {
            showErrors(data.errors);
            showToast('error', 'Periksa kembali isian form.');
        } else {
            showToast('error', data.message || 'Terjadi kesalahan.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
    } finally {
        btn.disabled    = false;
        txt.textContent = isNew ? 'Simpan' : 'Perbarui';
    }
}

// ─── Validation errors ────────────────────────────────────────────────────────
const ERR_MAP = {
    name: ['err-name','f-name'], phone: ['err-phone','f-phone'],
    email: ['err-email','f-email'], address: ['err-address','f-address'],
    package_id: ['err-package_id','f-package'], join_date: ['err-join_date','f-join'],
    billing_date: ['err-billing_date','f-billing-date'],
};

function showErrors(errors) {
    Object.entries(errors).forEach(([f, msgs]) => {
        const cfg = ERR_MAP[f];
        if (!cfg) return;
        const errEl = document.getElementById(cfg[0]);
        const inpEl = document.getElementById(cfg[1]);
        if (errEl) { errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
        if (inpEl) inpEl.style.borderColor = '#fca5a5';
    });
}

function clearErrors() {
    document.querySelectorAll('.err').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
    document.querySelectorAll('.inp').forEach(el => el.style.borderColor = '');
}

// ─── Delete ───────────────────────────────────────────────────────────────────
function confirmDelete(id, name) {
    deleteTargetId = id;
    document.getElementById('del-name').textContent = name;
    const modal = document.getElementById('del-modal');
    const card  = document.getElementById('del-card');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95','opacity-0');
        card.classList.add('scale-100','opacity-100');
    }));
}

function closeDelModal() {
    const card  = document.getElementById('del-card');
    const modal = document.getElementById('del-modal');
    card.classList.add('scale-95','opacity-0');
    card.classList.remove('scale-100','opacity-100');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; deleteTargetId = null; }, 200);
}

async function executeDelete() {
    if (!deleteTargetId) return;
    const id  = deleteTargetId;
    const btn = document.getElementById('del-confirm-btn');
    const txt = document.getElementById('del-btn-text');
    btn.disabled    = true;
    txt.textContent = 'Menghapus…';

    try {
        const res  = await fetch(`/customers/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();

        if (data.success) {
            closeDelModal();
            showToast('success', data.message);
            const row = document.querySelector(`[data-cust-row][data-id="${id}"]`);
            if (row) {
                row.style.transition = 'opacity .25s, transform .25s';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(16px)';
                setTimeout(() => {
                    row.remove();
                    const idx = __custs.findIndex(c => c.id === id);
                    if (idx !== -1) __custs.splice(idx, 1);
                    if (!document.querySelector('[data-cust-row]')) {
                        document.getElementById('cust-tbody').innerHTML =
                            `<tr id="empty-row"><td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400">Belum ada pelanggan</p>
                                    <p class="text-xs text-gray-300">Klik "Tambah Pelanggan" untuk memulai</p>
                                </div>
                            </td></tr>`;
                    }
                    updateStats();
                    applyFilters();
                }, 260);
            }
        } else {
            showToast('error', data.message || 'Gagal menghapus pelanggan.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    } finally {
        btn.disabled    = false;
        txt.textContent = 'Hapus';
    }
}

// ─── Toast ────────────────────────────────────────────────────────────────────
function showToast(type, message) {
    const container = document.getElementById('toast-container');
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
    };
    const clrs = { success:'bg-gray-900 text-white', error:'bg-red-600 text-white', warning:'bg-amber-500 text-white' };
    const icls = { success:'text-green-400', error:'text-red-200', warning:'text-amber-100' };

    const t = document.createElement('div');
    t.setAttribute('data-toast','');
    t.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl
        text-sm font-medium transition-all duration-300 translate-y-4 opacity-0
        min-w-[260px] max-w-sm ${clrs[type]||clrs.success}`;
    t.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 ${icls[type]}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            ${icons[type]}
        </svg>
        <span class="flex-1 leading-snug">${message}</span>
        <button onclick="this.closest('[data-toast]').remove()" class="ml-1 opacity-60 hover:opacity-100 transition-opacity flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    container.appendChild(t);
    requestAnimationFrame(() => requestAnimationFrame(() => t.classList.remove('translate-y-4','opacity-0')));
    let timer = setTimeout(() => dismiss(t), 4000);
    t.addEventListener('mouseenter', () => clearTimeout(timer));
    t.addEventListener('mouseleave', () => { timer = setTimeout(() => dismiss(t), 1500); });
}

function dismiss(t) {
    if (!t.parentElement) return;
    t.classList.add('translate-y-4','opacity-0');
    setTimeout(() => t.remove(), 300);
}

// ─── Escape key ───────────────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (!document.getElementById('cust-modal').classList.contains('hidden')) closeModal();
    else if (!document.getElementById('del-modal').classList.contains('hidden'))  closeDelModal();
    else document.querySelectorAll('.dd-menu').forEach(d => d.classList.add('hidden'));
});
</script>
@endpush

@if(env('GOOGLE_MAPS_API_KEY'))
@push('scripts')
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initCustomerMapLib">
</script>
@endpush
@endif
