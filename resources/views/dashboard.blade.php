@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Selamat datang, Admin 👋</h1>
        <p class="text-sm text-gray-400 mt-0.5">Ringkasan operasional ISP — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="flex items-center gap-2 bg-white border border-gray-200 text-sm text-gray-700 font-medium px-3 py-2 rounded-xl hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Bulan Ini
        </button>
        <button class="flex items-center gap-1.5 bg-green-600 hover:bg-green-500 text-white text-sm font-medium px-3 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pelanggan
        </button>
    </div>
</div>

{{-- KPI Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
    $stats = [
        ['label'=>'Total Pelanggan','value'=>'248','sub'=>'+12 bulan ini','icon'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75','color'=>'text-blue-600','bg'=>'bg-blue-50','trend'=>'up','pct'=>'+5.1%'],
        ['label'=>'Pendapatan Bulan Ini','value'=>'Rp 74,4 Jt','sub'=>'dari target Rp 80 Jt','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'text-green-600','bg'=>'bg-green-50','trend'=>'up','pct'=>'+8.3%'],
        ['label'=>'Tagihan Jatuh Tempo','value'=>'36','sub'=>'perlu ditindaklanjuti','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'text-amber-600','bg'=>'bg-amber-50','trend'=>'down','pct'=>'-3 dari bulan lalu'],
        ['label'=>'Gangguan Aktif','value'=>'5','sub'=>'3 sedang ditangani','icon'=>'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z','color'=>'text-red-600','bg'=>'bg-red-50','trend'=>'up','pct'=>'+2 hari ini'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="stat-card bg-white rounded-2xl border border-gray-100 p-4 lg:p-5 transition-all duration-200 cursor-pointer">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $s['bg'] }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $s['color'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $s['trend']==='up' ? 'text-green-700 bg-green-50' : 'text-red-600 bg-red-50' }}">
                {{ $s['pct'] }}
            </span>
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-0.5">{{ $s['value'] }}</p>
        <p class="text-xs font-medium text-gray-500">{{ $s['label'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Middle Row: Revenue Chart + Paket Distribution --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-5">

    {{-- Revenue Chart --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-gray-900">Pendapatan 6 Bulan Terakhir</h2>
                <p class="text-xs text-gray-400 mt-0.5">Tagihan terkumpul vs target bulanan</p>
            </div>
            <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
                <button class="text-xs px-3 py-1 rounded-lg bg-white text-gray-800 font-semibold shadow-sm">Bulanan</button>
                <button class="text-xs px-3 py-1 rounded-lg text-gray-500 font-medium">Tahunan</button>
            </div>
        </div>
        {{-- Simple bar chart via SVG --}}
        <div class="overflow-x-auto">
        <svg viewBox="0 0 480 160" class="w-full" style="min-width:300px">
            @php
            $data = [
                ['m'=>'Nov','v'=>58,'t'=>80],
                ['m'=>'Des','v'=>67,'t'=>80],
                ['m'=>'Jan','v'=>71,'t'=>80],
                ['m'=>'Feb','v'=>69,'t'=>80],
                ['m'=>'Mar','v'=>76,'t'=>80],
                ['m'=>'Apr','v'=>74,'t'=>80],
            ];
            $bw=42; $gap=34; $base=138; $sx=24;
            @endphp
            {{-- Y grid --}}
            @foreach([0,25,50,75,100] as $g)
            @php $y = $base - ($g/100*120); @endphp
            <line x1="20" y1="{{ $y }}" x2="475" y2="{{ $y }}" stroke="#f3f4f6" stroke-width="1"/>
            <text x="14" y="{{ $y+3 }}" font-size="8" fill="#9ca3af" text-anchor="end">{{ $g }}%</text>
            @endforeach
            @foreach($data as $i=>$d)
            @php
              $x   = $sx + $i*($bw+$gap);
              $hv  = round(($d['v']/100)*120);
              $ht  = round(($d['t']/100)*120);
              $yv  = $base - $hv;
              $yt  = $base - $ht;
              $last= $i===count($data)-1;
            @endphp
            {{-- Target dashed --}}
            <rect x="{{ $x }}" y="{{ $yt }}" width="{{ $bw }}" height="{{ $ht }}" rx="6"
                  fill="{{ $last ? '#bbf7d0' : '#f0fdf4' }}" stroke="#86efac" stroke-width="1" stroke-dasharray="3,2"/>
            {{-- Actual --}}
            <rect x="{{ $x+4 }}" y="{{ $yv }}" width="{{ $bw-8 }}" height="{{ $hv }}" rx="5"
                  fill="{{ $last ? '#16a34a' : '#4ade80' }}"/>
            <text x="{{ $x + $bw/2 }}" y="{{ $base+12 }}" font-size="8.5" fill="#6b7280" text-anchor="middle">{{ $d['m'] }}</text>
            <text x="{{ $x + $bw/2 }}" y="{{ $yv - 4 }}" font-size="7.5" fill="#374151" text-anchor="middle" font-weight="600">{{ $d['v'] }}Jt</text>
            @endforeach
        </svg>
        </div>
        <div class="flex items-center gap-4 mt-2">
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-500"></span><span class="text-xs text-gray-500">Terkumpul</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span><span class="text-xs text-gray-500">Target</span></div>
        </div>
    </div>

    {{-- Paket Distribution --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-900 mb-1">Distribusi Paket</h2>
        <p class="text-xs text-gray-400 mb-4">Total 248 pelanggan aktif</p>
        @php
        $pakets = [
            ['name'=>'Home 10 Mbps','count'=>82,'pct'=>33,'color'=>'bg-blue-500'],
            ['name'=>'Home 20 Mbps','count'=>74,'pct'=>30,'color'=>'bg-green-500'],
            ['name'=>'Home 50 Mbps','count'=>51,'pct'=>21,'color'=>'bg-purple-500'],
            ['name'=>'Bisnis 100 Mbps','count'=>28,'pct'=>11,'color'=>'bg-amber-500'],
            ['name'=>'Dedicated','count'=>13,'pct'=>5,'color'=>'bg-red-400'],
        ];
        @endphp
        <div class="space-y-3">
            @foreach($pakets as $p)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-medium text-gray-700">{{ $p['name'] }}</span>
                    <span class="text-xs text-gray-400">{{ $p['count'] }} ({{ $p['pct'] }}%)</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="{{ $p['color'] }} h-full rounded-full transition-all duration-700" style="width:{{ $p['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Bottom Row: Gangguan + Tagihan Jatuh Tempo --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">

    {{-- Tiket Gangguan Aktif --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Tiket Gangguan Aktif</h2>
            <a href="#" class="text-xs text-green-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        <div class="space-y-2.5">
            @php
            $tickets = [
                ['id'=>'TKT-0041','name'=>'Budi Santoso','area'=>'Blok A – Gg. Mawar','issue'=>'Koneksi putus-putus','status'=>'Sedang Ditangani','sc'=>'text-blue-600 bg-blue-50','time'=>'2 jam lalu','prio'=>'Tinggi','pc'=>'text-red-600 bg-red-50'],
                ['id'=>'TKT-0040','name'=>'Siti Rahayu','area'=>'Jl. Merdeka No. 12','issue'=>'Kecepatan lambat','status'=>'Menunggu','sc'=>'text-amber-600 bg-amber-50','time'=>'5 jam lalu','prio'=>'Sedang','pc'=>'text-amber-600 bg-amber-50'],
                ['id'=>'TKT-0039','name'=>'Ahmad Fauzi','area'=>'Perum Griya Indah B3','issue'=>'ONT mati total','status'=>'Eskalasi','sc'=>'text-red-600 bg-red-50','time'=>'1 hari lalu','prio'=>'Kritis','pc'=>'text-red-700 bg-red-100'],
                ['id'=>'TKT-0038','name'=>'Dewi Lestari','area'=>'Jl. Sudirman 45','issue'=>'Tidak bisa browsing','status'=>'Sedang Ditangani','sc'=>'text-blue-600 bg-blue-50','time'=>'1 hari lalu','prio'=>'Rendah','pc'=>'text-gray-600 bg-gray-100'],
            ];
            @endphp
            @foreach($tickets as $t)
            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer group">
                <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-mono text-gray-400">{{ $t['id'] }}</span>
                        <span class="text-xs font-semibold {{ $t['pc'] }} px-1.5 py-0.5 rounded-full">{{ $t['prio'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-800 truncate mt-0.5">{{ $t['name'] }} — {{ $t['issue'] }}</p>
                    <p class="text-xs text-gray-400">{{ $t['area'] }} · {{ $t['time'] }}</p>
                </div>
                <span class="text-[11px] font-semibold {{ $t['sc'] }} px-2 py-0.5 rounded-full flex-shrink-0">{{ $t['status'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tagihan Jatuh Tempo --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Tagihan Jatuh Tempo</h2>
            <a href="#" class="text-xs text-green-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        <div class="space-y-2.5">
            @php
            $bills = [
                ['name'=>'CV Maju Bersama','paket'=>'Bisnis 100 Mbps','amount'=>'Rp 850.000','due'=>'Hari ini','dc'=>'text-red-600','days'=>0],
                ['name'=>'Toko Berkah Jaya','paket'=>'Home 20 Mbps','amount'=>'Rp 250.000','due'=>'Besok','dc'=>'text-amber-600','days'=>1],
                ['name'=>'Rudi Hermawan','paket'=>'Home 10 Mbps','amount'=>'Rp 180.000','due'=>'2 hari lagi','dc'=>'text-amber-500','days'=>2],
                ['name'=>'Klinik Sehat Sejahtera','paket'=>'Dedicated','amount'=>'Rp 2.500.000','due'=>'3 hari lagi','dc'=>'text-gray-500','days'=>3],
                ['name'=>'Warnet GameZone','paket'=>'Bisnis 100 Mbps','amount'=>'Rp 1.200.000','due'=>'5 hari lagi','dc'=>'text-gray-400','days'=>5],
            ];
            @endphp
            @foreach($bills as $b)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer group">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0 font-bold text-sm text-gray-600">
                    {{ strtoupper(substr($b['name'],0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $b['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $b['paket'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-bold text-gray-900">{{ $b['amount'] }}</p>
                    <p class="text-xs font-semibold {{ $b['dc'] }}">{{ $b['due'] }}</p>
                </div>
                <button class="opacity-0 group-hover:opacity-100 transition-all ml-1 text-xs bg-green-600 hover:bg-green-500 text-white px-2.5 py-1 rounded-lg font-medium">
                    Tagih
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Pelanggan Terbaru --}}
<div class="bg-white rounded-2xl border border-gray-100 p-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h2 class="font-semibold text-gray-900">Pelanggan Terbaru</h2>
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" placeholder="Cari pelanggan..." class="text-sm bg-transparent outline-none text-gray-700 placeholder-gray-400 w-36">
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full min-w-[600px]">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Pelanggan</th>
                <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Paket</th>
                <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">IP Address</th>
                <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Bergabung</th>
                <th class="text-left text-xs font-semibold text-gray-400 pb-3 pr-4">Status</th>
                <th class="text-left text-xs font-semibold text-gray-400 pb-3">Tagihan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @php
            $customers = [
                ['name'=>'Eko Prasetyo','email'=>'eko@gmail.com','paket'=>'Home 20 Mbps','ip'=>'192.168.1.101','join'=>'28 Apr 2026','status'=>'Aktif','sc'=>'text-green-700 bg-green-50','tagihan'=>'Lunas','tc'=>'text-green-700 bg-green-50'],
                ['name'=>'Maya Indrawati','email'=>'maya@yahoo.com','paket'=>'Home 50 Mbps','ip'=>'192.168.1.145','join'=>'25 Apr 2026','status'=>'Aktif','sc'=>'text-green-700 bg-green-50','tagihan'=>'Lunas','tc'=>'text-green-700 bg-green-50'],
                ['name'=>'PT Karya Maju','email'=>'info@karyamaju.id','paket'=>'Bisnis 100 Mbps','ip'=>'192.168.2.10','join'=>'20 Apr 2026','status'=>'Aktif','sc'=>'text-green-700 bg-green-50','tagihan'=>'Belum','tc'=>'text-amber-600 bg-amber-50'],
                ['name'=>'Hendri Kurniawan','email'=>'hendri@gmail.com','paket'=>'Home 10 Mbps','ip'=>'192.168.1.88','join'=>'15 Apr 2026','status'=>'Suspend','sc'=>'text-red-600 bg-red-50','tagihan'=>'Terlambat','tc'=>'text-red-600 bg-red-50'],
                ['name'=>'Warung Makan Bu Sari','email'=>'-','paket'=>'Home 10 Mbps','ip'=>'192.168.1.72','join'=>'10 Apr 2026','status'=>'Aktif','sc'=>'text-green-700 bg-green-50','tagihan'=>'Lunas','tc'=>'text-green-700 bg-green-50'],
            ];
            @endphp
            @foreach($customers as $c)
            <tr class="hover:bg-gray-50/60 transition-colors group">
                <td class="py-3.5 pr-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-green-400 to-green-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($c['name'],0,1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $c['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $c['email'] }}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3.5 pr-4 text-sm text-gray-600">{{ $c['paket'] }}</td>
                <td class="py-3.5 pr-4 text-sm font-mono text-gray-500">{{ $c['ip'] }}</td>
                <td class="py-3.5 pr-4 text-sm text-gray-500">{{ $c['join'] }}</td>
                <td class="py-3.5 pr-4">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $c['sc'] }}">{{ $c['status'] }}</span>
                </td>
                <td class="py-3.5">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $c['tc'] }}">{{ $c['tagihan'] }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

@endsection
