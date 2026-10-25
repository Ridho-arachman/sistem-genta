<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0a0a0b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GENTAPANGAN">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

        body {
            background-color: #0a0a0b;
            color: #e4e4e7;
            font-family: 'Inter', system-ui, sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', system-ui, sans-serif;
        }

        .text-mono {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        .bg-surface {
            background-color: #101014;
        }

        .bg-surface-hover {
            background-color: #18181b;
        }

        .shadow-glow {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.1);
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased flex flex-col">

    {{-- Navigation --}}
    <nav class="border-b border-zinc-800 bg-surface/95 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="font-semibold text-xl tracking-tight text-white">GENTA<span class="text-green-500">PANGAN</span></span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-zinc-300 hover:text-white transition-colors">Dashboard</a>
                    @auth
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="text-sm font-medium text-zinc-400 hover:text-red-400 transition-colors">Keluar</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-300 hover:text-white transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg transition-colors">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-zinc-800 bg-surface py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs text-zinc-500">
                {{ config('app.name') }} &mdash; Monitoring CCTV &amp; Absensi Geotag Dapur
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
