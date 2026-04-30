@extends('layouts.app')
@section('title', 'Penggajian')
@section('page-title', 'Penggajian')

@php
$departemenColors = [
    'manajemen'    => 'bg-violet-50 text-violet-700',
    'teknis'       => 'bg-blue-50 text-blue-700',
    'noc'          => 'bg-cyan-50 text-cyan-700',
    'keuangan'     => 'bg-emerald-50 text-emerald-700',
    'cs'           => 'bg-orange-50 text-orange-700',
    'administrasi' => 'bg-gray-100 text-gray-600',
];
$avatarColors = [
    'bg-gradient-to-br from-violet-500 to-purple-600',
    'bg-gradient-to-br from-blue-500 to-cyan-500',
    'bg-gradient-to-br from-emerald-500 to-teal-500',
    'bg-gradient-to-br from-orange-500 to-amber-500',
    'bg-gradient-to-br from-rose-500 to-pink-500',
    'bg-gradient-to-br from-indigo-500 to-blue-600',
];
@endphp

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Penggajian</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola slip gaji & konfigurasi gaji per jabatan</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">
        <form method="GET" action="{{ route('payroll.index') }}">
            <input type="month" name="period" value="{{ $period }}" onchange="this.form.submit()"
                   class="bg-white border border-gray-200 text-gray-700 text-sm font-semibold px-3 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm cursor-pointer">
        </form>
        @if(auth()->user()->role === 'admin')
        <button onclick="generatePayroll()"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Generate Slip
        </button>
        @if($stats['pending'] > 0)
        <button onclick="payAll()"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Bayar Semua ({{ $stats['pending'] }})
        </button>
        @endif
        @endif
    </div>
</div>

{{-- ===== Stats ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500">Total Slip</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500">Belum Dibayar</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['paid'] }}</p>
            <p class="text-xs text-gray-500">Sudah Dibayar</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500">Total Gaji Bulan Ini</p>
        </div>
    </div>
</div>

{{-- ===== Tabs ===== --}}
<div class="flex gap-1 mb-4">
    <button onclick="switchTab('payroll')" id="tab-payroll"
            class="tab-btn px-5 py-2 text-sm font-semibold rounded-xl transition-colors bg-gray-900 text-white">
        Slip Gaji
    </button>
    <button onclick="switchTab('config')" id="tab-config"
            class="tab-btn px-5 py-2 text-sm font-semibold rounded-xl transition-colors bg-white border border-gray-200 text-gray-500 hover:text-gray-700">
        Konfigurasi Gaji
    </button>
</div>

{{-- ===== TAB: SLIP GAJI ===== --}}
<div id="pane-payroll">
    @if($payrolls->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 py-16 text-center">
        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-400 text-sm font-medium">Belum ada slip gaji untuk periode ini</p>
        <p class="text-gray-300 text-xs mt-1">Klik "Generate Slip" untuk membuat slip gaji otomatis</p>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Pegawai</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-3 py-3">Jabatan</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Gaji Pokok</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Tunjangan</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Potongan</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Gaji Bersih</th>
                        <th class="text-center text-xs font-semibold text-gray-500 px-3 py-3">Status</th>
                        <th class="text-center text-xs font-semibold text-gray-500 px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $p)
                    @php $color = $avatarColors[$p->employee_id % count($avatarColors)]; @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl {{ $color }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(mb_substr($p->employee?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $p->employee?->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $p->employee?->employee_number ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3.5">
                            <p class="text-xs font-medium text-gray-700">{{ $p->employee?->jabatan ?? '—' }}</p>
                            @php $dep = $p->employee?->departemen ?? 'administrasi'; @endphp
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium {{ $departemenColors[$dep] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $p->employee?->departemen_label ?? '' }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5 text-right text-sm text-gray-700 whitespace-nowrap">
                            Rp {{ number_format($p->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3.5 text-right text-sm text-green-600 whitespace-nowrap">
                            +Rp {{ number_format($p->allowance, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3.5 text-right text-sm text-red-500 whitespace-nowrap">
                            -Rp {{ number_format($p->deduction, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3.5 text-right font-bold text-gray-900 whitespace-nowrap">
                            Rp {{ number_format($p->net_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($p->status === 'paid')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Lunas
                                </span>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $p->paid_at?->format('d M Y') ?? '' }}</p>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($p->status === 'pending')
                                <button onclick='openEditModal(@json($p->toJsonData()))'
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="payOne({{ $p->id }}, '{{ addslashes($p->employee?->name ?? '') }}', {{ $p->net_salary }})"
                                        class="p-1.5 rounded-lg hover:bg-green-50 text-green-600 transition-colors" title="Bayar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </button>
                                @else
                                <a href="{{ route('financial.index', ['year' => $p->period?->year, 'month' => $p->period?->month]) }}"
                                   class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 transition-colors" title="Lihat di Keuangan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @endif
                                <button onclick="deletePayroll({{ $p->id }}, '{{ addslashes($p->employee?->name ?? '') }}')"
                                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 transition-colors" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50/70 border-t-2 border-gray-100">
                        <td colspan="2" class="px-5 py-3 text-xs font-semibold text-gray-600">Total</td>
                        <td class="px-3 py-3 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($payrolls->sum('base_salary'), 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-semibold text-green-600">+Rp {{ number_format($payrolls->sum('allowance'), 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-xs font-semibold text-red-500">-Rp {{ number_format($payrolls->sum('deduction'), 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-sm font-bold text-gray-900">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</td>
                        <td colspan="2" class="px-3 py-3 text-center text-xs text-gray-400">
                            <span class="text-green-600 font-semibold">{{ $stats['paid'] }} lunas</span> /
                            <span class="text-amber-500 font-semibold">{{ $stats['pending'] }} pending</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- ===== TAB: KONFIGURASI GAJI ===== --}}
<div id="pane-config" class="hidden">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Form --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm font-semibold text-gray-700 mb-4">Tambah / Edit Konfigurasi</p>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                    @php
                    $jabatanOpts = ['CEO','Direktur','Manajer','Supervisor','Admin','Keuangan','Customer Service','NOC Engineer','Teknisi','Lainnya'];
                    @endphp
                    <select id="cfg-jabatan"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatanOpts as $j)
                        <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Gaji Pokok (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="cfg-base" min="0" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tunjangan (Rp)</label>
                    <input type="number" id="cfg-allowance" min="0" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total</label>
                    <p class="text-lg font-bold text-green-600 px-1" id="cfg-total">Rp 0</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                    <input type="text" id="cfg-notes" placeholder="Opsional..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <button onclick="saveConfig()"
                        class="w-full bg-green-600 hover:bg-green-500 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                    Simpan Konfigurasi
                </button>
            </div>
        </div>

        {{-- Config Table --}}
        <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-700">Daftar Konfigurasi Gaji</p>
                <span class="text-xs text-gray-400">{{ $salaryConfigs->count() }} jabatan terdaftar</span>
            </div>
            @if($salaryConfigs->isEmpty())
            <div class="py-12 text-center">
                <p class="text-gray-400 text-sm">Belum ada konfigurasi gaji</p>
                <p class="text-gray-300 text-xs mt-1">Tambahkan konfigurasi di sebelah kiri</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Jabatan</th>
                            <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Gaji Pokok</th>
                            <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Tunjangan</th>
                            <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Total</th>
                            <th class="text-center text-xs font-semibold text-gray-500 px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaryConfigs as $cfg)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $cfg->jabatan }}</td>
                            <td class="px-3 py-3 text-right text-gray-600 whitespace-nowrap">Rp {{ number_format($cfg->base_salary, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-green-600 whitespace-nowrap">+Rp {{ number_format($cfg->allowance, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($cfg->base_salary + $cfg->allowance, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick='loadConfig(@json($cfg->toJsonData()))'
                                            class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteConfig({{ $cfg->id }}, '{{ addslashes($cfg->jabatan) }}')"
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 transition-colors" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== Edit Slip Modal ===== --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Edit Slip Gaji</h3>
                    <p class="text-xs text-gray-400" id="edit-emp-name"></p>
                </div>
                <button onclick="closeEditModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-3">
                <input type="hidden" id="edit-id">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Gaji Pokok</label>
                    <input type="number" id="edit-base" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tunjangan</label>
                    <input type="number" id="edit-allowance" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Potongan</label>
                    <input type="number" id="edit-deduction" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="bg-gray-50 rounded-xl px-4 py-3 flex justify-between items-center">
                    <span class="text-xs text-gray-500">Gaji Bersih</span>
                    <span class="font-bold text-gray-900" id="edit-net">Rp 0</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                    <input type="text" id="edit-notes" placeholder="Opsional..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            <div class="flex gap-2 px-6 py-4 border-t border-gray-100">
                <button onclick="closeEditModal()" class="flex-1 py-2 text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">Batal</button>
                <button onclick="saveEdit()" class="flex-1 py-2 text-sm font-semibold bg-green-600 hover:bg-green-500 text-white rounded-xl transition-colors">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== Pay Confirm Modal ===== --}}
<div id="pay-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePayModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Konfirmasi Pembayaran</h3>
            <p class="text-sm text-gray-500 mb-1">Bayar gaji <span id="pay-name" class="font-medium text-gray-700"></span></p>
            <p class="text-xl font-bold text-green-600 mb-4" id="pay-amount"></p>
            <p class="text-xs text-gray-400 mb-5">Pembayaran akan otomatis tercatat di halaman Keuangan sebagai pengeluaran gaji.</p>
            <div class="flex gap-2">
                <button onclick="closePayModal()" class="flex-1 py-2 text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">Batal</button>
                <button onclick="confirmPay()" class="flex-1 py-2 text-sm font-semibold bg-green-600 hover:bg-green-500 text-white rounded-xl transition-colors">Bayar</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== Delete Confirm Modal ===== --}}
<div id="del-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDelModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Hapus Slip Gaji</h3>
            <p class="text-sm text-gray-500 mb-5">Hapus slip gaji "<span id="del-name" class="font-medium text-gray-700"></span>"? Catatan di keuangan juga akan dihapus.</p>
            <div class="flex gap-2">
                <button onclick="closeDelModal()" class="flex-1 py-2 text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">Batal</button>
                <button onclick="confirmDel()" class="flex-1 py-2 text-sm font-semibold bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors">Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const PERIOD  = '{{ $period }}';
let payTarget = null;
let delTarget = null;

// ─── Tabs ─────────────────────────────────────────────────────────────────────
function switchTab(tab) {
    ['payroll','config'].forEach(t => {
        const pane = document.getElementById('pane-' + t);
        const btn  = document.getElementById('tab-'  + t);
        if (t === tab) {
            pane.classList.remove('hidden');
            btn.className = 'tab-btn px-5 py-2 text-sm font-semibold rounded-xl transition-colors bg-gray-900 text-white';
        } else {
            pane.classList.add('hidden');
            btn.className = 'tab-btn px-5 py-2 text-sm font-semibold rounded-xl transition-colors bg-white border border-gray-200 text-gray-500 hover:text-gray-700';
        }
    });
}

// ─── Generate ─────────────────────────────────────────────────────────────────
async function generatePayroll() {
    try {
        const res  = await fetch('/payroll/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ period: PERIOD }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 900);
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}

// ─── Pay All ──────────────────────────────────────────────────────────────────
async function payAll() {
    if (!confirm('Bayar semua gaji pending sekaligus? Semua akan otomatis dicatat di keuangan.')) return;
    try {
        const res  = await fetch('/payroll/pay-all', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ period: PERIOD }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 900);
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}

// ─── Pay One ──────────────────────────────────────────────────────────────────
function payOne(id, name, amount) {
    payTarget = id;
    document.getElementById('pay-name').textContent   = name;
    document.getElementById('pay-amount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
    document.getElementById('pay-modal').classList.remove('hidden');
}
function closePayModal() {
    document.getElementById('pay-modal').classList.add('hidden');
    payTarget = null;
}
async function confirmPay() {
    if (!payTarget) return;
    try {
        const res  = await fetch(`/payroll/${payTarget}/pay`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) { closePayModal(); setTimeout(() => location.reload(), 900); }
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}

// ─── Edit slip ────────────────────────────────────────────────────────────────
function openEditModal(data) {
    document.getElementById('edit-id').value         = data.id;
    document.getElementById('edit-emp-name').textContent = data.name + ' — ' + data.jabatan;
    document.getElementById('edit-base').value       = data.base_salary;
    document.getElementById('edit-allowance').value  = data.allowance;
    document.getElementById('edit-deduction').value  = data.deduction;
    document.getElementById('edit-notes').value      = data.notes;
    updateEditNet();
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('edit-modal').classList.add('hidden'); }
function updateEditNet() {
    const b = parseInt(document.getElementById('edit-base').value)      || 0;
    const a = parseInt(document.getElementById('edit-allowance').value) || 0;
    const d = parseInt(document.getElementById('edit-deduction').value) || 0;
    document.getElementById('edit-net').textContent = 'Rp ' + (b + a - d).toLocaleString('id-ID');
}
['edit-base','edit-allowance','edit-deduction'].forEach(id =>
    document.getElementById(id)?.addEventListener('input', updateEditNet)
);
async function saveEdit() {
    const id = document.getElementById('edit-id').value;
    try {
        const res  = await fetch(`/payroll/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                base_salary: document.getElementById('edit-base').value,
                allowance:   document.getElementById('edit-allowance').value,
                deduction:   document.getElementById('edit-deduction').value,
                notes:       document.getElementById('edit-notes').value,
            }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) { closeEditModal(); setTimeout(() => location.reload(), 900); }
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}

// ─── Delete payroll ───────────────────────────────────────────────────────────
function deletePayroll(id, name) {
    delTarget = id;
    document.getElementById('del-name').textContent = name;
    document.getElementById('del-modal').classList.remove('hidden');
}
function closeDelModal() { document.getElementById('del-modal').classList.add('hidden'); delTarget = null; }
async function confirmDel() {
    if (!delTarget) return;
    try {
        const res  = await fetch(`/payroll/${delTarget}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) { closeDelModal(); setTimeout(() => location.reload(), 900); }
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}

// ─── Salary Config ─────────────────────────────────────────────────────────
function loadConfig(data) {
    switchTab('config');
    document.getElementById('cfg-jabatan').value   = data.jabatan;
    document.getElementById('cfg-base').value      = data.base_salary;
    document.getElementById('cfg-allowance').value = data.allowance;
    document.getElementById('cfg-notes').value     = data.notes;
    updateCfgTotal();
}
function updateCfgTotal() {
    const b = parseInt(document.getElementById('cfg-base').value)      || 0;
    const a = parseInt(document.getElementById('cfg-allowance').value) || 0;
    document.getElementById('cfg-total').textContent = 'Rp ' + (b + a).toLocaleString('id-ID');
}
['cfg-base','cfg-allowance'].forEach(id =>
    document.getElementById(id)?.addEventListener('input', updateCfgTotal)
);
async function saveConfig() {
    const body = {
        jabatan:     document.getElementById('cfg-jabatan').value.trim(),
        base_salary: document.getElementById('cfg-base').value,
        allowance:   document.getElementById('cfg-allowance').value,
        notes:       document.getElementById('cfg-notes').value.trim(),
    };
    if (!body.jabatan || !body.base_salary) { showToast('Jabatan dan gaji pokok wajib diisi.', 'error'); return; }
    try {
        const res  = await fetch('/payroll/salary-config', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 900);
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}
async function deleteConfig(id, jabatan) {
    if (!confirm(`Hapus konfigurasi gaji jabatan "${jabatan}"?`)) return;
    try {
        const res  = await fetch(`/payroll/salary-config/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 900);
    } catch { showToast('Gagal menghubungi server.', 'error'); }
}

// ─── Toast ───────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `fixed bottom-5 right-5 z-[9999] px-5 py-3 rounded-xl text-sm font-semibold shadow-lg
        ${type === 'success' ? 'bg-green-600 text-white' : 'bg-red-500 text-white'}`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}
</script>
@endpush
