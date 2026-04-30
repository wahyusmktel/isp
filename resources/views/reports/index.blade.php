@extends('layouts.app')
@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Laporan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Ringkasan pendapatan, pelanggan, dan jaringan</p>
    </div>
    <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2 self-start sm:self-auto">
        <select name="month" class="inp text-sm py-2 px-3">
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endforeach
        </select>
        <select name="year" class="inp text-sm py-2 px-3">
            @foreach(range(now()->year - 2, now()->year) as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filter
        </button>
    </form>
</div>

{{-- ═══════════ PENDAPATAN ═══════════ --}}
<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Pendapatan — {{ $periodDate->translatedFormat('F Y') }}</p>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $cards = [
        ['label'=>'Total Lunas',      'value'=>'Rp '.number_format($totalRevenue,0,',','.'),  'bg'=>'bg-green-50',  'tc'=>'text-green-700', 'sub'=>$paidCount.' tagihan'],
        ['label'=>'Belum Dibayar',    'value'=>'Rp '.number_format($totalUnpaid,0,',','.'),   'bg'=>'bg-amber-50',  'tc'=>'text-amber-700', 'sub'=>$unpaidCount.' tagihan'],
        ['label'=>'Jatuh Tempo',      'value'=>'Rp '.number_format($totalOverdue,0,',','.'),  'bg'=>'bg-red-50',    'tc'=>'text-red-600',   'sub'=>$overdueCount.' tagihan'],
        ['label'=>'Total Tagihan',    'value'=>$invoiceCount,                                   'bg'=>'bg-blue-50',   'tc'=>'text-blue-700',  'sub'=>'bulan ini'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <p class="text-xs text-gray-500 mb-1">{{ $c['label'] }}</p>
        <p class="text-xl font-bold {{ $c['tc'] }}">{{ $c['value'] }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $c['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Revenue Chart --}}
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
    <p class="text-xs font-semibold text-gray-700 mb-4">Pendapatan 12 Bulan Terakhir</p>
    <div class="flex items-end gap-1.5 h-40" id="rev-chart">
        @php
        $maxRev = max(1, max(array_values($revenueChart)));
        @endphp
        @foreach($revenueChart as $period => $val)
        @php
        $h = round(($val / $maxRev) * 100);
        $isCurrentMonth = $period === now()->format('Y-m');
        $label = \Carbon\Carbon::parse($period.'-01')->format('M');
        @endphp
        <div class="flex-1 flex flex-col items-center gap-1 group relative">
            <div class="absolute -top-8 bg-gray-900 text-white text-[10px] px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                Rp {{ number_format($val, 0, ',', '.') }}
            </div>
            <div class="w-full rounded-t-lg transition-all duration-300 {{ $isCurrentMonth ? 'bg-green-500' : 'bg-green-200 hover:bg-green-400' }}"
                 style="height: {{ max($h, 4) }}%"></div>
            <span class="text-[9px] text-gray-400 {{ $isCurrentMonth ? 'font-bold text-green-700' : '' }}">{{ $label }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══════════ PELANGGAN ═══════════ --}}
<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Pelanggan</p>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
    @php
    $custCards = [
        ['label'=>'Total',     'value'=>$totalCustomers,     'tc'=>'text-gray-900'],
        ['label'=>'Aktif',     'value'=>$activeCustomers,    'tc'=>'text-green-600'],
        ['label'=>'Suspend',   'value'=>$suspendCustomers,   'tc'=>'text-amber-600'],
        ['label'=>'Terminate', 'value'=>$terminateCustomers, 'tc'=>'text-red-500'],
        ['label'=>'Baru (bulan ini)', 'value'=>$newCustomers, 'tc'=>'text-blue-600'],
    ];
    @endphp
    @foreach($custCards as $c)
    <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
        <p class="text-2xl font-bold {{ $c['tc'] }}">{{ $c['value'] }}</p>
        <p class="text-[10px] text-gray-500 mt-0.5">{{ $c['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Customer Growth Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-xs font-semibold text-gray-700 mb-4">Pelanggan Baru per Bulan</p>
        <div class="flex items-end gap-1.5 h-32">
            @php $maxCG = max(1, max(array_values($custGrowthChart))); @endphp
            @foreach($custGrowthChart as $period => $val)
            @php
            $h = round(($val / $maxCG) * 100);
            $label = \Carbon\Carbon::parse($period.'-01')->format('M');
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                <div class="absolute -top-7 bg-gray-900 text-white text-[10px] px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">{{ $val }}</div>
                <div class="w-full rounded-t-lg bg-blue-200 hover:bg-blue-400 transition-all" style="height: {{ max($h, 4) }}%"></div>
                <span class="text-[9px] text-gray-400">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Distribution per Package --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-xs font-semibold text-gray-700 mb-4">Distribusi per Paket</p>
        @if(count($custByPackage))
        <div class="space-y-3">
            @php $maxPkg = max(1, max(array_column($custByPackage, 'total'))); @endphp
            @foreach($custByPackage as $pkg)
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-700 font-medium">{{ $pkg['pkg_name'] }}</span>
                    <span class="text-gray-500">{{ $pkg['total'] }} pelanggan</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-400 rounded-full transition-all" style="width: {{ round(($pkg['total'] / $maxPkg) * 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-6">Belum ada data</p>
        @endif
    </div>
</div>

{{-- ═══════════ TAGIHAN DETAIL ═══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Payment Methods --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-xs font-semibold text-gray-700 mb-4">Metode Pembayaran — {{ $periodDate->translatedFormat('F Y') }}</p>
        @if(count($paymentMethods))
        <div class="space-y-3">
            @foreach($paymentMethods as $pm)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($pm['method']) }}</p>
                        <p class="text-[10px] text-gray-400">{{ $pm['total'] }} transaksi</p>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-700">Rp {{ number_format($pm['amount'], 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-6">Belum ada pembayaran bulan ini</p>
        @endif
    </div>

    {{-- Top Overdue --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-xs font-semibold text-gray-700 mb-4">Tagihan Jatuh Tempo Terbesar</p>
        @if(count($topOverdue))
        <div class="space-y-3">
            @foreach($topOverdue as $od)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-300 to-red-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-[10px] font-bold">{{ strtoupper(substr($od['customer_name'], 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $od['customer_name'] }}</p>
                        <p class="text-[10px] text-gray-400">Jatuh tempo: {{ $od['due_date'] }} · {{ $od['days_overdue'] }} hari</p>
                    </div>
                </div>
                <p class="text-sm font-bold text-red-600">Rp {{ number_format($od['amount'], 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-6">Tidak ada tagihan jatuh tempo 🎉</p>
        @endif
    </div>
</div>

{{-- ═══════════ JARINGAN ═══════════ --}}
<p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Jaringan</p>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $netCards = [
        ['label'=>'Total Router',   'value'=>$totalRouters,  'ic'=>'text-gray-500',    'bg'=>'bg-gray-100'],
        ['label'=>'Router Online',  'value'=>$onlineRouters, 'ic'=>'text-green-600',   'bg'=>'bg-green-50'],
        ['label'=>'PPPoE Aktif',    'value'=>$totalPppoe,    'ic'=>'text-blue-600',    'bg'=>'bg-blue-50'],
        ['label'=>'Customer Mapped','value'=>$mappedCust,    'ic'=>'text-indigo-600',  'bg'=>'bg-indigo-50'],
    ];
    @endphp
    @foreach($netCards as $nc)
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $nc['bg'] }} flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 {{ $nc['ic'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="2" y="6" width="20" height="8" rx="2"/><path stroke-linecap="round" d="M6 10h.01M10 10h.01M6 14v3M12 14v3M18 14v3"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $nc['value'] }}</p>
            <p class="text-[10px] text-gray-500">{{ $nc['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Invoice Status Donut (pure CSS) --}}
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
    <p class="text-xs font-semibold text-gray-700 mb-4">Status Tagihan — {{ $periodDate->translatedFormat('F Y') }}</p>
    @if($invoiceCount > 0)
    <div class="flex items-center gap-8 flex-wrap">
        <div class="relative w-32 h-32 flex-shrink-0">
            @php
            $paidPct    = round(($paidCount / $invoiceCount) * 100);
            $unpaidPct  = round(($unpaidCount / $invoiceCount) * 100);
            $overduePct = round(($overdueCount / $invoiceCount) * 100);
            $cancelPct  = 100 - $paidPct - $unpaidPct - $overduePct;
            $p1 = $paidPct;
            $p2 = $p1 + $unpaidPct;
            $p3 = $p2 + $overduePct;
            @endphp
            <div class="w-full h-full rounded-full" style="background: conic-gradient(
                #16a34a 0% {{ $p1 }}%,
                #f59e0b {{ $p1 }}% {{ $p2 }}%,
                #ef4444 {{ $p2 }}% {{ $p3 }}%,
                #9ca3af {{ $p3 }}% 100%
            )"></div>
            <div class="absolute inset-3 bg-white rounded-full flex items-center justify-center">
                <div class="text-center">
                    <p class="text-lg font-bold text-gray-900">{{ $invoiceCount }}</p>
                    <p class="text-[9px] text-gray-400">Total</p>
                </div>
            </div>
        </div>
        <div class="space-y-2">
            @foreach([
                ['color'=>'bg-green-500', 'label'=>'Lunas',        'count'=>$paidCount,    'pct'=>$paidPct],
                ['color'=>'bg-amber-400', 'label'=>'Belum Bayar',  'count'=>$unpaidCount,  'pct'=>$unpaidPct],
                ['color'=>'bg-red-500',   'label'=>'Jatuh Tempo',  'count'=>$overdueCount, 'pct'=>$overduePct],
                ['color'=>'bg-gray-400',  'label'=>'Dibatalkan',   'count'=>$invoiceCount-$paidCount-$unpaidCount-$overdueCount, 'pct'=>$cancelPct],
            ] as $leg)
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $leg['color'] }} flex-shrink-0"></span>
                <span class="text-xs text-gray-700 w-24">{{ $leg['label'] }}</span>
                <span class="text-xs font-bold text-gray-900">{{ $leg['count'] }}</span>
                <span class="text-[10px] text-gray-400">({{ $leg['pct'] }}%)</span>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <p class="text-sm text-gray-400 text-center py-6">Belum ada tagihan bulan ini</p>
    @endif
</div>

@endsection
