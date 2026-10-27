<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Pengawas Dashboard' }} - {{ config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">

    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="GENTA Banten">
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
    <header class="topbar h-[72px] bg-[var(--color-primary)] text-white flex items-center justify-between px-[28px] gap-[18px]">
        <div class="brand flex items-center gap-3 font-extrabold tracking-[-.3px]">
            <div class="brandmark w-10 h-10 rounded-[13px] bg-[#FFE0E0] text-[var(--color-primary)] grid place-items-center font-extrabold text-2xl">G</div>
            <div>GENTA <small class="block font-medium text-[11px] opacity-75 tracking-[1.2px] mt-0.5">PENGAWASAN DAPUR MBG</small></div>
        </div>
        <div class="top-actions flex items-center gap-3">
            <span class="demo-pill hidden sm:inline-block text-[11px] font-extrabold tracking-[.6px] border border-white/30 rounded-full px-[10px] py-[7px]">MODE PRODUKSI</span>
            <div class="avatar w-9 h-9 rounded-full bg-[#FFE0E0] text-[var(--color-primary)] grid place-items-center font-extrabold text-sm" x-text="strtoupper((auth()->user()->name ?? 'P').charAt(0))"></div>
        </div>
    </header>

    <div class="shell grid grid-cols-[230px_minmax(0,1fr)] min-h-[calc(100vh-72px)]">
        <aside class="bg-[var(--color-background)] border-r border-[var(--color-border)] p-[22px_14px] overflow-y-auto lg:block hidden">
            <div class="side-label text-[10px] tracking-[1.4px] text-[var(--color-text-muted)] font-extrabold px-3 py-2 uppercase">MENU UTAMA</div>

            <a href="{{ route('pengawas.dashboard') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('pengawas.dashboard') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/5 hover:text-[var(--color-primary)]' }}"
               data-page="dashboard">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">▦</span> Dashboard
            </a>

            <a href="{{ route('pengawas.absensi.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('pengawas.absensi*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/5 hover:text-[var(--color-primary)]' }}"
               data-page="kitchens">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">⌂</span> Data Dapur
            </a>

            <a href="{{ route('pengawas.cctvs.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('pengawas.cctvs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/5 hover:text-[var(--color-primary)]' }}"
               data-page="cctv">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">▣</span> Monitoring CCTV
            </a>

            <a href="{{ route('pengawas.laporan.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('pengawas.laporan*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/5 hover:text-[var(--color-primary)]' }}"
               data-page="reports">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">▤</span> Laporan Pengawasan
            </a>

            <a href="{{ route('pengawas.users.index') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('pengawas.users*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/5 hover:text-[var(--color-primary)]' }}"
               data-page="users">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">♙</span> Manajemen Pengguna
            </a>

            <div class="side-label text-[10px] tracking-[1.4px] text-[var(--color-text-muted)] font-extrabold px-3 pt-6 pb-2 uppercase">SISTEM</div>
            <a href="{{ route('pengawas.settings') }}" 
               class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-[11px] text-sm text-[var(--color-text-muted)] transition-colors
                      {{ request()->routeIs('pengawas.settings') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/5 hover:text-[var(--color-primary)]' }}"
               data-page="settings">
                <span class="sidebar-icon w-[22px] text-center text-[16px]">⚙</span> Pengaturan
            </a>

            <div class="side-note mt-7 bg-[var(--color-surface)] rounded-[14px] p-4 text-[12px] leading-relaxed text-[var(--color-text-muted)]">
                <b>ⓘ Mode Produksi</b><br>Data dapur, laporan, dan CCTV terhubung ke sistem produksi.
            </div>
        </aside>

        <main class="p-7 lg:p-8 min-w-0">
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