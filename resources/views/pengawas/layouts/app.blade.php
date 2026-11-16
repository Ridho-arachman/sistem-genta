@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Pengawas Dashboard', 'bodyClass' => 'bg-[var(--color-background)] text-[var(--color-text)]'])

@section('header')
@if (request()->cookie('capacitor_app'))
    <x-navbar-app
        :profile-route="'pengawas.settings'"
        brand-logo="icons/icon-512.png"
        brand-alt="Genta Logo"
        brand-text="GENTA"
        brand-sub-text="PENGAWASAN DAPUR MBG" />
@else
    <x-navbar-web
        :profile-route="'pengawas.settings'"
        :show-hamburger="false"
        :show-mode-badge="true"
        topbar-class="h-18 bg-[var(--color-primary)] text-white flex items-center justify-between px-4 sm:px-6 lg:px-8 gap-4"
        brand-logo="icons/icon-512.png"
        brand-alt="Genta Logo"
        brand-text="GENTA"
        brand-sub-text="PENGAWASAN DAPUR MBG" />
@endif
@endsection

@section('content')
<main class="flex-1 pt-24 sm:pt-28 pb-20 sm:pb-0 px-4 sm:px-6 lg:px-8 min-w-0">
    @yield('page_content')
</main>

<!-- Bottom Navigation (mobile only) -->
<nav class="dock dock-sm fixed bottom-0 left-0 right-0 z-40 bg-base-100 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] sm:hidden">
    <a href="{{ route('pengawas.dashboard') }}" class="{{ request()->routeIs(['pengawas.dashboard', 'pengawas.dapurs.show']) ? 'dock-active text-primary' : 'text-[var(--color-text-muted)]' }}" data-page="home">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span class="dock-label">Beranda</span>
    </a>
    <a href="{{ route('pengawas.inspeksi.index') }}" class="{{ request()->routeIs('pengawas.inspeksi*') ? 'dock-active text-primary' : 'text-[var(--color-text-muted)]' }}" data-page="inspection">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        <span class="dock-label">Inspeksi</span>
    </a>
    <a href="{{ route('pengawas.laporan.index') }}" class="{{ request()->routeIs('pengawas.laporan*') ? 'dock-active text-primary' : 'text-[var(--color-text-muted)]' }}" data-page="reports">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <span class="dock-label">Laporan</span>
    </a>
    <a href="{{ route('pengawas.settings') }}" class="{{ request()->routeIs('pengawas.settings') ? 'dock-active text-primary' : 'text-[var(--color-text-muted)]' }}" data-page="profile">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span class="dock-label">Profil</span>
    </a>
</nav>
@endsection