<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin Panel' }} - {{ config('app.name') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="GENTA Banten Admin">
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
            --color-sidebar-bg: #FAFAFA;
            --color-sidebar-border: #E8E8E8;
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

        .sidebar {
            background-color: var(--color-sidebar-bg);
            border-right: 1px solid var(--color-sidebar-border);
        }

        .header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--color-border);
        }

        .status-active { @apply bg-green-500/10 text-green-700; }
        .status-inactive { @apply bg-gray-500/10 text-gray-700; }
        .status-pending { @apply bg-yellow-500/10 text-yellow-700; }
        .status-processing { @apply bg-blue-500/10 text-blue-700; }
        .status-completed { @apply bg-green-500/10 text-green-700; }
        .status-new { @apply bg-red-500/10 text-red-700; }
    </style>
</head>
<body class="min-h-screen font-sans antialiased flex">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 sidebar flex flex-col">
        <div class="flex items-center gap-2 p-4 border-b border-[var(--color-sidebar-border)]">
            <img src="{{ asset('logo.png') }}" alt="GENTA Banten" class="w-8 h-8 object-contain">
            <span class="font-semibold text-xl tracking-tight text-[var(--color-secondary)]">GENTA <span class="text-[var(--color-primary)]">Banten</span></span>
        </div>
        
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
            
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Manajemen User
            </a>
            
            <a href="{{ route('admin.dapurs.index') }}" class="sidebar-link {{ request()->routeIs('admin.dapurs*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Data Dapur
            </a>
            
            <a href="{{ route('admin.cctvs.index') }}" class="sidebar-link {{ request()->routeIs('admin.cctvs*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Data CCTV
            </a>
            
            <a href="{{ route('admin.sp-warnings.index') }}" class="sidebar-link {{ request()->routeIs('admin.sp-warnings*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Surat Peringatan (SP)
            </a>
            
            <a href="{{ route('admin.cctv-anomalies.index') }}" class="sidebar-link {{ request()->routeIs('admin.cctv-anomalies*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Anomali CCTV
            </a>
            
            <a href="{{ route('admin.absensi.index') }}" class="sidebar-link {{ request()->routeIs('admin.absensi*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Absensi
            </a>
        </nav>
        
        <div class="p-4 border-t border-[var(--color-sidebar-border)]">
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full btn-secondary">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 ml-64 min-h-screen">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-40 header">
            <div class="max-w-full mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-[var(--color-secondary)]">{{ $pageTitle ?? 'Admin Panel' }}</h1>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-[var(--color-text-muted)]">{{ auth()->user()->name }}</span>
                        <span class="badge badge-primary">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if (session('status'))
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
                    {{ session('status') }}
                    <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-700">×</button>
                </div>
            </div>
        @endif

        {{-- Content --}}
        <div class="max-w-7xl mx-auto px-6 py-8">
            @yield('content')
        </div>
    </main>

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