@extends('layouts.app')
@section('title', 'Paket Internet')
@section('page-title', 'Paket Internet')

@php
$catCfg = [
    'home'      => ['label' => 'Home',      'badge' => 'bg-blue-100 text-blue-700',    'dot' => 'bg-blue-500'],
    'bisnis'    => ['label' => 'Bisnis',    'badge' => 'bg-purple-100 text-purple-700', 'dot' => 'bg-purple-500'],
    'dedicated' => ['label' => 'Dedicated', 'badge' => 'bg-amber-100 text-amber-700',  'dot' => 'bg-amber-500'],
];
@endphp

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Paket Internet</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola paket layanan yang tersedia untuk pelanggan</p>
    </div>
    <div class="flex items-center gap-2 self-start sm:self-auto">
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
            Tambah Paket
        </button>
    </div>

</div>

{{-- ===== Stats ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $statCards = [
        ['id'=>'stat-total',    'label'=>'Total Paket',  'value'=>$stats['total'],    'ico'=>'M4 6h16M4 10h16M4 14h16M4 18h7',                                                                     'bg'=>'bg-gray-100',   'ic'=>'text-gray-500'],
        ['id'=>'stat-aktif',    'label'=>'Paket Aktif',  'value'=>$stats['aktif'],    'ico'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                      'bg'=>'bg-green-50',   'ic'=>'text-green-600'],
        ['id'=>'stat-nonaktif', 'label'=>'Nonaktif',     'value'=>$stats['nonaktif'], 'ico'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',     'bg'=>'bg-red-50',     'ic'=>'text-red-500'],
        ['id'=>'stat-kategori', 'label'=>'Kategori',     'value'=>(int)($stats['home']>0)+(int)($stats['bisnis']>0)+(int)($stats['dedicated']>0), 'ico'=>'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'bg'=>'bg-blue-50', 'ic'=>'text-blue-600'],
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
    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-60">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="pkg-search" placeholder="Cari nama paket..."
               class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1"
               oninput="applyFilters()">
    </div>

    {{-- Category pills --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 sm:pb-0 scrollbar-none">
        <button onclick="setFilter('all')" id="filter-all"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors bg-gray-900 text-white">
            Semua&nbsp;<span id="cnt-all">{{ $stats['total'] }}</span>
        </button>
        <button onclick="setFilter('home')" id="filter-home"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Home&nbsp;<span id="cnt-home">{{ $stats['home'] }}</span>
        </button>
        <button onclick="setFilter('bisnis')" id="filter-bisnis"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Bisnis&nbsp;<span id="cnt-bisnis">{{ $stats['bisnis'] }}</span>
        </button>
        <button onclick="setFilter('dedicated')" id="filter-dedicated"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Dedicated&nbsp;<span id="cnt-dedicated">{{ $stats['dedicated'] }}</span>
        </button>
    </div>

    {{-- Show inactive toggle --}}
    <label class="sm:ml-auto flex items-center gap-2 text-xs text-gray-500 cursor-pointer select-none flex-shrink-0">
        <div class="relative">
            <input type="checkbox" id="show-inactive" class="sr-only peer" checked onchange="applyFilters()">
            <div class="w-8 h-4 bg-gray-200 peer-checked:bg-green-500 rounded-full transition-colors"></div>
            <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-4 shadow-sm"></div>
        </div>
        Tampilkan nonaktif
    </label>
</div>

{{-- ===== Table ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[720px]">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/60">
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pl-5 pr-4">Paket</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Kategori</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Kecepatan</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Harga / Bulan</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Status</th>
                <th class="text-right text-xs font-semibold text-gray-400 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody id="pkg-tbody" class="divide-y divide-gray-50">
            @forelse($packages as $pkg)
            @php $cfg = $catCfg[$pkg->category] ?? $catCfg['home']; @endphp
            <tr data-pkg-row
                data-id="{{ $pkg->id }}"
                data-name="{{ strtolower($pkg->name) }}"
                data-category="{{ $pkg->category }}"
                data-active="{{ $pkg->is_active ? '1' : '0' }}"
                class="hover:bg-gray-50/50 transition-colors group">

                <td class="py-3.5 pl-5 pr-4">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full {{ $cfg['dot'] }} flex-shrink-0 mt-0.5"></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $pkg->name }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ $pkg->description ?: '—' }}</p>
                        </div>
                    </div>
                </td>

                <td class="py-3.5 pr-4">
                    <span class="inline-flex text-xs font-semibold {{ $cfg['badge'] }} px-2.5 py-1 rounded-full whitespace-nowrap">
                        {{ $cfg['label'] }}
                    </span>
                </td>

                <td class="py-3.5 pr-4">
                    <p class="text-sm font-semibold text-gray-800 whitespace-nowrap">
                        ↓&nbsp;{{ $pkg->speed_download }}&nbsp;/&nbsp;↑&nbsp;{{ $pkg->speed_upload }}&nbsp;Mbps
                    </p>
                    @if($pkg->contention)
                    <p class="text-xs text-gray-400">Rasio {{ $pkg->contention }}</p>
                    @endif
                </td>

                <td class="py-3.5 pr-4">
                    <p class="text-sm font-bold text-gray-900 whitespace-nowrap">{{ $pkg->formatted_price }}</p>
                    <p class="text-xs text-gray-400">/bulan</p>
                </td>

                <td class="py-3.5 pr-4">
                    <button onclick="toggleStatus({{ $pkg->id }}, this)"
                            class="status-btn inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors
                                   {{ $pkg->is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $pkg->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </td>

                <td class="py-3.5 pr-5">
                    <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="openModal('edit', getPkg({{ $pkg->id }}))"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-colors"
                                title="Edit Paket">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="confirmDelete({{ $pkg->id }}, {{ json_encode($pkg->name) }})"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-500 flex items-center justify-center transition-colors"
                                title="Hapus Paket">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="empty-row">
                <td colspan="6">
                    @include('packages._empty')
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Filter empty state (shown by JS) --}}
    <div id="filter-empty" class="hidden py-14 text-center">
        <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <p class="text-sm font-medium text-gray-400">Tidak ada paket yang sesuai filter</p>
        <p class="text-xs text-gray-300 mt-0.5">Coba ubah kata kunci atau pilih kategori lain</p>
    </div>

    {{-- Footer: row count --}}
    <div id="table-footer" class="border-t border-gray-50 px-5 py-3 flex items-center justify-between">
        <p class="text-xs text-gray-400" id="row-count-label">
            Menampilkan <span id="row-count" class="font-semibold text-gray-600">{{ $packages->count() }}</span> paket
        </p>
    </div>
</div>

{{-- ===== Create / Edit Modal ===== --}}
<div id="pkg-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div id="modal-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg my-4
                    transition-all duration-200 scale-95 opacity-0">

            {{-- Modal Header --}}
            <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
                <div>
                    <h2 id="modal-title" class="text-base font-bold text-gray-900">Tambah Paket</h2>
                    <p id="modal-subtitle" class="text-xs text-gray-400 mt-0.5">Isi detail paket internet baru</p>
                </div>
                <button onclick="closeModal()"
                        class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="pkg-form" onsubmit="submitForm(event)" novalidate>
                @csrf
                <input type="hidden" id="pkg-id">

                <div class="px-6 py-5 space-y-4">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Nama Paket <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="f-name" name="name" placeholder="cth. Home 20 Mbps"
                               class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        <p class="err hidden text-xs text-red-500 mt-1" id="err-name"></p>
                    </div>

                    {{-- Kategori + Kontention --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="f-category" name="category"
                                    class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                <option value="">Pilih kategori</option>
                                <option value="home">Home</option>
                                <option value="bisnis">Bisnis</option>
                                <option value="dedicated">Dedicated</option>
                            </select>
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-category"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rasio Kontention</label>
                            <input type="text" id="f-contention" name="contention" placeholder="cth. 1:8"
                                   class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        </div>
                    </div>

                    {{-- Kecepatan --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Download <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="f-dl" name="speed_download" min="1" max="10000" placeholder="20"
                                       class="inp w-full px-3.5 py-2.5 pr-14 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">Mbps</span>
                            </div>
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-speed_download"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Upload <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="f-ul" name="speed_upload" min="1" max="10000" placeholder="10"
                                       class="inp w-full px-3.5 py-2.5 pr-14 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">Mbps</span>
                            </div>
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-speed_upload"></p>
                        </div>
                    </div>

                    {{-- Harga + Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Harga / Bulan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none font-medium">Rp</span>
                                <input type="number" id="f-price" name="price" min="0" placeholder="250000"
                                       class="inp w-full pl-9 pr-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                            </div>
                            <p class="err hidden text-xs text-red-500 mt-1" id="err-price"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                            <select id="f-active" name="is_active"
                                    class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi</label>
                        <textarea id="f-desc" name="description" rows="2"
                                  placeholder="Deskripsi singkat paket (opsional)"
                                  class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white resize-none"></textarea>
                    </div>

                    {{-- Urutan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Urutan Tampil</label>
                        <input type="number" id="f-sort" name="sort_order" min="0" placeholder="0"
                               class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white">
                        <p class="text-[11px] text-gray-400 mt-1">Angka lebih kecil ditampilkan lebih dahulu</p>
                    </div>
                </div>

                {{-- Modal Footer --}}
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
                    <h2 class="text-base font-bold text-gray-900">Generate Paket Dummy</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Buat data paket internet secara otomatis</p>
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Data (1 - 50)</label>
                    <input type="number" id="f-dummy-count" min="1" max="50" value="5"
                           class="inp w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all bg-gray-50 focus:bg-white" required>
                    <p class="text-[10px] text-gray-400 mt-2 italic">Data akan dibuat dengan kecepatan dan harga acak.</p>
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
                    <h3 class="text-base font-bold text-gray-900">Hapus Paket?</h3>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                        Paket <strong id="del-name" class="text-gray-800"></strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
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

{{-- ===== Toast Container ===== --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[60] flex flex-col gap-2 pointer-events-none"></div>

@endsection

@push('scripts')
<script>
// ─── Package data from server ───────────────────────────────────────────────
const __pkgs = @json($packages->map->toJsonData());

function getPkg(id) {
    return __pkgs.find(p => p.id === id) || null;
}

// ─── Category config ────────────────────────────────────────────────────────
const CAT = {
    home:      { label: 'Home',      badge: 'bg-blue-100 text-blue-700',    dot: 'bg-blue-500'   },
    bisnis:    { label: 'Bisnis',    badge: 'bg-purple-100 text-purple-700', dot: 'bg-purple-500' },
    dedicated: { label: 'Dedicated', badge: 'bg-amber-100 text-amber-700',  dot: 'bg-amber-500'  },
};

const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ─── State ──────────────────────────────────────────────────────────────────
let activeFilter    = 'all';
let deleteTargetId  = null;

// ─── Filter & Search ────────────────────────────────────────────────────────
function setFilter(f) {
    activeFilter = f;
    document.querySelectorAll('.filter-pill').forEach(b => {
        b.classList.remove('bg-gray-900', 'text-white');
        b.classList.add('text-gray-500', 'hover:bg-gray-100');
    });
    const active = document.getElementById('filter-' + f);
    active.classList.add('bg-gray-900', 'text-white');
    active.classList.remove('text-gray-500', 'hover:bg-gray-100');
    applyFilters();
}

function applyFilters() {
    const q       = document.getElementById('pkg-search').value.toLowerCase();
    const showIn  = document.getElementById('show-inactive').checked;
    let visible   = 0;

    document.querySelectorAll('[data-pkg-row]').forEach(row => {
        const ok = row.dataset.name.includes(q)
            && (activeFilter === 'all' || row.dataset.category === activeFilter)
            && (showIn || row.dataset.active === '1');
        row.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });

    document.getElementById('filter-empty').classList.toggle('hidden', visible > 0);
    document.getElementById('row-count').textContent = visible;
}

// ─── Build row HTML ─────────────────────────────────────────────────────────
function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtPrice(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function buildRow(p) {
    const cfg   = CAT[p.category] || CAT.home;
    const actC  = p.is_active
        ? 'bg-green-50 text-green-700 hover:bg-green-100'
        : 'bg-gray-100 text-gray-500 hover:bg-gray-200';
    const dotC  = p.is_active ? 'bg-green-500' : 'bg-gray-400';
    const label = p.is_active ? 'Aktif' : 'Nonaktif';
    const pData = esc(JSON.stringify(p));

    return `<tr data-pkg-row data-id="${p.id}"
        data-name="${esc(p.name.toLowerCase())}"
        data-category="${esc(p.category)}"
        data-active="${p.is_active ? '1' : '0'}"
        class="hover:bg-gray-50/50 transition-colors group border-b border-gray-50">
        <td class="py-3.5 pl-5 pr-4">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full ${cfg.dot} flex-shrink-0 mt-0.5"></span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">${esc(p.name)}</p>
                    <p class="text-xs text-gray-400 truncate max-w-[180px]">${p.description ? esc(p.description) : '—'}</p>
                </div>
            </div>
        </td>
        <td class="py-3.5 pr-4">
            <span class="inline-flex text-xs font-semibold ${cfg.badge} px-2.5 py-1 rounded-full whitespace-nowrap">${cfg.label}</span>
        </td>
        <td class="py-3.5 pr-4">
            <p class="text-sm font-semibold text-gray-800 whitespace-nowrap">↓ ${p.speed_download} / ↑ ${p.speed_upload} Mbps</p>
            ${p.contention ? `<p class="text-xs text-gray-400">Rasio ${esc(p.contention)}</p>` : ''}
        </td>
        <td class="py-3.5 pr-4">
            <p class="text-sm font-bold text-gray-900 whitespace-nowrap">${fmtPrice(p.price)}</p>
            <p class="text-xs text-gray-400">/bulan</p>
        </td>
        <td class="py-3.5 pr-4">
            <button onclick="toggleStatus(${p.id}, this)"
                    class="status-btn inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors ${actC}">
                <span class="w-1.5 h-1.5 rounded-full ${dotC}"></span>${label}
            </button>
        </td>
        <td class="py-3.5 pr-5">
            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick='openModal("edit",JSON.parse(this.dataset.p))'
                        data-p="${pData}"
                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-colors" title="Edit Paket">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick='confirmDelete(${p.id},${JSON.stringify(p.name)})'
                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-500 flex items-center justify-center transition-colors" title="Hapus Paket">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>`;
}

// ─── Empty state HTML ────────────────────────────────────────────────────────
function emptyRowHTML() {
    return `<tr id="empty-row"><td colspan="6" class="py-16 text-center">
        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
        </svg>
        <p class="text-sm font-medium text-gray-400">Belum ada paket</p>
        <p class="text-xs text-gray-300 mt-0.5">Klik "Tambah Paket" untuk mulai menambahkan</p>
    </td></tr>`;
}

// ─── Stats update ────────────────────────────────────────────────────────────
function updateStats() {
    const rows = [...document.querySelectorAll('[data-pkg-row]')];
    const total     = rows.length;
    const aktif     = rows.filter(r => r.dataset.active === '1').length;
    const nonaktif  = total - aktif;
    const home      = rows.filter(r => r.dataset.category === 'home').length;
    const bisnis    = rows.filter(r => r.dataset.category === 'bisnis').length;
    const dedicated = rows.filter(r => r.dataset.category === 'dedicated').length;
    const cats      = (home > 0 ? 1 : 0) + (bisnis > 0 ? 1 : 0) + (dedicated > 0 ? 1 : 0);

    document.getElementById('stat-total').textContent    = total;
    document.getElementById('stat-aktif').textContent    = aktif;
    document.getElementById('stat-nonaktif').textContent = nonaktif;
    document.getElementById('stat-kategori').textContent = cats;
    document.getElementById('cnt-all').textContent       = total;
    document.getElementById('cnt-home').textContent      = home;
    document.getElementById('cnt-bisnis').textContent    = bisnis;
    document.getElementById('cnt-dedicated').textContent = dedicated;
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
        const res  = await fetch('/packages/generate-dummy', {
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

            const tbody  = document.getElementById('pkg-tbody');
            const emptyR = document.getElementById('empty-row');
            if (emptyR) emptyR.remove();

            data.packages.forEach(p => {
                tbody.insertAdjacentHTML('afterbegin', buildRow(p));
                __pkgs.unshift(p);
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

// ─── Modal open / close ───────────────────────────────────────────────────────

function openModal(mode, pkg = null) {
    const modal = document.getElementById('pkg-modal');
    const card  = document.getElementById('modal-card');

    document.getElementById('pkg-form').reset();
    clearErrors();
    document.getElementById('pkg-id').value = '';

    if (mode === 'edit' && pkg) {
        document.getElementById('modal-title').textContent    = 'Edit Paket';
        document.getElementById('modal-subtitle').textContent = 'Ubah informasi paket internet';
        document.getElementById('modal-save-text').textContent = 'Perbarui';
        document.getElementById('pkg-id').value    = pkg.id;
        document.getElementById('f-name').value    = pkg.name;
        document.getElementById('f-category').value = pkg.category;
        document.getElementById('f-dl').value      = pkg.speed_download;
        document.getElementById('f-ul').value      = pkg.speed_upload;
        document.getElementById('f-price').value   = pkg.price;
        document.getElementById('f-contention').value = pkg.contention ?? '';
        document.getElementById('f-desc').value    = pkg.description ?? '';
        document.getElementById('f-active').value  = pkg.is_active ? '1' : '0';
        document.getElementById('f-sort').value    = pkg.sort_order ?? 0;
    } else {
        document.getElementById('modal-title').textContent    = 'Tambah Paket';
        document.getElementById('modal-subtitle').textContent = 'Isi detail paket internet baru';
        document.getElementById('modal-save-text').textContent = 'Simpan';
        document.getElementById('f-active').value = '1';
        document.getElementById('f-sort').value   = '0';
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }));
}

function closeModal() {
    const card  = document.getElementById('modal-card');
    const modal = document.getElementById('pkg-modal');
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

// ─── Form submit ─────────────────────────────────────────────────────────────
async function submitForm(e) {
    e.preventDefault();

    const id    = document.getElementById('pkg-id').value;
    const isNew = !id;
    const url   = isNew ? '/packages' : `/packages/${id}`;
    const btn   = document.getElementById('modal-save-btn');
    const txt   = document.getElementById('modal-save-text');

    clearErrors();
    btn.disabled  = true;
    txt.textContent = 'Menyimpan…';

    const fd = new FormData(document.getElementById('pkg-form'));
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

            const tbody  = document.getElementById('pkg-tbody');
            const emptyR = document.getElementById('empty-row');

            if (isNew) {
                if (emptyR) emptyR.remove();
                tbody.insertAdjacentHTML('afterbegin', buildRow(data.package));
                // Sync __pkgs array
                __pkgs.unshift(data.package);
            } else {
                const existing = document.querySelector(`[data-pkg-row][data-id="${id}"]`);
                if (existing) existing.outerHTML = buildRow(data.package);
                // Sync __pkgs array
                const idx = __pkgs.findIndex(p => p.id === data.package.id);
                if (idx !== -1) __pkgs[idx] = data.package;
            }

            updateStats();
            applyFilters();
        } else if (res.status === 422 && data.errors) {
            showErrors(data.errors);
            showToast('error', 'Periksa kembali isian form.');
        } else {
            showToast('error', data.message || 'Terjadi kesalahan. Silakan coba lagi.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
    } finally {
        btn.disabled    = false;
        txt.textContent = isNew ? 'Simpan' : 'Perbarui';
    }
}

// ─── Validation errors ───────────────────────────────────────────────────────
const ERR_MAP = {
    name:           ['err-name',           'f-name'],
    category:       ['err-category',       'f-category'],
    speed_download: ['err-speed_download', 'f-dl'],
    speed_upload:   ['err-speed_upload',   'f-ul'],
    price:          ['err-price',          'f-price'],
};

function showErrors(errors) {
    Object.entries(errors).forEach(([field, msgs]) => {
        const cfg = ERR_MAP[field];
        if (!cfg) return;
        const errEl = document.getElementById(cfg[0]);
        const inpEl = document.getElementById(cfg[1]);
        if (errEl) { errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
        if (inpEl) inpEl.classList.add('!border-red-300');
    });
}

function clearErrors() {
    document.querySelectorAll('.err').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
    document.querySelectorAll('.inp').forEach(el => el.classList.remove('!border-red-300'));
}

// ─── Toggle status ────────────────────────────────────────────────────────────
async function toggleStatus(id, btn) {
    btn.disabled = true;
    try {
        const res  = await fetch(`/packages/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();

        if (data.success) {
            const active = data.is_active;
            btn.className = `status-btn inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors ${
                active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
            }`;
            btn.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${active ? 'bg-green-500' : 'bg-gray-400'}"></span>${active ? 'Aktif' : 'Nonaktif'}`;

            const row = btn.closest('[data-pkg-row]');
            row.dataset.active = active ? '1' : '0';

            // Sync __pkgs
            const p = __pkgs.find(p => p.id === id);
            if (p) p.is_active = active;

            showToast('success', data.message);
            updateStats();
        } else {
            showToast('error', data.message || 'Gagal mengubah status.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    } finally {
        btn.disabled = false;
    }
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
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }));
}

function closeDelModal() {
    const card  = document.getElementById('del-card');
    const modal = document.getElementById('del-modal');
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        deleteTargetId = null;
    }, 200);
}

async function executeDelete() {
    if (!deleteTargetId) return;
    const id  = deleteTargetId;
    const btn = document.getElementById('del-confirm-btn');
    const txt = document.getElementById('del-btn-text');
    btn.disabled    = true;
    txt.textContent = 'Menghapus…';

    try {
        const res  = await fetch(`/packages/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();

        if (data.success) {
            closeDelModal();
            showToast('success', data.message);

            const row = document.querySelector(`[data-pkg-row][data-id="${id}"]`);
            if (row) {
                row.style.transition = 'opacity .25s, transform .25s';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(16px)';
                setTimeout(() => {
                    row.remove();
                    // Sync __pkgs
                    const idx = __pkgs.findIndex(p => p.id === id);
                    if (idx !== -1) __pkgs.splice(idx, 1);
                    // Show empty row if needed
                    if (!document.querySelector('[data-pkg-row]')) {
                        document.getElementById('pkg-tbody').innerHTML = emptyRowHTML();
                    }
                    updateStats();
                    applyFilters();
                }, 260);
            }
        } else {
            showToast('error', data.message || 'Gagal menghapus paket.');
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
    const colors    = { success: 'bg-gray-900 text-white', error: 'bg-red-600 text-white', warning: 'bg-amber-500 text-white' };
    const icoColors = { success: 'text-green-400',         error: 'text-red-200',          warning: 'text-amber-100' };

    const toast = document.createElement('div');
    toast.setAttribute('data-toast', '');
    toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl
        text-sm font-medium transition-all duration-300 translate-y-4 opacity-0
        min-w-[260px] max-w-sm ${colors[type] || colors.success}`;
    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 ${icoColors[type]}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            ${icons[type]}
        </svg>
        <span class="flex-1 leading-snug">${message}</span>
        <button onclick="this.closest('[data-toast]').remove()"
                class="ml-1 opacity-60 hover:opacity-100 transition-opacity flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

    container.appendChild(toast);

    requestAnimationFrame(() => requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    }));

    let timer = setTimeout(() => dismissToast(toast), 4000);
    toast.addEventListener('mouseenter', () => clearTimeout(timer));
    toast.addEventListener('mouseleave', () => { timer = setTimeout(() => dismissToast(toast), 1500); });
}

function dismissToast(t) {
    if (!t.parentElement) return;
    t.classList.add('translate-y-4', 'opacity-0');
    setTimeout(() => t.remove(), 300);
}

// ─── Close modals on Escape ───────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (!document.getElementById('pkg-modal').classList.contains('hidden')) closeModal();
    else if (!document.getElementById('del-modal').classList.contains('hidden'))  closeDelModal();
});
</script>
@endpush
