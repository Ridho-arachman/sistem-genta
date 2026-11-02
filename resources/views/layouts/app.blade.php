@extends('layouts.base', ['pageTitle' => $pageTitle ?? config('app.name'), 'bodyClass' => 'bg-white'])

@section('header')
<nav class="border-b border-[var(--color-border)] bg-white/95 backdrop-blur sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_web') ?? 'GENTA Banten' }}" class="w-8 h-8 object-contain">
                <span class="font-semibold text-xl tracking-tight text-[var(--color-secondary)]">{{ settings('app_name_web') ?? 'GENTA Banten' }}</span>
            </div>
            <div class="hidden md:flex items-center gap-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-[var(--color-primary)]' : '' }}">Dashboard</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link text-sm font-medium text-red-600 hover:text-red-700 transition-colors">Keluar</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('home') }}" class="nav-link text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('home') ? 'text-[var(--color-primary)]' : '' }}">Beranda</a>
                    <a href="{{ route('login') }}" class="btn-primary inline-flex items-center justify-center px-4 py-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white rounded-lg font-medium transition-colors">Masuk</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>
@endsection
