<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="GENTA Banten">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

        :root {
            --color-primary: #E50000;
            --color-primary-dark: #B20000;
            --color-primary-light: #FF3333;
            --color-secondary: #000000;
            --color-background: #FFFFFF;
            --color-surface: #F5F5F5;
            --color-surface-hover: #EEEEEE;
            --color-border: #E0E0E0;
            --color-text: #000000;
            --color-text-muted: #666666;
            --color-success: #16A34A;
            --color-warning: #F59E0B;
            --color-danger: #DC2626;
        }

        body {
            background-color: var(--color-background);
            color: var(--color-text);
            font-family: 'Inter', system-ui, sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            color: var(--color-secondary);
        }

        .text-mono {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        .border-primary {
            border-color: var(--color-primary);
        }

        .border-secondary {
            border-color: var(--color-border);
        }

        .bg-surface {
            background-color: var(--color-surface);
        }

        .bg-surface-hover {
            background-color: var(--color-surface-hover);
        }

        .bg-primary {
            background-color: var(--color-primary);
        }

        .bg-primary-dark {
            background-color: var(--color-primary-dark);
        }

        .bg-primary-light {
            background-color: var(--color-primary-light);
        }

        .text-primary {
            color: var(--color-primary);
        }

        .text-secondary {
            color: var(--color-secondary);
        }

        .text-muted {
            color: var(--color-text-muted);
        }

        .shadow-glow {
            box-shadow: 0 0 20px rgba(229, 0, 0, 0.15);
        }

        .btn-primary {
            @apply bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }

        .btn-secondary {
            @apply bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] text-[var(--color-text)] border border-[var(--color-border)] px-4 py-2 rounded-lg font-medium transition-colors;
        }

        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }

        .btn-outline-primary {
            @apply border-2 border-[var(--color-primary)] text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }

        .input-field {
            @apply w-full bg-white border border-[var(--color-border)] rounded-lg px-4 py-2 text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition-all;
        }

        .table-th {
            @apply px-4 py-3 text-left text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider bg-[var(--color-surface)];
        }

        .table-td {
            @apply px-4 py-3 text-sm text-[var(--color-text)];
        }

        .badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }

        .badge-primary {
            @apply bg-[var(--color-primary)]/10 text-[var(--color-primary)];
        }

        .badge-success {
            @apply bg-green-500/10 text-green-700;
        }

        .badge-warning {
            @apply bg-yellow-500/10 text-yellow-700;
        }

        .badge-danger {
            @apply bg-red-500/10 text-red-700;
        }

        .badge-gray {
            @apply bg-gray-500/10 text-gray-700;
        }

        .card {
            @apply bg-white border border-[var(--color-border)] rounded-xl shadow-sm;
        }

        .sidebar-link {
            @apply flex items-center gap-3 px-4 py-3 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] hover:bg-[var(--color-primary)]/5 rounded-lg transition-colors;
        }

        .sidebar-link.active {
            @apply text-[var(--color-primary)] bg-[var(--color-primary)]/10 font-medium;
        }

        .nav-link {
            @apply text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors;
        }

        .nav-link-active {
            @apply text-[var(--color-primary)] font-medium;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased flex flex-col">

    {{-- Navigation --}}
    <nav class="border-b border-[var(--color-border)] bg-white/95 backdrop-blur sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-8 h-8 object-contain">
                    <span class="font-semibold text-xl tracking-tight text-[var(--color-secondary)]">GENTA <span class="text-[var(--color-primary)]">Banten</span></span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="nav-link text-red-600 hover:text-red-700">Keluar</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">Beranda</a>
                        <a href="{{ route('tentang') }}" class="nav-link {{ request()->routeIs('tentang') ? 'nav-link-active' : '' }}">Tentang</a>
                        <a href="{{ route('sektor') }}" class="nav-link {{ request()->routeIs('sektor') ? 'nav-link-active' : '' }}">Sektor</a>
                        <a href="{{ route('program') }}" class="nav-link {{ request()->routeIs('program') ? 'nav-link-active' : '' }}">Program</a>
                        <a href="{{ route('galeri') }}" class="nav-link {{ request()->routeIs('galeri') ? 'nav-link-active' : '' }}">Galeri</a>
                        <a href="{{ route('kontak') }}" class="nav-link {{ request()->routeIs('kontak') ? 'nav-link-active' : '' }}">Kontak</a>
                        <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[var(--color-border)] bg-[var(--color-surface)] py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs text-[var(--color-text-muted)]">
                {{ config('app.name') }} &mdash; Monitoring CCTV & Absensi Geotag Dapur
            </p>
        </div>
    </footer>

    {{-- Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(function(registration) {
                        console.log('SW registered: ', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('SW registration failed: ', error);
                    });
            });
        }
    </script>
</body>
</html>