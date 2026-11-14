@php
    $appName = settings('app_name_web') ?? config('app.name', 'GENTA Pangan Banten');
    $navLink = 'relative px-1 py-2 text-sm font-medium text-[var(--color-text-muted)] transition-colors duration-200 hover:text-[var(--color-primary)] after:absolute after:bottom-0 after:left-1/2 after:h-0.5 after:w-0 after:bg-[var(--color-primary)] after:transition-[width,left] after:duration-300 hover:after:left-0 hover:after:w-full';
@endphp

{{--
    Navbar untuk halaman publik (landing + halaman auth: login, lupa password,
    reset password). Dipakai home.blade.php dan layouts/app.blade.php.
--}}
<nav class="sticky top-0 z-50 border-b border-[var(--color-border)] bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex min-w-0 items-center gap-2 transition-transform duration-300 hover:scale-105" aria-label="{{ $appName }} - Beranda">
                <img src="{{ asset('logo.png') }}" alt="{{ $appName }}" class="h-8 w-8 object-contain">
                <span class="truncate text-xl font-semibold tracking-tight text-[var(--color-secondary)]">{{ $appName }}</span>
            </a>
            <div class="hidden items-center gap-6 md:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="{{ $navLink }} {{ request()->routeIs('admin.dashboard') ? 'text-[var(--color-primary)] after:left-0 after:w-full' : '' }}">Dashboard</a>
                    <button type="submit" form="logout-form" class="{{ $navLink }} text-red-600 hover:text-red-700">Keluar</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('home') }}" class="{{ $navLink }} {{ request()->routeIs('home') ? 'text-[var(--color-primary)] after:left-0 after:w-full' : '' }}">Beranda</a>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
                @endauth
            </div>
        </div>
    </div>
</nav>