@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Admin Panel', 'bodyClass' => 'app'])

@section('header')
<div x-data="{ sidebarOpen: false }">
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
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2" aria-expanded="open" aria-label="Profile menu">
                <span class="demo-pill hidden sm:inline-block">{{ auth()->user()->name }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:leave="transition ease-in duration-75" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1">
                <a href="{{ route('admin.settings.profile') }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
@endsection

@section('content')
<div x-data="{ sidebarOpen: false }">
<div class="shell overflow-y-hidden items-start">
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
                   {{ request()->routeIs(['admin.cctvs.index', 'admin.cctv-anomalies.*']) ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-bold' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}">
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

    <main class="main-content flex-1 min-w-0 pt-[96px] px-[28px] pb-0 lg:ml-[230px]" id="view">
        @yield('page_content')
    </main>
</div>
</div>
@endsection