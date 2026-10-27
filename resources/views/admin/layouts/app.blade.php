<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin Panel' }} - {{ settings('app_name_desktop') ?? config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">

    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ settings('app_name_desktop') ?? 'GENTA Banten Admin' }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{
    sidebarOpen: false,
    toast: { show: false, message: '' },
    init() {
        window.showToast = (msg) => {
            this.toast.message = msg;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 2800);
        };
    }
}" class="app">
    <header class="topbar">
        <div class="brand">
            <button 
                class="hamburger lg:hidden" 
                @click="sidebarOpen = !sidebarOpen" 
                :aria-expanded="sidebarOpen"
                aria-controls="sidebar"
                aria-label="Toggle menu">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        :d="sidebarOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                </svg>
            </button>
            <div class="brandmark">
                <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_desktop') ?? 'GENTA Pangan Banten' }}">
            </div>
            <div>{{ strtoupper(settings('app_name_desktop') ?? 'GENTA') }} <small>ADMIN PANEL</small></div>
        </div>
        <div class="top-actions">
            <span class="demo-pill hidden sm:inline-block">{{ auth()->user()->name }}</span>
            <div class="avatar" x-text="{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}"></div>
        </div>
    </header>

    <div class="shell">
        <aside 
            id="sidebar" 
            :class="{ 'open': sidebarOpen }" 
            class="fixed top-[72px] left-0 bottom-0 z-999 w-[230px] flex-shrink-0 bg-[var(--color-background)] border-r border-[var(--color-border)] p-[22px_14px] overflow-y-auto transition-slide"
            x-show="sidebarOpen || true"
            @click.outside="sidebarOpen = false">
            
            <div class="side-label">MENU UTAMA</div>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">▦</span> Dashboard
            </a>

            <a href="{{ route('admin.dapurs.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('admin.dapurs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">⌂</span> Data Dapur
            </a>

            <a href="{{ route('admin.cctvs.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('admin.cctvs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">▣</span> Monitoring CCTV
            </a>

            <a href="{{ route('admin.laporan.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('admin.laporan*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">▤</span> Laporan Pengawasan
            </a>

            <a href="{{ route('admin.users') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('admin.users*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">♙</span> Manajemen Pengguna
            </a>

            <div class="side-label mt-5">SISTEM</div>

            <a href="{{ route('admin.settings') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('admin.settings') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">⚙</span> Pengaturan
            </a>

            <div class="side-note mt-7 bg-[var(--color-surface)] rounded-[14px] p-[14px] text-[12px] leading-relaxed text-[var(--color-text-muted)]">
                <b>ⓘ Mode Produksi</b><br>Data dapur, laporan, dan CCTV terhubung ke sistem produksi.
            </div>
        </aside>

        <main class="main-content flex-1 min-w-0 pt-[96px] px-[28px] pb-[28px] lg:pt-[96px] lg:ml-[230px] lg:pl-[14px] lg:pr-[28px] lg:pb-[28px]" id="view">
            @yield('content')
        </main>
    </div>

    {{-- Toast Notification --}}
    <div class="toast fixed bottom-6 right-6 bg-[var(--color-secondary)] text-white px-5 py-3 rounded-lg shadow-lg z-[9999] transition-opacity duration-300"
         x-show="toast.show"
         x-transition:enter="opacity-0 transform translate-y-2"
         x-transition:leave="opacity-0 transform translate-y-2"
         x-cloak>
        <span x-text="toast.message"></span>
    </div>
</body>
@stack('scripts')
</html>