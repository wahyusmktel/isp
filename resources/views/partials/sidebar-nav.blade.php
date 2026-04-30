{{-- Nav --}}
<nav class="flex-1 px-3 py-2 space-y-0.5">

    <p class="sidebar-label text-[10px] font-semibold uppercase tracking-widest text-gray-600 px-3 pt-3 pb-1 transition-all duration-200">Main Menu</p>

    <a href="{{ route('dashboard') }}"
       class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Dashboard">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Dashboard</span>
    </a>

    <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.index', 'customers.show') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Pelanggan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Pelanggan</span>
        @php $custCount = \App\Models\Customer::count(); @endphp
        @if($custCount > 0)
        <span class="sidebar-badge ml-auto bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $custCount }}</span>
        @endif
    </a>

    <a href="{{ route('customers.activities') }}" class="sidebar-link {{ request()->routeIs('customers.activities') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Aktifitas Pelanggan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Aktifitas Pelanggan</span>
    </a>

    @if(auth()->user()->role === 'admin')
    <a href="{{ route('packages.index') }}" class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Paket Internet">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Paket Internet</span>
    </a>
    @endif

    <a href="{{ route('invoices.index') }}" class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Tagihan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Tagihan</span>
        @php $invCount = \App\Models\Invoice::whereIn('status', ['unpaid', 'overdue'])->count(); @endphp
        @if($invCount > 0)
        <span class="sidebar-badge ml-auto text-[10px] text-amber-400 bg-amber-400/10 px-1.5 py-0.5 rounded font-semibold">{{ $invCount }}</span>
        @endif
    </a>

    <p class="sidebar-label text-[10px] font-semibold uppercase tracking-widest text-gray-600 px-3 pt-4 pb-1">Jaringan</p>

    @if(auth()->user()->role === 'admin')
    <a href="{{ route('network.index') }}" class="sidebar-link {{ request()->routeIs('network.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Router & ODP">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <rect x="2" y="6" width="20" height="8" rx="2"/>
            <path stroke-linecap="round" d="M6 10h.01M10 10h.01M6 14v3M12 14v3M18 14v3"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Router & ODP</span>
    </a>

    <a href="{{ route('pppoe-mapping.index') }}" class="sidebar-link {{ request()->routeIs('pppoe-mapping.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="PPPoE Mapping">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">PPPoE Mapping</span>
    </a>
    @endif

    <a href="{{ route('monitoring.index') }}" class="sidebar-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Monitoring">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Monitoring</span>
        <span class="sidebar-badge ml-auto w-2 h-2 rounded-full bg-green-500 badge-pulse"></span>
    </a>

    <a href="{{ route('traffic.index') }}" class="sidebar-link {{ request()->routeIs('traffic.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Monitoring Trafik">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Monitoring Trafik</span>
    </a>

    @if(auth()->user()->role === 'admin')
    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Tiket Gangguan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Tiket Gangguan</span>
        <span class="sidebar-badge ml-auto text-[10px] text-red-400 bg-red-400/10 px-1.5 py-0.5 rounded font-semibold">5</span>
    </a>
    @endif

    <p class="sidebar-label text-[10px] font-semibold uppercase tracking-widest text-gray-600 px-3 pt-4 pb-1">Umum</p>

    <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Pegawai">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Pegawai</span>
        @php $empCount = \App\Models\Employee::where('status','aktif')->count(); @endphp
        @if($empCount > 0)
        <span class="sidebar-badge ml-auto bg-violet-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $empCount }}</span>
        @endif
    </a>

    <a href="{{ route('payroll.index') }}" class="sidebar-link {{ request()->routeIs('payroll.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Penggajian">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Penggajian</span>
        @php $pendingPayroll = \App\Models\Payroll::where('status','pending')->whereYear('period', now()->year)->whereMonth('period', now()->month)->count(); @endphp
        @if($pendingPayroll > 0)
        <span class="sidebar-badge ml-auto text-[10px] text-amber-400 bg-amber-400/10 px-1.5 py-0.5 rounded font-semibold">{{ $pendingPayroll }}</span>
        @endif
    </a>

    <a href="{{ route('financial.index') }}" class="sidebar-link {{ request()->routeIs('financial.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Manajemen Keuangan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Keuangan</span>
    </a>

    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Laporan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Laporan</span>
    </a>

    @if(auth()->user()->role === 'admin')
    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Kelola Pengguna">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Kelola Pengguna</span>
    </a>
    <a href="{{ route('settings') }}" class="sidebar-link {{ request()->routeIs('settings') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all" title="Pengaturan">
        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        <span class="sidebar-text whitespace-nowrap">Pengaturan</span>
    </a>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="mt-1">
        @csrf
        <button type="submit" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all text-red-400 hover:!text-red-300 w-full" title="Keluar">
            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="sidebar-text whitespace-nowrap">Keluar</span>
        </button>
    </form>
</nav>
