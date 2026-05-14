@extends('layouts.app')
@section('title', 'Tagihan')
@section('page-title', 'Tagihan')

@php
$statusCfg = [
    'paid'      => ['label' => 'Lunas',         'badge' => 'bg-green-50 text-green-700',  'dot' => 'bg-green-500'],
    'unpaid'    => ['label' => 'Belum Dibayar', 'badge' => 'bg-amber-50 text-amber-700',  'dot' => 'bg-amber-500'],
    'overdue'   => ['label' => 'Jatuh Tempo',   'badge' => 'bg-red-50 text-red-600',      'dot' => 'bg-red-500'],
    'cancelled' => ['label' => 'Dibatalkan',    'badge' => 'bg-gray-100 text-gray-600',   'dot' => 'bg-gray-400'],
];
@endphp

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Tagihan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola data tagihan dan pembayaran pelanggan</p>
    </div>
    <div class="flex items-center gap-2 self-start sm:self-auto relative z-10 flex-wrap">
        {{-- Filter Periode --}}
        <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center">
            <div class="relative">
                <input type="month" name="period" value="{{ $period }}" onchange="this.form.submit()" 
                       class="bg-white border border-gray-200 text-gray-700 text-sm font-semibold pl-3 pr-3 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 cursor-pointer transition-colors shadow-sm"
                       title="Filter Periode Tagihan">
            </div>
        </form>

        @if(auth()->user()->role === 'admin')
        <button type="button" onclick="generateMass('{{ $period }}')" id="btn-generate-mass"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors cursor-pointer">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Generate Otomatis
        </button>
        <button onclick="openModal('create')"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Buat Tagihan
        </button>
        @endif
    </div>
</div>

{{-- ===== Stats ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $statCards = [
        ['id'=>'stat-total',   'label'=>'Total Tagihan', 'value'=>$stats['total'],   'ico'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'bg'=>'bg-blue-50',  'ic'=>'text-blue-600'],
        ['id'=>'stat-paid',    'label'=>'Lunas',         'value'=>$stats['paid'],    'ico'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg'=>'bg-green-50', 'ic'=>'text-green-600'],
        ['id'=>'stat-unpaid',  'label'=>'Belum Dibayar', 'value'=>$stats['unpaid'],  'ico'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg'=>'bg-amber-50', 'ic'=>'text-amber-600'],
        ['id'=>'stat-overdue', 'label'=>'Jatuh Tempo',   'value'=>$stats['overdue'], 'ico'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg'=>'bg-red-50',   'ic'=>'text-red-500'],
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
        <input type="text" id="inv-search" placeholder="Cari no. tagihan, nama pelanggan..."
               class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1"
               oninput="applyFilters()">
    </div>

    {{-- Status pills --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 sm:pb-0 scrollbar-none flex-shrink-0">
        <button onclick="setFilter('all')" id="filter-all"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors bg-gray-900 text-white">
            Semua&nbsp;<span id="cnt-all">{{ $stats['total'] }}</span>
        </button>
        <button onclick="setFilter('unpaid')" id="filter-unpaid"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Belum Dibayar&nbsp;<span id="cnt-unpaid">{{ $stats['unpaid'] }}</span>
        </button>
        <button onclick="setFilter('paid')" id="filter-paid"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Lunas&nbsp;<span id="cnt-paid">{{ $stats['paid'] }}</span>
        </button>
        <button onclick="setFilter('overdue')" id="filter-overdue"
                class="filter-pill flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors text-gray-500 hover:bg-gray-100">
            Jatuh Tempo&nbsp;<span id="cnt-overdue">{{ $stats['overdue'] }}</span>
        </button>
    </div>
</div>

{{-- ===== Table ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[820px]">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/60">
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pl-5 pr-4">No. Tagihan</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Pelanggan</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Periode</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Nominal</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Status</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Metode Bayar</th>
                <th class="text-left text-xs font-semibold text-gray-400 py-3 pr-4">Jatuh Tempo</th>
                <th class="text-right text-xs font-semibold text-gray-400 py-3 pr-5">Aksi</th>
            </tr>
        </thead>
        <tbody id="inv-tbody" class="divide-y divide-gray-50">
            @forelse($invoices as $inv)
            @php
            $sCfg = $statusCfg[$inv->status] ?? $statusCfg['unpaid'];
            @endphp
            <tr data-inv-row
                data-id="{{ $inv->id }}"
                data-number="{{ strtolower($inv->invoice_number) }}"
                data-customer="{{ strtolower($inv->customer?->name ?? '') }}"
                data-customer-num="{{ strtolower($inv->customer?->customer_number ?? '') }}"
                data-status="{{ $inv->status }}"
                class="hover:bg-gray-50/50 transition-colors group">

                {{-- No Tagihan --}}
                <td class="py-3.5 pl-5 pr-4">
                    <p class="text-sm font-semibold text-gray-900 font-mono">{{ $inv->invoice_number }}</p>
                </td>

                {{-- Pelanggan --}}
                <td class="py-3.5 pr-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $inv->customer?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $inv->customer?->customer_number ?? '—' }}</p>
                    </div>
                </td>

                {{-- Periode --}}
                <td class="py-3.5 pr-4">
                    <p class="text-sm text-gray-700 whitespace-nowrap">{{ $inv->billing_period?->format('F Y') ?? '—' }}</p>
                </td>

                {{-- Nominal --}}
                <td class="py-3.5 pr-4">
                    <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($inv->amount, 0, ',', '.') }}</p>
                </td>

                {{-- Status dropdown --}}
                <td class="py-3.5 pr-4" data-status-cell>
                    <div class="relative" data-status-dd>
                        <button onclick="toggleStatusDd(this)" data-id="{{ $inv->id }}"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors cursor-pointer select-none
                                       {{ $sCfg['badge'] }} hover:opacity-80">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sCfg['dot'] }}"></span>
                            {{ $sCfg['label'] }}
                            <svg class="w-2.5 h-2.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="dd-menu hidden absolute top-full left-0 z-30 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden min-w-[150px] py-1">
                            <button onclick="setStatus({{ $inv->id }}, 'unpaid', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $inv->status==='unpaid' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>Belum Dibayar
                                @if($inv->status==='unpaid')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                            <button onclick="setStatus({{ $inv->id }}, 'paid', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $inv->status==='paid' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>Lunas
                                @if($inv->status==='paid')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                            <button onclick="setStatus({{ $inv->id }}, 'overdue', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $inv->status==='overdue' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>Jatuh Tempo
                                @if($inv->status==='overdue')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                            <button onclick="setStatus({{ $inv->id }}, 'cancelled', this)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors {{ $inv->status==='cancelled' ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 flex-shrink-0"></span>Dibatalkan
                                @if($inv->status==='cancelled')<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </button>
                        </div>
                    </div>
                </td>

                {{-- Metode Pembayaran --}}
                <td class="py-3.5 pr-4">
                    <select onchange="updatePaymentMethod({{ $inv->id }}, this.value, this)" class="bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg focus:ring-green-500 focus:border-green-500 block w-full px-2 py-1 outline-none transition-colors">
                        <option value="" disabled {{ !$inv->payment_method ? 'selected' : '' }}>Pilih Metode</option>
                        <option value="Tunai" {{ $inv->payment_method === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="Transfer Bank" {{ $inv->payment_method === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="QRIS" {{ $inv->payment_method === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        <option value="E-Wallet" {{ $inv->payment_method === 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </td>

                {{-- Jatuh Tempo --}}
                <td class="py-3.5 pr-4">
                    <p class="text-sm text-gray-600 whitespace-nowrap">{{ $inv->due_date?->format('d M Y') ?? '—' }}</p>
                </td>

                {{-- Aksi --}}
                <td class="py-3.5 pr-5">
                    <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('invoices.pdf', $inv->id) }}" target="_blank"
                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-green-50 hover:text-green-600 text-gray-500 flex items-center justify-center transition-colors"
                           title="Cetak PDF">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <button onclick="openModal('edit', getInv({{ $inv->id }}))"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-colors"
                                title="Edit Tagihan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="confirmDelete({{ $inv->id }}, {{ json_encode($inv->invoice_number) }})"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-500 flex items-center justify-center transition-colors"
                                title="Hapus Tagihan">
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
                <td colspan="8" class="py-16 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-400">Belum ada tagihan</p>
                        <p class="text-xs text-gray-300">Klik "Buat Tagihan" untuk memulai</p>
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
        <p class="text-sm font-medium text-gray-400">Tidak ada tagihan yang sesuai filter</p>
        <p class="text-xs text-gray-300 mt-0.5">Coba kata kunci atau filter lain</p>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-50 px-5 py-3">
        <p class="text-xs text-gray-400">
            Menampilkan <span id="row-count" class="font-semibold text-gray-600">{{ $invoices->count() }}</span> tagihan
        </p>
    </div>
</div>

{{-- ===== Create / Edit Modal ===== --}}
<div id="inv-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div id="modal-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4
                    transition-all duration-200 scale-95 opacity-0">

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100">
                <div>
                    <h2 id="modal-title" class="text-base font-bold text-gray-900">Buat Tagihan</h2>
                    <p id="modal-subtitle" class="text-xs text-gray-400 mt-0.5">Isi rincian tagihan pelanggan</p>
                </div>
                <button type="button" onclick="closeModal()"
                        class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="inv-form" onsubmit="submitForm(event)" novalidate>
                @csrf
                <input type="hidden" id="inv-id">

                <div class="px-6 py-5 overflow-y-auto max-h-[65vh] space-y-5">

                    {{-- ─── Informasi Utama ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Informasi Utama</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="lbl">Pelanggan <span class="text-red-500">*</span></label>
                                <select id="f-customer" name="customer_id" class="inp w-full" onchange="updateAmount(this)">
                                    <option value="" data-price="">Pilih pelanggan...</option>
                                    @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" data-price="{{ $cust->package?->price ?? 0 }}">{{ $cust->name }} ({{ $cust->phone }})</option>
                                    @endforeach
                                </select>
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-customer_id"></p>
                            </div>
                            <div>
                                <label class="lbl">Periode Tagihan <span class="text-red-500">*</span></label>
                                <input type="date" id="f-period" name="billing_period" class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-billing_period"></p>
                            </div>
                            <div>
                                <label class="lbl">No. Tagihan</label>
                                <input type="text" id="f-number" name="invoice_number" placeholder="Otomatis digenerate sistem"
                                       class="inp w-full font-mono text-sm bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-invoice_number"></p>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Pembayaran ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Pembayaran</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="lbl">Nominal (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" id="f-amount" name="amount" placeholder="Pilih pelanggan..."
                                       class="inp w-full bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-amount"></p>
                            </div>
                            <div>
                                <label class="lbl">Status <span class="text-red-500">*</span></label>
                                <select id="f-status" name="status" class="inp w-full">
                                    <option value="unpaid">Belum Dibayar</option>
                                    <option value="paid">Lunas</option>
                                    <option value="overdue">Jatuh Tempo</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label class="lbl">Jatuh Tempo <span class="text-red-500">*</span></label>
                                <input type="date" id="f-due" name="due_date" class="inp w-full">
                                <p class="err hidden text-xs text-red-500 mt-1" id="err-due_date"></p>
                            </div>
                            <div>
                                <label class="lbl">Tanggal Bayar</label>
                                <input type="datetime-local" id="f-paid_at" name="paid_at" class="inp w-full">
                            </div>
                            <div>
                                <label class="lbl">Metode Pembayaran</label>
                                <select id="f-method" name="payment_method" class="inp w-full">
                                    <option value="">Pilih metode...</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="E-Wallet">E-Wallet / QRIS</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Catatan ─── --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Catatan</p>
                        <textarea id="f-notes" name="notes" rows="2"
                                  placeholder="Keterangan tambahan (opsional)"
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
                    <h3 class="text-base font-bold text-gray-900">Hapus Tagihan?</h3>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                        Tagihan <strong id="del-name" class="text-gray-800"></strong> akan dihapus permanen.
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

{{-- ===== Mass Generate Confirm Modal ===== --}}
<div id="mass-modal" class="fixed inset-0 z-[60] hidden" role="dialog">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeMassModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="mass-card"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6
                    transition-all duration-200 scale-95 opacity-0">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Generate Tagihan Massal?</h3>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                        Sistem akan membuat tagihan otomatis untuk seluruh pelanggan <strong>Aktif</strong> pada periode <strong id="mass-period-label"></strong> yang belum memiliki tagihan. Lanjutkan?
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button onclick="closeMassModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button id="mass-confirm-btn" onclick="executeMass()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-colors disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="mass-btn-text">Ya, Generate Sekarang</span>
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
        outline:none; background:#f9fafb; transition:all .15s; }
.inp:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); background:#fff; }
textarea.inp { resize:none; }
</style>
@endpush

@push('scripts')
<script>
// ─── Server data ─────────────────────────────────────────────────────────────
const __invs = @json($invoices->map->toJsonData());
const CSRF   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ─── Config ──────────────────────────────────────────────────────────────────
const STATUS = {
    paid:      { label:'Lunas',         badge:'bg-green-50 text-green-700',  dot:'bg-green-500'  },
    unpaid:    { label:'Belum Dibayar', badge:'bg-amber-50 text-amber-700',  dot:'bg-amber-500'  },
    overdue:   { label:'Jatuh Tempo',   badge:'bg-red-50 text-red-600',      dot:'bg-red-500'    },
    cancelled: { label:'Dibatalkan',    badge:'bg-gray-100 text-gray-600',   dot:'bg-gray-400'   },
};

// ─── State ───────────────────────────────────────────────────────────────────
let activeFilter   = 'all';
let deleteTargetId = null;
let massGenPeriod  = '{{ $period }}';

// ─── Helpers ─────────────────────────────────────────────────────────────────
function getInv(id) { return __invs.find(i => i.id === id) || null; }
function esc(s)     { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function fmtNum(n)  { return new Intl.NumberFormat('id-ID').format(n); }

function updateAmount(select) {
    const opt = select.options[select.selectedIndex];
    const amount = opt ? opt.getAttribute('data-price') : '';
    document.getElementById('f-amount').value = amount;
}

// ─── Build Status Dropdown HTML ───────────────────────────────────────────────
function buildStatusCell(id, status) {
    const s = STATUS[status] || STATUS.unpaid;
    const opts = Object.entries(STATUS).map(([k,v]) => `
        <button onclick="setStatus(${id},'${k}',this)" type="button"
                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-left hover:bg-gray-50 transition-colors ${status===k?'font-semibold text-gray-900':'text-gray-600'}">
            <span class="w-1.5 h-1.5 rounded-full ${v.dot} flex-shrink-0"></span>${v.label}
            ${status===k ? '<svg class="w-3 h-3 ml-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' : ''}
        </button>`).join('');
    return `<div class="relative" data-status-dd>
        <button onclick="toggleStatusDd(this)" data-id="${id}" type="button"
                class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors cursor-pointer select-none ${s.badge} hover:opacity-80">
            <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>${s.label}
            <svg class="w-2.5 h-2.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div class="dd-menu hidden absolute top-full left-0 z-30 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden min-w-[150px] py-1">
            ${opts}
        </div>
    </div>`;
}

// ─── Build Table Row ──────────────────────────────────────────────────────────
function buildRow(i) {
    const iData = esc(JSON.stringify(i));
    return `<tr data-inv-row data-id="${i.id}"
        data-number="${esc(i.invoice_number.toLowerCase())}"
        data-customer="${esc(i.customer_name.toLowerCase())}"
        data-status="${esc(i.status)}"
        class="hover:bg-gray-50/50 transition-colors group border-b border-gray-50">
        <td class="py-3.5 pl-5 pr-4">
            <p class="text-sm font-semibold text-gray-900 font-mono">${esc(i.invoice_number)}</p>
        </td>
        <td class="py-3.5 pr-4">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">${esc(i.customer_name)}</p>
                <p class="text-xs text-gray-400 truncate max-w-[160px]">${esc(i.customer_phone)}</p>
            </div>
        </td>
        <td class="py-3.5 pr-4">
            <p class="text-sm text-gray-700 whitespace-nowrap">${esc(i.billing_period_fmt)}</p>
        </td>
        <td class="py-3.5 pr-4">
            <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">Rp ${fmtNum(i.amount)}</p>
        </td>
        <td class="py-3.5 pr-4" data-status-cell>${buildStatusCell(i.id, i.status)}</td>
        <td class="py-3.5 pr-4">
            <p class="text-sm text-gray-600 whitespace-nowrap">${esc(i.due_date_fmt)}</p>
        </td>
        <td class="py-3.5 pr-5">
            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="/invoices/${i.id}/pdf" target="_blank"
                   class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-green-50 hover:text-green-600 text-gray-500 flex items-center justify-center transition-colors" title="Cetak PDF">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </a>
                <button onclick='openModal("edit",JSON.parse(this.dataset.i))' data-i="${iData}" type="button"
                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-50 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-colors" title="Edit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick='confirmDelete(${i.id},${JSON.stringify(i.invoice_number)})' type="button"
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
    const q      = document.getElementById('inv-search').value.toLowerCase();
    let visible  = 0;

    document.querySelectorAll('[data-inv-row]').forEach(row => {
        const matchSearch  = !q || row.dataset.number.includes(q) || row.dataset.customer.includes(q) || (row.dataset.customerNum && row.dataset.customerNum.includes(q));
        const matchStatus  = activeFilter === 'all' || row.dataset.status === activeFilter;
        const show         = matchSearch && matchStatus;
        row.style.display  = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('filter-empty').classList.toggle('hidden', visible > 0);
    document.getElementById('row-count').textContent = visible;
}

// ─── Stats update ─────────────────────────────────────────────────────────────
function updateStats() {
    const rows = [...document.querySelectorAll('[data-inv-row]')];
    const total    = rows.length;
    const unpaid   = rows.filter(r => r.dataset.status === 'unpaid').length;
    const paid     = rows.filter(r => r.dataset.status === 'paid').length;
    const overdue  = rows.filter(r => r.dataset.status === 'overdue').length;
    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-unpaid').textContent    = unpaid;
    document.getElementById('stat-paid').textContent      = paid;
    document.getElementById('stat-overdue').textContent   = overdue;
    document.getElementById('cnt-all').textContent        = total;
    document.getElementById('cnt-unpaid').textContent     = unpaid;
    document.getElementById('cnt-paid').textContent       = paid;
    document.getElementById('cnt-overdue').textContent    = overdue;
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

// ─── Metode Pembayaran ───────────────────────────────────────────────────
async function updatePaymentMethod(id, method, selectEl) {
    selectEl.disabled = true;
    const originalValue = selectEl.getAttribute('data-original') || selectEl.value;

    try {
        const res = await fetch(`/invoices/${id}/payment-method`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ payment_method: method })
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message, 'success');
            selectEl.setAttribute('data-original', method);
        } else {
            throw new Error(data.message || 'Terjadi kesalahan.');
        }
    } catch (err) {
        showToast(err.message, 'error');
        selectEl.value = originalValue; // Revert
    } finally {
        selectEl.disabled = false;
    }
}

async function setStatus(id, status, btn) {
    btn.closest('.dd-menu').classList.add('hidden');
    try {
        const res  = await fetch(`/invoices/${id}/status`, {
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
            const row  = document.querySelector(`[data-inv-row][data-id="${id}"]`);
            row.dataset.status = status;
            row.querySelector('[data-status-cell]').innerHTML = buildStatusCell(id, status);
            const i = __invs.find(i => i.id === id);
            if (i) i.status = status;
            showToast('success', data.message);
            updateStats();
        } else {
            showToast('error', data.message || 'Gagal mengubah status.');
        }
    } catch {
        showToast('error', 'Koneksi bermasalah.');
    }
}

// ─── Modal open / close ───────────────────────────────────────────────────────
function openModal(mode, inv = null) {
    const modal = document.getElementById('inv-modal');
    const card  = document.getElementById('modal-card');

    document.getElementById('inv-form').reset();
    clearErrors();
    document.getElementById('inv-id').value = '';

    // Default dates
    const today = new Date();
    document.getElementById('f-period').value = today.toISOString().split('T')[0];
    
    // Set due date to +7 days default
    const nextWeek = new Date(today);
    nextWeek.setDate(nextWeek.getDate() + 7);
    document.getElementById('f-due').value = nextWeek.toISOString().split('T')[0];

    if (mode === 'edit' && inv) {
        document.getElementById('modal-title').textContent    = 'Edit Tagihan';
        document.getElementById('modal-subtitle').textContent = 'Ubah detail rincian tagihan';
        document.getElementById('modal-save-text').textContent = 'Perbarui';
        document.getElementById('inv-id').value       = inv.id;
        document.getElementById('f-customer').value   = inv.customer_id;
        document.getElementById('f-period').value     = inv.billing_period;
        document.getElementById('f-number').value     = inv.invoice_number;
        document.getElementById('f-amount').value     = inv.amount;
        document.getElementById('f-status').value     = inv.status;
        document.getElementById('f-due').value        = inv.due_date;
        document.getElementById('f-paid_at').value    = inv.paid_at;
        document.getElementById('f-method').value     = inv.payment_method;
        document.getElementById('f-notes').value      = inv.notes;
    } else {
        document.getElementById('modal-title').textContent    = 'Buat Tagihan';
        document.getElementById('modal-subtitle').textContent = 'Isi rincian tagihan pelanggan';
        document.getElementById('modal-save-text').textContent = 'Simpan';
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95','opacity-0');
        card.classList.add('scale-100','opacity-100');
    }));
}

function closeModal() {
    const card  = document.getElementById('modal-card');
    const modal = document.getElementById('inv-modal');
    card.classList.add('scale-95','opacity-0');
    card.classList.remove('scale-100','opacity-100');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 200);
}

// ─── Form submit ─────────────────────────────────────────────────────────────
async function submitForm(e) {
    e.preventDefault();
    const id    = document.getElementById('inv-id').value;
    const isNew = !id;
    const url   = isNew ? '/invoices' : `/invoices/${id}`;
    const btn   = document.getElementById('modal-save-btn');
    const txt   = document.getElementById('modal-save-text');

    clearErrors();
    btn.disabled    = true;
    txt.textContent = 'Menyimpan…';

    const fd = new FormData(document.getElementById('inv-form'));
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

            const tbody  = document.getElementById('inv-tbody');
            const emptyR = document.getElementById('empty-row');

            if (isNew) {
                if (emptyR) emptyR.remove();
                tbody.insertAdjacentHTML('afterbegin', buildRow(data.invoice));
                __invs.unshift(data.invoice);
            } else {
                const existing = document.querySelector(`[data-inv-row][data-id="${id}"]`);
                if (existing) existing.outerHTML = buildRow(data.invoice);
                const idx = __invs.findIndex(i => i.id === data.invoice.id);
                if (idx !== -1) __invs[idx] = data.invoice;
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
    customer_id: ['err-customer_id','f-customer'], billing_period: ['err-billing_period','f-period'],
    invoice_number: ['err-invoice_number','f-number'], amount: ['err-amount','f-amount'],
    due_date: ['err-due_date','f-due']
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
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95','opacity-0');
        card.classList.add('scale-100','opacity-100');
    }));
}

function closeDelModal() {
    const modal = document.getElementById('del-modal');
    const card  = document.getElementById('del-card');
    card.classList.add('scale-95','opacity-0');
    card.classList.remove('scale-100','opacity-100');
    setTimeout(() => { modal.classList.add('hidden'); deleteTargetId = null; }, 200);
}

async function executeDelete() {
    if (!deleteTargetId) return;
    const btn = document.getElementById('del-confirm-btn');
    const txt = document.getElementById('del-btn-text');
    
    btn.disabled = true;
    txt.textContent = 'Menghapus…';

    try {
        const res = await fetch(`/invoices/${deleteTargetId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        const data = await res.json();
        
        if (data.success) {
            document.querySelector(`[data-inv-row][data-id="${deleteTargetId}"]`)?.remove();
            const idx = __invs.findIndex(i => i.id === deleteTargetId);
            if (idx !== -1) __invs.splice(idx, 1);
            
            showToast('success', data.message);
            updateStats();
            applyFilters();
            
            if (__invs.length === 0) {
                document.getElementById('inv-tbody').innerHTML = `
                <tr id="empty-row">
                    <td colspan="7" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">Belum ada tagihan</p>
                        </div>
                    </td>
                </tr>`;
            }
        }
    } catch {
        showToast('error', 'Gagal menghapus tagihan.');
    } finally {
        btn.disabled = false;
        txt.textContent = 'Hapus';
        closeDelModal();
    }
}

// ─── Generate Massal ────────────────────────────────────────────────────────
function generateMass(period) {
    massGenPeriod = period || '{{ $period }}';

    // Format label periode (YYYY-MM → "Mei 2026")
    const [y, m] = massGenPeriod.split('-');
    const label = new Date(y, m - 1, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    document.getElementById('mass-period-label').textContent = label;

    const modal = document.getElementById('mass-modal');
    const card  = document.getElementById('mass-card');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => requestAnimationFrame(() => {
        card.classList.remove('scale-95','opacity-0');
        card.classList.add('scale-100','opacity-100');
    }));
}

function closeMassModal() {
    const modal = document.getElementById('mass-modal');
    const card  = document.getElementById('mass-card');
    card.classList.add('scale-95','opacity-0');
    card.classList.remove('scale-100','opacity-100');
    setTimeout(() => { modal.classList.add('hidden'); }, 200);
}

async function executeMass() {
    const btn = document.getElementById('mass-confirm-btn');
    const txt = document.getElementById('mass-btn-text');
    const originalBtn = document.getElementById('btn-generate-mass');
    
    btn.disabled = true;
    txt.textContent = 'Memproses...';
    
    if (originalBtn) {
        originalBtn.disabled = true;
        originalBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...`;
    }

    try {
        const res = await fetch('/invoices/generate-mass', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ period: massGenPeriod }),
        });
        
        const data = await res.json();
        
        if (data.success) {
            closeMassModal();
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Gagal memproses tagihan.');
            btn.disabled = false;
            txt.textContent = 'Ya, Generate Sekarang';
            if (originalBtn) {
                originalBtn.disabled = false;
                originalBtn.innerHTML = `<svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Generate Otomatis`;
            }
        }
    } catch (err) {
        showToast('error', 'Koneksi bermasalah.');
        btn.disabled = false;
        txt.textContent = 'Ya, Generate Sekarang';
        if (originalBtn) {
            originalBtn.disabled = false;
            originalBtn.innerHTML = `<svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Generate Otomatis`;
        }
    }
}

// ─── Toast Component ─────────────────────────────────────────────────────────
function showToast(type, msg) {
    const tc = document.getElementById('toast-container');
    const t = document.createElement('div');
    const isErr = type === 'error';
    
    t.className = `flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl transition-all duration-300 translate-y-4 opacity-0
                   ${isErr ? 'bg-red-600 text-white' : 'bg-gray-900 text-white'}`;
    
    t.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            ${isErr 
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'}
        </svg>
        <p class="text-sm font-medium leading-tight">${esc(msg)}</p>
    `;
    
    tc.appendChild(t);
    
    requestAnimationFrame(() => requestAnimationFrame(() => {
        t.classList.remove('translate-y-4','opacity-0');
    }));
    
    setTimeout(() => {
        t.classList.add('translate-y-4','opacity-0');
        setTimeout(() => t.remove(), 300);
    }, 3000);
}
</script>
@endpush
