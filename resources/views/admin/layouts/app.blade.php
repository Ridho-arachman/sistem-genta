@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Admin Panel', 'bodyClass' => 'bg-[var(--color-background)] text-[var(--color-text)]'])

@section('header')
<div>
    @if (request()->cookie('capacitor_app'))
        @include('components.navbar-web', [
            'profileRoute' => 'admin.settings.profile',
            'showHamburger' => true,
            'showModeBadge' => false,
            'brandLogo' => 'logo.png',
            'brandAlt' => settings('app_name_desktop') ?? 'GENTA Pangan Banten',
            'brandText' => strtoupper(settings('app_name_desktop') ?? 'GENTA'),
            'brandSubText' => 'ADMIN PANEL',
        ])
    @else
        @include('components.navbar-web', [
            'profileRoute' => 'admin.settings.profile',
            'showHamburger' => true,
            'showModeBadge' => false,
            'brandLogo' => 'logo.png',
            'brandAlt' => settings('app_name_desktop') ?? 'GENTA Pangan Banten',
            'brandText' => strtoupper(settings('app_name_desktop') ?? 'GENTA'),
            'brandSubText' => 'ADMIN PANEL',
        ])
    @endif
</div>
@endsection

@section('content')
@php
    $navBase = 'group relative flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[13px] transition-colors duration-150';
    $navIdle = 'font-medium text-[var(--color-text-muted)] hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-secondary)]';
    $navActive = 'bg-[var(--color-primary)]/10 font-semibold text-[var(--color-primary)]';
    $navSection = 'mb-1 border-b border-[var(--color-border)] px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-[var(--color-text-muted)]';
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'squares-2x2', 'match' => 'admin.dashboard'],
        ['label' => 'Data Dapur', 'route' => 'admin.dapurs.index', 'icon' => 'home', 'match' => 'admin.dapurs*'],
        ['label' => 'Monitoring CCTV', 'route' => 'admin.cctvs.index', 'icon' => 'video', 'match' => ['admin.cctvs.index', 'admin.cctv-anomalies.*']],
        ['label' => 'Laporan Pengawasan', 'route' => 'admin.laporan.index', 'icon' => 'document-text', 'match' => 'admin.laporan*'],
        ['label' => 'Manajemen Pengguna', 'route' => 'admin.users', 'icon' => 'users', 'match' => 'admin.users*'],
    ];
    $systemItems = [
        ['label' => 'Pengaturan', 'route' => 'admin.settings', 'icon' => 'cog', 'match' => 'admin.settings'],
    ];
@endphp
<div>
<div class="flex overflow-y-hidden items-start min-h-screen">
    <aside
        id="sidebar"
        :class="{ 'open': $store.ui.sidebarOpen }"
        class="fixed top-[72px] left-0 bottom-0 z-999 w-[230px] flex-shrink-0 bg-[var(--color-background)] border-r border-[var(--color-border)] p-[22px_14px] overflow-y-auto transition-transform"
        @click.outside="$store.ui.close()">

        <div class="{{ $navSection }}">Menu Utama</div>

        <nav class="flex flex-col gap-0.5">
            @foreach ($menuItems as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="{{ $navBase }} {{ $isActive ? $navActive : $navIdle }}"
                   @if($isActive) aria-current="page" @endif>
                    @if($isActive)
                        <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-full bg-[var(--color-primary)]"></span>
                    @endif
                    <x-heroicon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="{{ $navSection }} mt-5">Sistem</div>

        <nav class="flex flex-col gap-0.5">
            @foreach ($systemItems as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="{{ $navBase }} {{ $isActive ? $navActive : $navIdle }}"
                   @if($isActive) aria-current="page" @endif>
                    @if($isActive)
                        <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-full bg-[var(--color-primary)]"></span>
                    @endif
                    <x-heroicon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-6 rounded-[12px] border border-[var(--color-border)] bg-[var(--color-surface)] p-3.5">
            <div class="mb-1.5 flex items-center gap-2">
                <x-heroicon name="info" class="h-4 w-4 shrink-0 text-[var(--color-primary)]" />
                <span class="text-[11px] font-bold uppercase tracking-wide text-[var(--color-secondary)]">Mode Produksi</span>
            </div>
            <p class="text-[11px] leading-relaxed text-[var(--color-text-muted)]">Data dapur, laporan, dan CCTV terhubung ke sistem produksi.</p>
        </div>
    </aside>

    <main class="flex-1 min-w-0 pt-[64px] px-[28px] pb-0 lg:ml-[230px]" id="view">
        @yield('page_content')
    </main>
</div>
</div>
@endsection
