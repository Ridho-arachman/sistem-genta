<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Kepala Dapur Dashboard' }} - {{ config('app.name') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="GENTA Banten">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{
    mobileMenuOpen: false,
    toast: { show: false, message: '' },
    init() {
        window.showToast = (msg) => {
            this.toast.message = msg;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 2800);
        };
    }
}" class="min-h-screen font-sans antialiased flex flex-col">
    {{-- Navigation --}}
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
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">Keluar</button>
                    </form>
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
    </nav>

    {{-- Flash messages --}}
    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm" x-show="true" x-transition>{{ session('status') }}</div>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[var(--color-border)] bg-[var(--color-surface)] py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center gap-2 mb-2">
                <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-5 h-5 object-contain">
                <span class="font-semibold text-sm tracking-tight text-[var(--color-secondary)]">GENTA <span class="text-[var(--color-primary)]">Banten</span></span>
            </div>
            <p class="text-center text-xs text-[var(--color-text-muted)]">
                {{ config('app.name') }} &mdash; Kepala Dapur
            </p>
        </div>
    </footer>

    {{-- Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(function(registration) { console.log('SW registered: ', registration.scope); })
                    .catch(function(error) { console.log('SW registration failed: ', error); });
            });
        }
    </script>

    {{-- Toast Notification --}}
    <div class="toast fixed bottom-6 right-6 bg-[var(--color-secondary)] text-white px-5 py-3 rounded-lg shadow-lg z-[9999] transition-opacity duration-300"
         x-show="toast.show"
         x-transition:enter="opacity-0 transform translate-y-2"
         x-transition:leave="opacity-0 transform translate-y-2"
         x-cloak>
        <span x-text="toast.message"></span>
    </div>
</body>
@stack('scripts')
</html>