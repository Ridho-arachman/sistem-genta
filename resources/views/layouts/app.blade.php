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
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-[var(--color-primary)]' : '' }}">Dashboard</a>
                    <button type="submit" form="logout-form" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">Keluar</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('home') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('home') ? 'text-[var(--color-primary)]' : '' }}">Beranda</a>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm inline-flex items-center justify-center">Masuk</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
@endsection
