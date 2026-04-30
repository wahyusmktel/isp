<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ISP Manager</title>
    <meta name="description" content="@yield('description', 'ISP Management Dashboard')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f0f2f0] text-gray-900 antialiased">

{{-- Mobile Overlay --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300 opacity-0"
     onclick="closeSidebar()"></div>

{{-- Wrapper --}}
<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="flex flex-col bg-[#0a0a0a] text-gray-400 overflow-y-auto flex-shrink-0 z-40
                  fixed inset-y-0 left-0 w-64
                  transition-transform duration-300 ease-in-out
                  -translate-x-full
                  lg:relative lg:translate-x-0 lg:w-64 lg:min-w-64">

        {{-- Logo + Toggle (desktop) --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/5">
            <div class="w-9 h-9 rounded-xl bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span id="sidebar-brand" class="text-white font-semibold text-base tracking-tight transition-all duration-200">ISP Manager</span>
            {{-- Desktop close button --}}
            <button onclick="toggleSidebar()" id="sidebar-close-btn"
                    class="ml-auto w-7 h-7 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors hidden lg:flex">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="px-4 py-3" id="sidebar-search">
            <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/5">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Search..." class="bg-transparent text-sm text-gray-400 placeholder-gray-600 outline-none flex-1 w-full min-w-0">
                <span class="text-xs text-gray-600 bg-white/5 px-1.5 py-0.5 rounded font-mono flex-shrink-0">⌘K</span>
            </div>
        </div>

        @include('partials.sidebar-nav')

        {{-- System Status Banner --}}
        <div id="sidebar-banner" class="mx-3 mb-4 p-4 bg-gradient-to-br from-green-900/40 to-green-800/20 border border-green-700/30 rounded-2xl transition-all duration-200">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-green-400 badge-pulse flex-shrink-0"></span>
                <p class="text-white font-semibold text-sm">Sistem Online</p>
            </div>
            <p class="text-gray-400 text-xs mb-3 leading-relaxed">Semua layanan berjalan normal</p>
            <div class="flex items-center gap-2">
                <button class="flex-1 bg-green-600 hover:bg-green-500 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-colors">
                    Cek Status
                </button>
                <button class="text-gray-400 hover:text-white text-xs transition-colors px-2">Detail</button>
            </div>
        </div>

    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div id="main-content" class="flex-1 flex flex-col min-w-0 overflow-hidden transition-all duration-300">

        {{-- ===== TOP BAR ===== --}}
        <header class="flex-shrink-0 h-14 bg-white border-b border-gray-100 flex items-center px-4 lg:px-6 gap-3 z-20 sticky top-0">

            {{-- Hamburger / Toggle --}}
            <button id="sidebar-toggle-btn" onclick="toggleSidebar()"
                    class="w-9 h-9 rounded-xl hover:bg-gray-100 flex items-center justify-center transition-colors flex-shrink-0"
                    title="Toggle Menu">
                <svg id="icon-menu" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
                <svg id="icon-close" class="w-5 h-5 text-gray-600 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-sm flex-1 min-w-0">
                <span class="text-gray-400 hidden sm:inline">ISP Manager</span>
                <svg class="w-3.5 h-3.5 text-gray-300 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                <span class="font-medium text-gray-800 truncate">@yield('page-title', 'Dashboard')</span>
            </nav>

            {{-- Right Actions --}}
            <div class="flex items-center gap-1.5 flex-shrink-0">
                {{-- Notif --}}
                <button class="w-9 h-9 rounded-xl hover:bg-gray-100 relative flex items-center justify-center transition-colors" title="Notifikasi">
                    <svg class="w-4.5 h-4.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-red-500 badge-pulse"></span>
                </button>
                {{-- Email --}}
                <button class="w-9 h-9 rounded-xl hover:bg-gray-100 hidden sm:flex items-center justify-center transition-colors" title="Pesan">
                    <svg class="w-4.5 h-4.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                    </svg>
                </button>
                <div class="w-px h-5 bg-gray-200 mx-0.5"></div>
                {{-- Avatar Dropdown --}}
                <div class="relative" id="profile-dropdown-container">
                    <button onclick="document.getElementById('profile-dropdown').classList.toggle('hidden')" class="flex items-center gap-2 hover:bg-gray-50 pl-1 pr-2.5 py-1.5 rounded-xl transition-colors focus:outline-none">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-green-400 to-green-700 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden md:inline">{{ auth()->user()->name ?? 'Guest' }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden md:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-50 mb-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name ?? 'Guest' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-green-600 font-semibold mt-1">{{ auth()->user()->role ?? 'User' }}</p>
                        </div>
                        <a href="{{ route('profile.index') ?? '#' }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil & Password
                        </a>
                        <div class="h-px bg-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar Sistem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- ===== PAGE CONTENT ===== --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>

    </div>
</div>

{{-- ===== SIDEBAR TOGGLE SCRIPT ===== --}}
<script>
    const sidebar       = document.getElementById('sidebar');
    const overlay       = document.getElementById('sidebar-overlay');
    const iconMenu      = document.getElementById('icon-menu');
    const iconClose     = document.getElementById('icon-close');
    const brand         = document.getElementById('sidebar-brand');
    const searchBox     = document.getElementById('sidebar-search');
    const banner        = document.getElementById('sidebar-banner');
    const labels        = document.querySelectorAll('.sidebar-label');
    const texts         = document.querySelectorAll('.sidebar-text');
    const badges        = document.querySelectorAll('.sidebar-badge');

    const isDesktop = () => window.innerWidth >= 1024;

    // State: desktop=collapsed(icon-only)|expanded; mobile=hidden|shown
    let desktopCollapsed = false;
    let mobileSidebarOpen = false;

    // Handle profile dropdown click outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('profile-dropdown-container');
        const dropdown = document.getElementById('profile-dropdown');
        if (container && dropdown && !container.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    function toggleSidebar() {
        if (isDesktop()) {
            desktopCollapsed = !desktopCollapsed;
            applyDesktopState();
        } else {
            mobileSidebarOpen = !mobileSidebarOpen;
            applyMobileState();
        }
    }

    function closeSidebar() {
        mobileSidebarOpen = false;
        applyMobileState();
    }

    function applyDesktopState() {
        if (desktopCollapsed) {
            // Collapse to icon-only
            sidebar.classList.remove('lg:w-64', 'lg:min-w-64');
            sidebar.classList.add('lg:w-[68px]', 'lg:min-w-[68px]');
            brand.classList.add('opacity-0', 'w-0', 'overflow-hidden');
            searchBox.classList.add('hidden');
            banner.classList.add('hidden');
            labels.forEach(el => el.classList.add('hidden'));
            texts.forEach(el => el.classList.add('opacity-0', 'w-0', 'overflow-hidden', 'pointer-events-none'));
            badges.forEach(el => el.classList.add('hidden'));
            iconMenu.classList.add('hidden');
            iconClose.classList.remove('hidden');
        } else {
            // Expand
            sidebar.classList.add('lg:w-64', 'lg:min-w-64');
            sidebar.classList.remove('lg:w-[68px]', 'lg:min-w-[68px]');
            brand.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
            searchBox.classList.remove('hidden');
            banner.classList.remove('hidden');
            labels.forEach(el => el.classList.remove('hidden'));
            texts.forEach(el => el.classList.remove('opacity-0', 'w-0', 'overflow-hidden', 'pointer-events-none'));
            badges.forEach(el => el.classList.remove('hidden'));
            iconMenu.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }
    }

    function applyMobileState() {
        if (mobileSidebarOpen) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden', 'opacity-0');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
            iconMenu.classList.add('hidden');
            iconClose.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            iconMenu.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }
    }

    // On desktop: sidebar open by default
    function initSidebar() {
        if (isDesktop()) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('lg:relative', 'lg:translate-x-0');
            desktopCollapsed = false;
        }
    }

    window.addEventListener('resize', () => {
        if (isDesktop()) {
            overlay.classList.add('hidden', 'opacity-0');
            overlay.classList.remove('opacity-100');
            mobileSidebarOpen = false;
            if (!sidebar.classList.contains('-translate-x-full') === false) {
                sidebar.classList.remove('-translate-x-full');
            }
        }
    });

    initSidebar();
</script>

@stack('scripts')
</body>
</html>
