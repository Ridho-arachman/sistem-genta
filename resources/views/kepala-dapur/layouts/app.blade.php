@extends('layouts.base', ['pageTitle' => $pageTitle ?? 'Kepala Dapur Dashboard', 'bodyClass' => ''])

@section('header')
<nav class="border-b border-[var(--color-border)] bg-white/95 backdrop-blur sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-8 h-8 object-contain">
                <span class="font-semibold text-xl tracking-tight text-[var(--color-secondary)]">GENTA <span class="text-[var(--color-primary)]">Banten</span></span>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('kepala-dapur.dashboard') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('kepala-dapur.dashboard') ? 'text-[var(--color-primary)]' : '' }}">Dashboard</a>
                <a href="{{ route('kepala-dapur.cctvs.index') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('kepala-dapur.cctvs*') ? 'text-[var(--color-primary)]' : '' }}">CCTV</a>
                <a href="{{ route('kepala-dapur.cctv-anomalies.index') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('kepala-dapur.cctv-anomalies*') ? 'text-[var(--color-primary)]' : '' }}">Anomali</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2" aria-expanded="open" aria-label="Profile menu">
                        <span class="text-sm font-medium text-[var(--color-text)]">{{ auth()->user()->name }}</span>
                        <div class="avatar w-8 h-8 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] grid place-items-center font-extrabold text-sm" x-text="auth()->user()->name.charAt(0).toUpperCase()"></div>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:leave="transition ease-in duration-75" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-[var(--color-border)] rounded-lg shadow-lg z-50 py-1">
                        <a href="{{ route('kepala-dapur.settings') }}" class="block px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface)]">Pengaturan</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[var(--color-surface)]">Keluar</button>
                        </form>
                    </div>
                </div>
                {{-- Mobile menu button --}}
                <button class="md:hidden p-2 rounded-lg hover:bg-[var(--color-surface)] transition" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="{ 'rotate-90': mobileMenuOpen }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        {{-- Mobile menu --}}
        <div class="md:hidden border-b border-[var(--color-border)] bg-white px-4 py-3 overflow-x-auto" x-show="mobileMenuOpen" x-transition>
            <div class="flex gap-4 whitespace-nowrap">
                <a href="{{ route('kepala-dapur.dashboard') }}" class="nav-link px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('kepala-dapur.dashboard') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : 'text-[var(--color-text-muted)] hover:text-[var(--color-primary)]' }}">Dashboard</a>
                <a href="{{ route('kepala-dapur.cctvs.index') }}" class="nav-link px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('kepala-dapur.cctvs*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : 'text-[var(--color-text-muted)] hover:text-[var(--color-primary)]' }}">CCTV</a>
                <a href="{{ route('kepala-dapur.cctv-anomalies.index') }}" class="nav-link px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('kepala-dapur.cctv-anomalies*') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : 'text-[var(--color-text-muted)] hover:text-[var(--color-primary)]' }}">Anomali</a>
            </div>
        </div>
    </div>
</nav>

<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>
@endsection
