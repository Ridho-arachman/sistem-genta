@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Manajerial Panel', 'bodyClass' => 'app'])

@section('header')
<div x-data="{ sidebarOpen: false }">
<header class="topbar bg-white border-b border-[var(--color-border)] sticky top-0 z-100">
    <div class="brand flex items-center gap-3 px-6 py-4">
        <div class="brandmark w-10 h-10 rounded-xl bg-[var(--color-primary)] grid place-items-center text-white font-bold text-xl">G</div>
        <div>GENTA <small class="text-xs font-medium opacity-75 tracking-wide block mt-0.5">MANAJERIAL PANEL</small></div>
    </div>
    <div class="top-actions flex items-center gap-3">
        <span class="demo-pill hidden sm:inline-block text-xs font-extrabold tracking-wide border border-[var(--color-border)] rounded-full px-3 py-1.5">MODE PRODUKSI</span>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2" aria-expanded="open" aria-label="Profile menu">
                <span class="demo-pill hidden sm:inline-block text-xs font-extrabold tracking-wide border border-[var(--color-border)] rounded-full px-3 py-1.5">{{ auth()->user()->name }}</span>
                <div class="avatar w-9 h-9 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center font-extrabold text-sm" x-text="auth()->user()->name.charAt(0).toUpperCase()"></div>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:leave="transition ease-in duration-75" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1">
                <a href="{{ route('manajerial.settings') }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>

<div class="shell flex flex-1">
    <aside id="sidebar" 
        :class="{ 'open': sidebarOpen, 'lg:translate-x-0': true }" 
        class="fixed top-0 left-0 bottom-0 z-999 w-[230px] flex-shrink-0 bg-[var(--color-surface)] border-r border-[var(--color-border)] p-4 overflow-y-auto transition-slide lg:static lg:translate-x-0 lg:shadow-none lg:z-auto"
        x-show="sidebarOpen || true"
        @click.outside="sidebarOpen = false">
        
        <div class="side-label text-[10px] tracking-widest text-[var(--color-text-muted)] font-extrabold px-3 py-2 uppercase">MENU UTAMA</div>
        
        <a href="{{ route('manajerial.dashboard') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.dashboard') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="dashboard">
            <span class="sidebar-icon w-5 text-center text-lg">▦</span> Dashboard
        </a>
        
        <a href="{{ route('manajerial.dapurs.index') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.dapurs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="dapurs">
            <span class="sidebar-icon w-5 text-center text-lg">⌂</span> Data Dapur
        </a>
        
        <a href="{{ route('manajerial.cctvs.index') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.cctvs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="cctvs">
            <span class="sidebar-icon w-5 text-center text-lg">▣</span> Data CCTV
        </a>
        
        <a href="{{ route('manajerial.cctvs.index') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.cctvs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="cctv">
            <span class="sidebar-icon w-5 text-center text-lg">▣</span> Monitoring CCTV
        </a>
        
        <a href="{{ route('manajerial.cctv-anomalies.index') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.cctv-anomalies*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="cctv-anomalies">
            <span class="sidebar-icon w-5 text-center text-lg">▣</span> Anomali CCTV
        </a>
        
        <a href="{{ route('manajerial.sp-warnings.index') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.sp-warnings*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="sp-warnings">
            <span class="sidebar-icon w-5 text-center text-lg">⚠</span> Surat Peringatan (SP)
        </a>
        
        <a href="{{ route('manajerial.inspeksi.index') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.inspeksi*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="inspeksi">
            <span class="sidebar-icon w-5 text-center text-lg">▣</span> Inspeksi
        </a>
        
        <div class="side-label text-[10px] tracking-widest text-[var(--color-text-muted)] font-extrabold px-3 pt-6 pb-2 uppercase">SISTEM</div>
        <a href="{{ route('manajerial.settings') }}" 
           class="sidebar-nav flex items-center gap-3 px-3 py-3 rounded-lg text-sm text-[var(--color-text-muted)] transition-colors
                  {{ request()->routeIs('manajerial.settings') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)] font-medium' : 'hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)]' }}"
           data-page="settings">
            <span class="sidebar-icon w-5 text-center text-lg">⚙</span> Pengaturan
        </a>
        
        <div class="side-note mt-7 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-xl p-4 text-[12px] leading-relaxed text-[var(--color-text-muted)]">
            <b>ⓘ Mode Produksi</b><br>Data dapur, laporan, dan CCTV terhubung ke sistem produksi.
        </div>
    </aside>
    
    <main class="main-content lg:ml-[230px] flex-1 min-w-0 p-6 lg:p-8" id="view">
        @yield('content')
    </main>
</div>
</div>
@endsection
