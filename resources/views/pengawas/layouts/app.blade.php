@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Pengawas Dashboard', 'bodyClass' => 'bg-[var(--color-background)] text-[var(--color-text)]'])

@stack('styles')
<style>
    /* Bottom Navigation - fixed at bottom (mobile only) */
    .p-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid var(--color-border, #eee);
        display: flex;
        justify-content: space-around;
        padding: 0.5rem 0;
        z-index: 40;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }

    .p-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        color: var(--color-text-muted, #888);
        text-decoration: none;
        font-size: 0.6875rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.15s ease;
    }

    .p-nav-item.active {
        color: var(--color-primary, #E50000);
    }

    .p-nav-icon {
        width: 20px;
        height: 20px;
    }

    /* Desktop adjustments */
    @media (min-width: 768px) {
        .p-bottom-nav {
            display: none;
        }
    }
</style>

@section('header')
<header class="topbar h-18 bg-[var(--color-primary)] text-white flex items-center justify-between px-4 sm:px-6 lg:px-8 gap-4">
    <div class="brand flex items-center gap-3 font-extrabold tracking-tight">
        <img src="{{ asset('icons/icon-512.png') }}" alt="Genta Logo" class="w-10 h-10 rounded-xl object-contain">
        <div>GENTA <small class="block font-medium text-xs opacity-75 tracking-wide mt-0.5">PENGAWASAN DAPUR MBG</small></div>
    </div>
    <div class="top-actions flex items-center gap-3">
        <span class="demo-pill hidden sm:inline-block text-xs font-extrabold tracking-wide border border-white/30 rounded-full px-3 py-1">MODE PRODUKSI</span>
        @php
    $_userName = auth()->user()->name ?? 'Pengawas';
    $_nameParts = array_filter(explode(' ', trim($_userName)));
    $_initials = strtoupper($_nameParts[0][0] ?? 'P');
    if (count($_nameParts) > 1) {
        $_initials .= strtoupper($_nameParts[1][0] ?? '');
    }
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center gap-2" aria-expanded="open" aria-label="Profile menu">
        <span class="demo-pill hidden sm:inline-block text-xs font-extrabold tracking-wide border border-white/30 rounded-full px-3 py-1">{{ auth()->user()->name }}</span>
        <div class="avatar w-9 h-9 rounded-full bg-red-100 text-[var(--color-primary)] grid place-items-center font-extrabold text-sm" x-text="'{{ $_initials }}'"></div>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:leave="transition ease-in duration-75" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1">
        <a href="{{ route('pengawas.settings') }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
        </form>
    </div>
</div>
    </div>
</header>

<main class="flex-1 pt-24 sm:pt-28 pb-20 sm:pb-0 px-4 sm:px-6 lg:px-8 min-w-0">
    @yield('content')
</main>

<!-- Bottom Navigation (mobile only) -->
<nav class="p-bottom-nav fixed bottom-0 left-0 right-0 sm:hidden bg-white border-t border-[var(--color-border)] flex justify-around py-2 z-40 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]" x-data="{ active: 'home' }">
    <a href="{{ route('pengawas.dashboard') }}" class="p-nav-item {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}" data-page="home">
        <svg class="p-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Beranda</span>
    </a>
    <a href="{{ route('pengawas.absensi.index') }}" class="p-nav-item {{ request()->routeIs('pengawas.absensi*') ? 'active' : '' }}" data-page="inspection">
        <svg class="p-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        <span>Inspeksi</span>
    </a>
    <a href="{{ route('pengawas.laporan.index') }}" class="p-nav-item {{ request()->routeIs('pengawas.laporan*') ? 'active' : '' }}" data-page="reports">
        <svg class="p-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <span>Laporan</span>
    </a>
    <a href="{{ route('pengawas.settings') }}" class="p-nav-item {{ request()->routeIs('pengawas.settings') ? 'active' : '' }}" data-page="profile">
        <svg class="p-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Profil</span>
    </a>
</nav>
@endsection
