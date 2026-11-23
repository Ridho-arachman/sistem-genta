@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Admin Panel', 'bodyClass' => 'bg-[var(--color-background)] text-[var(--color-text)]'])

@php
    $navBase = 'group relative flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[13px] transition-colors duration-150';
    $navIdle = 'font-medium text-[var(--color-text-muted)] hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-secondary)]';
    $navActive = 'bg-[var(--color-primary)]/10 font-semibold text-[var(--color-primary)]';
    $navSection = 'menu-title';

    $user = auth()->user();
    $adminMenuItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'squares-2x2', 'match' => 'admin.dashboard'],
        ['label' => 'Data Dapur', 'route' => 'admin.dapurs.index', 'icon' => 'home', 'match' => 'admin.dapurs*'],
        ['label' => 'Monitoring CCTV', 'route' => 'admin.cctvs.index', 'icon' => 'video', 'match' => ['admin.cctvs.*', 'admin.cctv-anomalies.*']],
        ['label' => 'Laporan Pengawasan', 'route' => 'admin.laporan.index', 'icon' => 'document-text', 'match' => 'admin.laporan*'],
        ['label' => 'Manajemen Pengguna', 'route' => 'admin.users', 'icon' => 'users', 'match' => 'admin.users*'],
    ];
    $managerMenuItems = [
        ['label' => 'Dashboard', 'route' => 'manajerial.dashboard', 'icon' => 'squares-2x2', 'match' => 'manajerial.dashboard'],
        ['label' => 'Data Dapur', 'route' => 'manajerial.dapurs.index', 'icon' => 'home', 'match' => 'manajerial.dapurs*'],
        ['label' => 'Monitoring CCTV', 'route' => 'manajerial.cctvs.index', 'icon' => 'video', 'match' => ['manajerial.cctvs.*', 'manajerial.cctv-anomalies.*']],
        ['label' => 'Inspeksi', 'route' => 'manajerial.inspeksi.index', 'icon' => 'document-text', 'match' => 'manajerial.inspeksi*'],
    ];
    $kepalaDapurMenuItems = [
        ['label' => 'Dashboard', 'route' => 'kepala-dapur.dashboard', 'icon' => 'squares-2x2', 'match' => 'kepala-dapur.dashboard'],
        ['label' => 'Data Dapur', 'route' => 'kepala-dapur.dapurs.index', 'icon' => 'home', 'match' => 'kepala-dapur.dapurs*'],
        ['label' => 'Monitoring CCTV', 'route' => 'kepala-dapur.cctvs.index', 'icon' => 'video', 'match' => 'kepala-dapur.cctvs*'],
        ['label' => 'Anomali CCTV', 'route' => 'kepala-dapur.cctv-anomalies.index', 'icon' => 'exclamation-triangle', 'match' => 'kepala-dapur.cctv-anomalies*'],
    ];
    $menuItems = match (true) {
        $user->isAdmin() => $adminMenuItems,
        $user->isManajerial() => $managerMenuItems,
        $user->isKepalaDapur() => $kepalaDapurMenuItems,
        default => [],
    };
    $profileRoute = match (true) {
        $user->isAdmin() => 'admin.settings.profile',
        $user->isManajerial() => 'manajerial.settings',
        $user->isKepalaDapur() => 'kepala-dapur.settings',
        default => null,
    };
    $systemItems = match (true) {
        $user->isAdmin() => [['label' => 'Pengaturan', 'route' => 'admin.settings', 'icon' => 'cog', 'match' => 'admin.settings']],
        $user->isManajerial() => [['label' => 'Pengaturan', 'route' => 'manajerial.settings', 'icon' => 'cog', 'match' => 'manajerial.settings']],
        $user->isKepalaDapur() => [['label' => 'Pengaturan', 'route' => 'kepala-dapur.settings', 'icon' => 'cog', 'match' => 'kepala-dapur.settings']],
        default => [],
    };
@endphp

@section('header')
<div>
    <x-navbar-web
        :profile-route="$profileRoute ?? 'admin.settings.profile'"
        :show-hamburger="true"
        :show-mode-badge="false"
        brand-logo="logo.png"
        :brand-alt="settings('app_name_desktop') ?? 'GENTA Pangan Banten'"
        :brand-text="strtoupper(settings('app_name_desktop') ?? 'GENTA')"
        brand-sub-text="ADMIN PANEL" />
</div>
@endsection

@section('content')
<div>
<div class="flex overflow-y-hidden items-start min-h-screen">
    <aside
        id="sidebar"
        x-data
        :class="$store.ui.sidebarOpen ? 'max-[900px]:translate-x-0' : 'max-[900px]:-translate-x-full'"
        class="menu w-[230px] fixed top-[72px] left-0 bottom-0 z-999 flex-shrink-0 bg-base-100 border-r border-[var(--color-border)] p-[22px_14px] overflow-y-auto transition-transform max-[900px]:shadow-[4px_0_20px_rgb(0_0_0_/_0.1)] min-[901px]:translate-x-0"
        @click.outside="$store.ui.close()">

        <ul class="gap-0.5">
            <li class="{{ $navSection }}">Menu Utama</li>
            @foreach ($menuItems as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="{{ $navBase }} {{ $isActive ? $navActive : $navIdle }}"
                       @if($isActive) aria-current="page" @endif>
                        @if($isActive)
                            <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-full bg-[var(--color-primary)]"></span>
                        @endif
                        <x-heroicon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <ul class="gap-0.5 mt-5">
            <li class="{{ $navSection }}">Sistem</li>
            @foreach ($systemItems as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="{{ $navBase }} {{ $isActive ? $navActive : $navIdle }}"
                       @if($isActive) aria-current="page" @endif>
                        @if($isActive)
                            <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-full bg-[var(--color-primary)]"></span>
                        @endif
                        <x-heroicon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="card border border-[var(--color-border)] mt-6 bg-base-200 p-3.5">
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
