<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Pengawas Dashboard' }} - {{ config('app.name') }}</title>
    
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

        .btn-primary {
            @apply bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }

        .btn-secondary {
            @apply bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] text-[var(--color-text)] border border-[var(--color-border)] px-4 py-2 rounded-lg font-medium transition-colors;
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

        .badge-success { @apply bg-green-500/10 text-green-700; }
        .badge-warning { @apply bg-yellow-500/10 text-yellow-700; }
        .badge-danger { @apply bg-red-500/10 text-red-700; }
        .badge-gray { @apply bg-gray-500/10 text-gray-700; }
        .badge-primary { @apply bg-[var(--color-primary)]/10 text-[var(--color-primary)]; }

        .card {
            @apply bg-white border border-[var(--color-border)] rounded-xl shadow-sm;
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
                    <a href="{{ route('pengawas.dashboard') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
                    <a href="{{ route('pengawas.absensi.index') }}" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Absensi</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">Keluar</button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- Mobile menu --}}
        <div class="md:hidden border-b border-[var(--color-border)] bg-white px-4 py-3">
            <div class="flex justify-around">
                <a href="{{ route('pengawas.dashboard') }}" class="nav-link {{ request()->routeIs('pengawas.dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>
                <a href="{{ route('pengawas.absensi.index') }}" class="nav-link {{ request()->routeIs('pengawas.absensi*') ? 'nav-link-active' : '' }}">Absensi</a>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
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
                {{ config('app.name') }} &mdash; Absensi Pengawas
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

    </body>
</html>