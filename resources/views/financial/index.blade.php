@extends('layouts.app')
@section('title', 'Manajemen Keuangan')
@section('page-title', 'Manajemen Keuangan')

@php
$categoryLabels = [
    'operasional'  => 'Operasional',
    'pemeliharaan' => 'Pemeliharaan',
    'gaji'         => 'Gaji',
    'peralatan'    => 'Peralatan',
    'lainnya'      => 'Lainnya',
];
$categoryColors = [
    'operasional'  => 'bg-blue-50 text-blue-700',
    'pemeliharaan' => 'bg-orange-50 text-orange-700',
    'gaji'         => 'bg-purple-50 text-purple-700',
    'peralatan'    => 'bg-indigo-50 text-indigo-700',
    'lainnya'      => 'bg-gray-100 text-gray-600',
];
$months = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
];
@endphp

@section('content')

{{-- ===== Header ===== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Manajemen Keuangan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Pemasukan dari pembayaran & pencatatan pengeluaran</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">
        {{-- Filter Periode --}}
        <form method="GET" action="{{ route('financial.index') }}" class="flex items-center gap-2">
            <select name="month" onchange="this.form.submit()"
                    class="bg-white border border-gray-200 text-gray-700 text-sm font-semibold px-3 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()"
                    class="bg-white border border-gray-200 text-gray-700 text-sm font-semibold px-3 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>

        <button onclick="openExpenseModal()"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
            </svg>
            Tambah Pengeluaran
        </button>
    </div>
</div>

{{-- ===== Summary Cards ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-0.5">Total Pemasukan</p>
            <p class="text-xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $incomes->count() }} pembayaran lunas</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l7-7m-7 7l-7-7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-0.5">Total Pengeluaran</p>
            <p class="text-xl font-bold text-red-500">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $expenses->count() }} transaksi pengeluaran</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl {{ $netBalance >= 0 ? 'bg-blue-50' : 'bg-amber-50' }} flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 {{ $netBalance >= 0 ? 'text-blue-600' : 'text-amber-500' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-0.5">Saldo Bersih</p>
            <p class="text-xl font-bold {{ $netBalance >= 0 ? 'text-blue-600' : 'text-amber-500' }}">
                {{ $netBalance >= 0 ? '' : '-' }}Rp {{ number_format(abs($netBalance), 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $months[$month] }} {{ $year }}</p>
        </div>
    </div>
</div>

{{-- ===== Chart + Breakdown ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">

    {{-- Bar Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm font-semibold text-gray-700 mb-4">Tren 6 Bulan Terakhir</p>
        @php
            $maxChart = max(1, collect($chartData)->max(fn($d) => max($d['income'], $d['expense'])));
        @endphp
        <div class="flex items-end gap-3 h-40">
            @foreach($chartData as $cd)
            @php
                $inH  = round(($cd['income']  / $maxChart) * 100);
                $exH  = round(($cd['expense'] / $maxChart) * 100);
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full flex items-end gap-0.5 h-32">
                    <div class="flex-1 bg-green-400 rounded-t-sm transition-all" style="height:{{ $inH }}%"
                         title="Pemasukan: Rp {{ number_format($cd['income'],0,',','.') }}"></div>
                    <div class="flex-1 bg-red-300 rounded-t-sm transition-all" style="height:{{ $exH }}%"
                         title="Pengeluaran: Rp {{ number_format($cd['expense'],0,',','.') }}"></div>
                </div>
                <p class="text-[10px] text-gray-400 text-center leading-tight">{{ $cd['label'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-3">
            <span class="flex items-center gap-1.5 text-xs text-gray-500"><span class="w-3 h-3 rounded-sm bg-green-400 inline-block"></span>Pemasukan</span>
            <span class="flex items-center gap-1.5 text-xs text-gray-500"><span class="w-3 h-3 rounded-sm bg-red-300 inline-block"></span>Pengeluaran</span>
        </div>
    </div>

    {{-- Expense Breakdown by Category --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm font-semibold text-gray-700 mb-4">Pengeluaran per Kategori</p>
        @php $byCategory = $expenses->groupBy('category'); @endphp
        @if($byCategory->isEmpty())
            <p class="text-xs text-gray-400 text-center py-8">Belum ada pengeluaran bulan ini</p>
        @else
            <div class="space-y-3">
                @foreach($categoryLabels as $key => $label)
                @php $catTotal = $byCategory->get($key, collect())->sum('amount'); @endphp
                @if($catTotal > 0)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-600">{{ $label }}</span>
                        <span class="text-xs font-semibold text-gray-700">Rp {{ number_format($catTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-red-400 rounded-full" style="width:{{ $totalExpense > 0 ? round(($catTotal/$totalExpense)*100) : 0 }}%"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ===== Transaction List ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

    {{-- Tabs --}}
    <div class="px-5 pt-4 pb-0 border-b border-gray-100">
        <div class="flex gap-1">
            <button onclick="switchTab('all')" id="tab-all"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors text-green-600 border-b-2 border-green-500">
                Semua
            </button>
            <button onclick="switchTab('income')" id="tab-income"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors text-gray-500 border-b-2 border-transparent hover:text-gray-700">
                Pemasukan <span class="ml-1 text-xs text-gray-400">({{ $incomes->count() }})</span>
            </button>
            <button onclick="switchTab('expense')" id="tab-expense"
                    class="tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors text-gray-500 border-b-2 border-transparent hover:text-gray-700">
                Pengeluaran <span class="ml-1 text-xs text-gray-400">({{ $expenses->count() }})</span>
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Tanggal</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-3 py-3">Keterangan</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-3 py-3">Kategori</th>
                    <th class="text-right text-xs font-semibold text-gray-500 px-3 py-3">Jumlah</th>
                    <th class="text-center text-xs font-semibold text-gray-500 px-3 py-3">Tipe</th>
                    <th class="text-center text-xs font-semibold text-gray-500 px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="transaction-tbody">
                @php
                    // Merge incomes and expenses into one sorted list
                    $allTransactions = collect();

                    foreach ($incomes as $inv) {
                        $allTransactions->push([
                            'type'        => 'income',
                            'date'        => $inv->paid_at,
                            'date_fmt'    => $inv->paid_at?->format('d M Y') ?? '—',
                            'description' => 'Pembayaran: ' . ($inv->customer?->name ?? '—'),
                            'sub'         => $inv->invoice_number,
                            'category'    => $inv->payment_method ?: 'Pembayaran',
                            'amount'      => $inv->amount,
                            'id'          => $inv->id,
                            'data'        => null,
                        ]);
                    }

                    foreach ($expenses as $exp) {
                        $allTransactions->push([
                            'type'        => 'expense',
                            'date'        => $exp->expense_date,
                            'date_fmt'    => $exp->expense_date?->format('d M Y') ?? '—',
                            'description' => $exp->description,
                            'sub'         => $exp->notes,
                            'category'    => $exp->category_label,
                            'amount'      => $exp->amount,
                            'id'          => $exp->id,
                            'data'        => $exp->toJsonData(),
                        ]);
                    }

                    $allTransactions = $allTransactions->sortByDesc('date')->values();
                @endphp

                @forelse($allTransactions as $tx)
                <tr class="transaction-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors"
                    data-type="{{ $tx['type'] }}">
                    <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap text-xs">{{ $tx['date_fmt'] }}</td>
                    <td class="px-3 py-3.5">
                        <p class="font-medium text-gray-800">{{ $tx['description'] }}</p>
                        @if($tx['sub'])
                            <p class="text-xs text-gray-400 mt-0.5">{{ $tx['sub'] }}</p>
                        @endif
                    </td>
                    <td class="px-3 py-3.5">
                        @if($tx['type'] === 'expense')
                            @php $catKey = array_search($tx['category'], $categoryLabels) ?: 'lainnya'; @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $categoryColors[$catKey] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $tx['category'] }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">{{ $tx['category'] }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-right font-semibold whitespace-nowrap
                        {{ $tx['type'] === 'income' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $tx['type'] === 'income' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        @if($tx['type'] === 'income')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Pemasukan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Pengeluaran
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($tx['type'] === 'expense')
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick='editExpense(@json($tx["data"]))'
                                    class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="deleteExpense({{ $tx['id'] }}, '{{ addslashes($tx['description']) }}')"
                                    class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        @else
                        <a href="{{ route('invoices.index') }}" class="text-xs text-blue-400 hover:text-blue-600 transition-colors">
                            Lihat
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Tidak ada transaksi di periode ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== Expense Modal ===== --}}
<div id="expense-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeExpenseModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Tambah Pengeluaran</h3>
                <button onclick="closeExpenseModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="expense-form" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" id="expense-id" value="">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                    <input type="text" id="f-description" placeholder="Contoh: Bayar listrik kantor"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select id="f-category"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="operasional">Operasional</option>
                            <option value="pemeliharaan">Pemeliharaan</option>
                            <option value="gaji">Gaji</option>
                            <option value="peralatan">Peralatan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" id="f-expense-date"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="f-amount" min="1" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                    <textarea id="f-notes" rows="2" placeholder="Opsional..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
                </div>
            </form>

            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                <button onclick="closeExpenseModal()"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">
                    Batal
                </button>
                <button onclick="submitExpense()"
                        id="btn-save-expense"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== Delete Confirm Modal ===== --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Hapus Pengeluaran</h3>
            <p class="text-sm text-gray-500 mb-5">Hapus "<span id="delete-desc" class="font-medium text-gray-700"></span>"? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-2">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">Batal</button>
                <button onclick="confirmDelete()" class="flex-1 px-4 py-2 text-sm font-semibold bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors">Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.tab-btn { border-bottom-width: 2px; }
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let deleteTargetId = null;

// ─── Tab switching ────────────────────────────────────────────────────────────
function switchTab(type) {
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('text-green-600', 'border-green-500');
        b.classList.add('text-gray-500', 'border-transparent');
    });
    const active = document.getElementById('tab-' + type);
    active.classList.add('text-green-600', 'border-green-500');
    active.classList.remove('text-gray-500', 'border-transparent');

    document.querySelectorAll('.transaction-row').forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ─── Expense modal ────────────────────────────────────────────────────────────
function openExpenseModal(data = null) {
    document.getElementById('modal-title').textContent  = data ? 'Edit Pengeluaran' : 'Tambah Pengeluaran';
    document.getElementById('expense-id').value         = data ? data.id : '';
    document.getElementById('f-description').value      = data ? data.description : '';
    document.getElementById('f-category').value         = data ? data.category : 'operasional';
    document.getElementById('f-amount').value           = data ? data.amount : '';
    document.getElementById('f-expense-date').value     = data ? data.expense_date : '{{ now()->format("Y-m-d") }}';
    document.getElementById('f-notes').value            = data ? data.notes : '';
    document.getElementById('expense-modal').classList.remove('hidden');
}

function closeExpenseModal() {
    document.getElementById('expense-modal').classList.add('hidden');
}

function editExpense(data) {
    openExpenseModal(data);
}

async function submitExpense() {
    const id   = document.getElementById('expense-id').value;
    const btn  = document.getElementById('btn-save-expense');
    const body = {
        _token:       CSRF,
        description:  document.getElementById('f-description').value.trim(),
        category:     document.getElementById('f-category').value,
        amount:       document.getElementById('f-amount').value,
        expense_date: document.getElementById('f-expense-date').value,
        notes:        document.getElementById('f-notes').value.trim(),
    };

    if (!body.description || !body.amount || !body.expense_date) {
        showToast('Mohon isi semua field yang wajib diisi.', 'error');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const url    = id ? `/financial/expenses/${id}` : '/financial/expenses';
    const method = id ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            closeExpenseModal();
            setTimeout(() => location.reload(), 800);
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Terjadi kesalahan.');
            showToast(errs, 'error');
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan';
    }
}

// ─── Delete ───────────────────────────────────────────────────────────────────
function deleteExpense(id, desc) {
    deleteTargetId = id;
    document.getElementById('delete-desc').textContent = desc;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteTargetId = null;
}

async function confirmDelete() {
    if (!deleteTargetId) return;
    try {
        const res  = await fetch(`/financial/expenses/${deleteTargetId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            closeDeleteModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Gagal menghapus.', 'error');
        }
    } catch {
        showToast('Gagal menghubungi server.', 'error');
    }
}

// ─── Toast ────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `fixed bottom-5 right-5 z-[9999] px-5 py-3 rounded-xl text-sm font-semibold shadow-lg transition-all
        ${type === 'success' ? 'bg-green-600 text-white' : 'bg-red-500 text-white'}`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}
</script>
@endpush
