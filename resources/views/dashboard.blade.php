@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Selamat datang, {{ auth()->user()->name ?? 'Guest' }} 👋</h1>
        <p class="text-sm text-gray-400 mt-0.5">Ringkasan operasional ISP — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('customers.index') }}" class="flex items-center gap-1.5 bg-green-600 hover:bg-green-500 text-white text-sm font-medium px-3 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pelanggan
        </a>
    </div>
</div>

{{-- KPI Stats --}}
@php
$stats = [
    ['label'=>'Total Pelanggan','value'=>$totalCust,'sub'=>'+'.$newThisMonth.' bulan ini','icon'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8z','color'=>'text-blue-600','bg'=>'bg-blue-50','pct'=>($custGrowthPct >= 0 ? '+' : '').$custGrowthPct.'%','up'=>$custGrowthPct >= 0],
    ['label'=>'Pendapatan Bulan Ini','value'=>'Rp '.number_format($revenueThisMonth/1000000,1,',','.').' Jt','sub'=>$paidThisMonth.' tagihan lunas','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'text-green-600','bg'=>'bg-green-50','pct'=>($revGrowthPct >= 0 ? '+' : '').$revGrowthPct.'%','up'=>$revGrowthPct >= 0],
    ['label'=>'Tagihan Jatuh Tempo','value'=>$overdueCount,'sub'=>$unpaidCount.' belum dibayar','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'text-amber-600','bg'=>'bg-amber-50','pct'=>$overdueCount > $overdueLastMonth ? '+'.($overdueCount-$overdueLastMonth) : ($overdueCount < $overdueLastMonth ? '-'.($overdueLastMonth-$overdueCount) : '0'),'up'=>$overdueCount <= $overdueLastMonth],
    ['label'=>'Router Online','value'=>$onlineRouters.'/'.$totalRouters,'sub'=>$totalPppoe.' PPPoE aktif','icon'=>'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2','color'=>'text-indigo-600','bg'=>'bg-indigo-50','pct'=>$totalRouters > 0 ? round(($onlineRouters/$totalRouters)*100).'%' : '0%','up'=>$onlineRouters >= $totalRouters],
];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @foreach($stats as $s)
    <div class="bg-white rounded-2xl border border-gray-100 p-4 lg:p-5 hover:shadow-md transition-all">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $s['bg'] }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $s['color'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $s['up'] ? 'text-green-700 bg-green-50' : 'text-red-600 bg-red-50' }}">{{ $s['pct'] }}</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-0.5">{{ $s['value'] }}</p>
        <p class="text-xs font-medium text-gray-500">{{ $s['label'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Middle Row --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-5">
    {{-- Revenue Chart --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-900 mb-1">Pendapatan 6 Bulan Terakhir</h2>
        <p class="text-xs text-gray-400 mb-4">Total tagihan terkumpul per bulan</p>
        <div class="flex items-end gap-2 h-40">
            @foreach($revChart as $i => $r)
            @php $h = $revMax > 0 ? round(($r['value'] / $revMax) * 100) : 0; $last = $i === count($revChart)-1; @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                <div class="absolute -top-8 bg-gray-900 text-white text-[10px] px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">{{ $r['label'] }}</div>
                <div class="w-full rounded-t-lg transition-all duration-300 {{ $last ? 'bg-green-500' : 'bg-green-200 hover:bg-green-400' }}" style="height: {{ max($h, 4) }}%"></div>
                <span class="text-[9px] {{ $last ? 'font-bold text-green-700' : 'text-gray-400' }}">{{ $r['month'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Package Distribution --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-900 mb-1">Distribusi Paket</h2>
        <p class="text-xs text-gray-400 mb-4">{{ $activeCust }} pelanggan aktif</p>
        @if(count($pkgDist))
        @php $pkgMax = max(1, max(array_column($pkgDist, 'total'))); @endphp
        <div class="space-y-3">
            @foreach($pkgDist as $i => $p)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-medium text-gray-700">{{ $p['pkg_name'] }}</span>
                    <span class="text-xs text-gray-400">{{ $p['total'] }} ({{ $activeCust > 0 ? round(($p['total']/$activeCust)*100) : 0 }}%)</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="{{ $pkgColors[$i % count($pkgColors)] }} h-full rounded-full transition-all duration-700" style="width:{{ round(($p['total']/$pkgMax)*100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-8">Belum ada data paket</p>
        @endif
    </div>
</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
    {{-- Invoice Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Status Tagihan Bulan Ini</h2>
            <a href="{{ route('invoices.index') }}" class="text-xs text-green-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        @if($invTotal > 0)
        <div class="flex items-center gap-6">
            @php
            $paidP = round(($invPaid/$invTotal)*100); $unpaidP = round(($invUnpaid/$invTotal)*100);
            $overdP = round(($invOverdue/$invTotal)*100); $otherP = 100-$paidP-$unpaidP-$overdP;
            $c1=$paidP; $c2=$c1+$unpaidP; $c3=$c2+$overdP;
            @endphp
            <div class="relative w-28 h-28 flex-shrink-0">
                <div class="w-full h-full rounded-full" style="background:conic-gradient(#16a34a 0% {{$c1}}%,#f59e0b {{$c1}}% {{$c2}}%,#ef4444 {{$c2}}% {{$c3}}%,#9ca3af {{$c3}}% 100%)"></div>
                <div class="absolute inset-3 bg-white rounded-full flex items-center justify-center">
                    <div class="text-center"><p class="text-lg font-bold text-gray-900">{{ $invTotal }}</p><p class="text-[8px] text-gray-400">Total</p></div>
                </div>
            </div>
            <div class="space-y-2 flex-1">
                @foreach([['bg-green-500','Lunas',$invPaid,$paidP],['bg-amber-400','Belum Bayar',$invUnpaid,$unpaidP],['bg-red-500','Jatuh Tempo',$invOverdue,$overdP]] as $leg)
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $leg[0] }} flex-shrink-0"></span>
                    <span class="text-xs text-gray-600 w-20">{{ $leg[1] }}</span>
                    <span class="text-xs font-bold text-gray-900">{{ $leg[2] }}</span>
                    <span class="text-[10px] text-gray-400">({{ $leg[3] }}%)</span>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-8">Belum ada tagihan bulan ini</p>
        @endif
    </div>

    {{-- Upcoming Due --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Tagihan Segera Jatuh Tempo</h2>
            <a href="{{ route('invoices.index') }}" class="text-xs text-green-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        @if($upcomingDue->count())
        <div class="space-y-2.5">
            @foreach($upcomingDue as $b)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-9 h-9 rounded-xl {{ $b['days'] < 0 ? 'bg-red-50' : ($b['days'] <= 1 ? 'bg-amber-50' : 'bg-gray-100') }} flex items-center justify-center flex-shrink-0">
                    <span class="font-bold text-sm {{ $b['days'] < 0 ? 'text-red-600' : ($b['days'] <= 1 ? 'text-amber-600' : 'text-gray-600') }}">{{ strtoupper(substr($b['name'],0,1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $b['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $b['package'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($b['amount'],0,',','.') }}</p>
                    <p class="text-xs font-semibold {{ $b['days'] < 0 ? 'text-red-600' : ($b['days'] <= 1 ? 'text-amber-600' : 'text-gray-400') }}">{{ $b['due'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-sm text-gray-400">Tidak ada tagihan mendekati jatuh tempo 🎉</p>
        </div>
        @endif
    </div>
</div>

{{-- Network + Latest Customers --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
    {{-- Network Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Jaringan</h2>
            <a href="{{ route('monitoring.index') }}" class="text-xs text-green-600 font-medium hover:underline">Monitor →</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @php
            $netCards = [
                ['label'=>'Router','value'=>$totalRouters,'ic'=>'text-gray-600','bg'=>'bg-gray-100'],
                ['label'=>'Online','value'=>$onlineRouters,'ic'=>'text-green-600','bg'=>'bg-green-50'],
                ['label'=>'PPPoE Aktif','value'=>$totalPppoe,'ic'=>'text-blue-600','bg'=>'bg-blue-50'],
                ['label'=>'Mapped','value'=>$mappedCust,'ic'=>'text-indigo-600','bg'=>'bg-indigo-50'],
            ];
            @endphp
            @foreach($netCards as $nc)
            <div class="rounded-xl {{ $nc['bg'] }} p-3 text-center">
                <p class="text-xl font-bold {{ $nc['ic'] }}">{{ $nc['value'] }}</p>
                <p class="text-[10px] text-gray-500 mt-0.5">{{ $nc['label'] }}</p>
            </div>
            @endforeach
        </div>
        {{-- Quick status bar --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-[10px] text-gray-400 mb-1">
                <span>Uptime Rate</span>
                <span>{{ $totalRouters > 0 ? round(($onlineRouters/$totalRouters)*100) : 0 }}%</span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full transition-all" style="width:{{ $totalRouters > 0 ? round(($onlineRouters/$totalRouters)*100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    {{-- Latest Customers --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Pelanggan Terbaru</h2>
            <a href="{{ route('customers.index') }}" class="text-xs text-green-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        @if($latestCustomers->count())
        <div class="overflow-x-auto">
        <table class="w-full min-w-[500px]">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Pelanggan</th>
                    <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Paket</th>
                    <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Bergabung</th>
                    <th class="text-left text-xs font-semibold text-gray-400 pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                $grads = ['from-blue-400 to-blue-700','from-green-400 to-green-700','from-purple-400 to-purple-700','from-amber-400 to-amber-700','from-red-400 to-red-700'];
                $sCfg = ['aktif'=>['l'=>'Aktif','c'=>'text-green-700 bg-green-50'],'suspend'=>['l'=>'Suspend','c'=>'text-amber-600 bg-amber-50'],'terminate'=>['l'=>'Terminate','c'=>'text-red-600 bg-red-50']];
                @endphp
                @foreach($latestCustomers as $i => $c)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $grads[$i % count($grads)] }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ strtoupper(substr($c->name,0,1)) }}</div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $c->name }}</p>
                                <p class="text-xs text-gray-400">{{ $c->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 pr-4 text-sm text-gray-600">{{ $c->package?->name ?? '—' }}</td>
                    <td class="py-3 pr-4 text-sm text-gray-500">{{ $c->join_date?->format('d M Y') ?? '—' }}</td>
                    <td class="py-3">
                        @php $sc = $sCfg[$c->status] ?? $sCfg['aktif']; @endphp
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $sc['c'] }}">{{ $sc['l'] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-8">Belum ada pelanggan</p>
        @endif
    </div>
</div>

{{-- Quick Links --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    @php
    $links = [
        ['label'=>'Monitoring','desc'=>'Status jaringan','route'=>'monitoring.index','bg'=>'bg-green-50 hover:bg-green-100','ic'=>'text-green-600','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['label'=>'Trafik','desc'=>'Bandwidth realtime','route'=>'traffic.index','bg'=>'bg-blue-50 hover:bg-blue-100','ic'=>'text-blue-600','icon'=>'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
        ['label'=>'PPPoE Map','desc'=>'Mapping pelanggan','route'=>'pppoe-mapping.index','bg'=>'bg-indigo-50 hover:bg-indigo-100','ic'=>'text-indigo-600','icon'=>'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
        ['label'=>'Laporan','desc'=>'Analisis lengkap','route'=>'reports.index','bg'=>'bg-amber-50 hover:bg-amber-100','ic'=>'text-amber-600','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];
    @endphp
    @foreach($links as $lk)
    <a href="{{ route($lk['route']) }}" class="rounded-2xl border border-gray-100 p-4 {{ $lk['bg'] }} transition-all group">
        <svg class="w-6 h-6 {{ $lk['ic'] }} mb-2" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $lk['icon'] }}"/></svg>
        <p class="text-sm font-semibold text-gray-800">{{ $lk['label'] }}</p>
        <p class="text-[10px] text-gray-500">{{ $lk['desc'] }}</p>
    </a>
    @endforeach
</div>

@endsection
