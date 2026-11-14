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

    {{-- Service worker & penanda Capacitor didaftarkan sekali di resources/js/app.js --}}

    {{-- Optional extra head content (styles, etc.) --}}
    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased flex flex-col {{ $bodyClass ?? '' }}">
    {{-- Notifikasi lewat <x-toast /> di bawah --}}

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
