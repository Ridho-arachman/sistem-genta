<!DOCTYPE html>
<html lang="id" data-theme="genta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? settings('app_name_web') ?? config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">

    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ settings('app_name_web') ?? config('app.name') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    {{-- Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>

    {{-- Tandai request dari mobile Capacitor WebView --}}
    <script>
        if (window.Capacitor) {
            document.cookie = 'capacitor_app=1; path=/; max-age=3600; SameSite=Lax';
        }
    </script>

    {{-- Optional extra head content (styles, etc.) --}}
    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ toast: { show: false, message: '' }, init() { window.showToast = (msg) => { this.toast.message = msg; this.toast.show = true; setTimeout(() => this.toast.show = false, 2800); }; } }" class="min-h-screen font-sans antialiased flex flex-col {{ $bodyClass ?? '' }}">
    {{-- Flash messages --}}
    <x-flash-message />

    {{-- Header / Nav slot (override di tiap role layout) --}}
    @yield('header')

    {{-- Content diatur oleh role layout masing-masing (admin, pengawas, dll.) --}}
    <main class="flex-1 w-full py-8">
        @yield('content')
    </main>

    {{-- Footer slot (override di tiap role layout) --}}
    @yield('footer')

    {{-- Toast Notification --}}
    <x-toast />

</body>
@stack('scripts')
</html>
