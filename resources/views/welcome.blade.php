<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ settings('app_tagline') ?? 'Gerakan Nasional Tani untuk Kemandirian Pangan Banten. Ketahanan pangan melalui pertanian, peternakan, perikanan, dan UMKM.' }}">
    <title>{{ settings('app_name_web') ?? config('app.name', 'GENTA Pangan Banten') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E50000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    
    
    @vite(["resources/css/app.css", "resources/js/app.js"])
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
            --color-text: #1F2937;
            --color-text-muted: #6B7280;
            --color-agri-green: #2E7D32;
            --color-agri-light: #8FBC8F;
            --color-agri-dark: #1B5E20;
            --color-livestock: #6D4C41;
            --color-fishery: #1976D2;
            --color-umkm: #F57C00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--color-background);
            color: var(--color-text);
            font-family: 'Inter', system-ui, sans-serif;
            overflow-x: hidden;
            overflow-y: auto;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            color: var(--color-secondary);
        }

        .text-mono {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        /* Layout */
        .landing-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
        }

        .landing-section {
            position: relative;
            z-index: 10;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        /* Hero is always visible, no animation */
        .hero-section {
            opacity: 1 !important;
            transform: none !important;
        }

        /* Other sections start hidden, reveal on scroll */
        .landing-section:not(.hero-section) {
            opacity: 0;
            transform: translateY(30px);
        }

        .landing-section:not(.hero-section).visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Navigation */
        .landing-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--color-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.3s ease, background 0.3s ease;
        }

        .landing-nav.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 2px 6px rgba(0, 0, 0, 0.04);
            background: rgba(255, 255, 255, 0.99);
        }

        .nav-link {
            @apply relative text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors duration-200 py-2 px-1;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--color-primary);
            transition: width 0.3s ease, left 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
            left: 0;
        }

        .btn-primary {
            @apply bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            @apply bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] text-[var(--color-text)] border border-[var(--color-border)] px-6 py-2.5 rounded-lg font-medium transition-all duration-200;
        }

        .btn-icon {
            @apply flex items-center justify-center w-12 h-12 rounded-lg bg-[var(--color-surface)] hover:bg-[var(--color-primary)] hover:text-white border border-[var(--color-border)] text-[var(--color-text-muted)] transition-all duration-200;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 0, 0, 0.2);
        }

        .btn-icon:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* Logo hover */
        .nav-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .nav-logo:hover {
            transform: scale(1.05);
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.5rem;
            position: relative;
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1rem;
            color: var(--color-secondary);
        }

        .hero-title .highlight {
            color: var(--color-primary);
        }

        .hero-tagline {
            font-size: clamp(1.125rem, 2.5vw, 1.5rem);
            color: var(--color-text-muted);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        /* Section Styles */
        .section {
            padding: 5rem 1.5rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--color-secondary);
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* About Section */
        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .about-card:hover {
            border-color: var(--color-primary);
            box-shadow: 0 8px 30px rgba(229, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .about-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .about-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-secondary);
        }

        .about-desc {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Sectors Section */
        .sectors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sector-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .sector-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--sector-color);
        }

        .sector-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
        }

        .sector-image {
            width: 100%;
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .sector-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.5) 100%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 1.5rem;
        }

        .sector-content {
            padding: 1.5rem;
            text-align: center;
        }

        .sector-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-secondary);
        }

        .sector-desc {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Programs Section */
        .programs-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 1.5rem;
        }

        .program-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .program-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--program-color);
        }

        .program-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
        }

        .program-image {
            width: 100%;
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .program-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.5) 100%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 1.5rem;
        }

        .program-content {
            padding: 1.5rem;
            text-align: center;
        }

        .program-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .program-badge {
            background: var(--program-color);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 1.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .program-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--color-secondary);
        }

        .program-desc {
            color: var(--color-text-muted);
            line-height: 1.7;
            margin-bottom: 1rem;
        }

        .program-highlights {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }

        .program-highlights li {
            padding: 0.375rem 0;
            border-bottom: 1px solid var(--color-border);
            font-size: 0.8125rem;
            color: var(--color-text-muted);
            line-height: 1.6;
        }

        .program-highlights li:last-child {
            border-bottom: none;
        }

        .program-highlights strong {
            color: var(--color-text);
        }

        /* Gallery Section */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-item {
            aspect-ratio: 4/3;
            border-radius: 0.75rem;
            overflow: hidden;
            background: var(--color-surface);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .gallery-item:hover {
            transform: scale(1.015);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .gallery-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--color-text-muted);
        }

        /* Contact Section */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .contact-item {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            border-color: var(--color-primary);
            transform: translateY(-4px);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 1rem;
            background: var(--color-primary)/10;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        /* Footer */
        .landing-footer {
            background: var(--color-background);
            border-top: 1px solid var(--color-border);
            padding: 3rem 1.5rem 1.5rem;
            position: relative;
        }

        .landing-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-primary-light) 50%, var(--color-agri-green) 100%);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 2rem auto 0;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
            text-align: center;
        }

        .footer-copyright {
            color: var(--color-text-muted);
            font-size: 0.8125rem;
        }

        .newsletter-form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .newsletter-input {
            @apply w-full bg-white border border-[var(--color-border)] rounded-lg px-4 py-2.5 text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition-all text-sm;
        }

        .newsletter-btn {
            @apply bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white px-4 py-2.5 rounded-lg font-medium text-sm transition-colors w-full;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 3rem auto 0;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .footer-copyright {
            color: var(--color-text-muted);
            font-size: 0.8125rem;
        }

        .footer-legal {
            display: flex;
            gap: 1.5rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        /* Responsive */
        @media (max-width: 768px) {
            .section {
                padding: 4rem 1rem;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-tagline {
                font-size: 1rem;
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus visible */
        a:focus-visible,
        button:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
    </style>
</head>
<body class="font-sans antialiased">
    
    {{-- Navigation --}}
    <nav class="landing-nav" id="landing-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2 whitespace-nowrap flex-shrink-0">
                    <a href="{{ route('home') }}" class="nav-logo" aria-label="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }} - Beranda">
                        <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}" class="w-8 h-8 object-contain">
                        <span class="font-semibold text-xl tracking-tight text-[var(--color-secondary)]">{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="nav-link text-red-600 hover:text-red-700">Keluar</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">
                            <x-heroicon name="user" class="w-5 h-5 mr-2 inline" />
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="landing-wrapper">
        
        {{-- Hero Section --}}
        <section class="hero-section landing-section">
            <div class="hero-content">
                <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}" class="hero-logo animate-fade-in-up">
                
                <h1 class="hero-title animate-fade-in-up delay-1">
                    {{ str_replace('Pangan Banten', '<span class="highlight">Pangan Banten</span>', settings('app_name_web') ?? 'GENTA Pangan Banten') }}
                </h1>
                
                <p class="hero-tagline animate-fade-in-up delay-2">
                    {{ settings('app_tagline') ?? 'Gerakan Nasional Tani untuk Kemandirian Pangan , ketahanan pangan melalui pertanian, peternakan, perikanan, dan UMKM' }}
                </p>
                
                <div class="hero-buttons animate-fade-in-up delay-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">
                            <x-heroicon name="home" class="w-5 h-5 mr-2 inline" />
                            Buka Dashboard
                        </a>
                    @else
                        <a href="#kontak" class="btn-primary">
                            <x-heroicon name="mail" class="w-5 h-5 mr-2 inline" />
                            Hubungi Kami
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- About Section --}}
        <section id="tentang" class="section landing-section">
            <div class="max-w-7xl mx-auto">
                <div class="section-header">
                    <h2 class="section-title">Tentang GENTA Pangan Banten</h2>
                    <p class="section-subtitle">
                        Gerakan Nasional Tani untuk Kemandirian Pangan Banten adalah wadah kolaboratif untuk 
                        memacu ketahanan pangan melalui pertanian, peternakan, perikanan, dan UMKM.
                    </p>
                </div>

                <div class="about-grid">
                    <div class="about-card" style="--sector-color: #2E7D32;">
                        <div class="about-icon" style="background: var(--color-agri-light); color: white;">
                            <x-heroicon name="sparkles" class="w-8 h-8" />
                        </div>
                        <h3 class="about-title">Visi</h3>
                        <p class="about-desc">Terwujudnya ketahanan pangan Banten melalui sinergi petani, nelayan, dan pelaku UMKM lokal.</p>
                    </div>
                    <div class="about-card" style="--sector-color: #E50000;">
                        <div class="about-icon" style="background: var(--color-primary)/10; color: var(--color-primary);">
                            <x-heroicon name="arrow-up-right" class="w-8 h-8" />
                        </div>
                        <h3 class="about-title">Misi</h3>
                        <p class="about-desc">Memperkuat kapasitas produksi pangan lokal melalui pelatihan, teknologi, dan akses pasar.</p>
                    </div>
                    <div class="about-card" style="--sector-color: #F57C00;">
                        <div class="about-icon" style="background: #FFF3E0; color: #F57C00;">
                            <x-heroicon name="shield-check" class="w-8 h-8" />
                        </div>
                        <h3 class="about-title">Nilai Kami</h3>
                        <p class="about-desc">Kolaborasi, Keberlanjutan, dan Kreativitas sebagai dasar setiap langkah kami.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sectors Section (Bidang Pertanian Utama) --}}
        <section id="sektor" class="section landing-section" style="background: var(--color-surface);">
            <div class="max-w-7xl mx-auto">
                <div class="section-header">
                    <h2 class="section-title">Bidang Pertanian Utama</h2>
                    <p class="section-subtitle">
                        Empat pilar komoditas tanaman untuk kemandirian pangan ,  selaras semangat GENTA: Gerakan Nasional Tani untuk Kemandirian Pangan.
                    </p>
                </div>

                <div class="sectors-grid">
                    <div class="sector-card" style="--sector-color: #2E7D32;">
                        <div class="sector-image" style="background-image: url('https://images.unsplash.com/photo-1500937386664-56d1dfef3854?q=80&w=800&auto=format&fit=crop');"></div>
                        <div class="sector-content">
                            <h3 class="sector-title">Tanaman Pangan</h3>
                            <p class="sector-desc">Beras, jagung, kedelai, kacang tanah ,  komoditas utama ketahanan pangan nasional.</p>
                        </div>
                    </div>
                    <div class="sector-card" style="--sector-color: #43A047;">
                        <div class="sector-image" style="background-image: url('https://images.unsplash.com/photo-1610832958506-aa56368176cf?q=80&w=800&auto=format&fit=crop');"></div>
                        <div class="sector-content">
                            <h3 class="sector-title">Hortikultura</h3>
                            <p class="sector-desc">Sayuran, buah-buahan, tanaman obat, dan rempah-rempah bernilai ekonomi tinggi.</p>
                        </div>
                    </div>
                    <div class="sector-card" style="--sector-color: #2E7D32;">
                        <div class="sector-image" style="background-image: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=800&auto=format&fit=crop');"></div>
                        <div class="sector-content">
                            <h3 class="sector-title">Perkebunan</h3>
                            <p class="sector-desc">Kelapa sawit, kelapa, kakao, kopi, karet ,  komoditas ekspor dan industri hulu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Programs Section --}}
        <section id="program" class="section landing-section">
            <div class="max-w-1000 mx-auto">
                <div class="section-header">
                    <h2 class="section-title">Program Unggulan GENTA</h2>
                    <p class="section-subtitle">
                        Program-program andalan untuk mewujudkan kemandirian pangan melalui inovasi dan kolaborasi.
                    </p>
                </div>

                <div class="programs-container">
                    <div class="program-card" style="--program-color: #E50000;">
                        <div class="program-image" style="background-image: url('/images/programs/MBG-2-765x510.webp');">
                            <div class="program-overlay"></div>
                        </div>
                        <div class="program-content">
                            <div class="program-header">
                                <span class="program-badge">GENTA MBG</span>
                                <h3 class="program-title">Program Makan Bergizi</h3>
                            </div>
                            <p class="program-desc">
                                Program pangan berbasis kedaulatan pangan lokal dengan standar gizi 
                                untuk masyarakat Banten.
                            </p>
                            <ul class="program-highlights">
                                <li><strong>Target:</strong> 500.000+ siswa SD/MI di Banten</li>
                                <li><strong>Cakupan:</strong> 5.000+ sekolah di 6 kabupaten/kota</li>
                                <li><strong>Menu:</strong> Beras merah, ikan, telur, sayur, buah lokal</li>
                                <li><strong>Manfaat:</strong> Meningkatkan konsentrasi & prestasi belajar</li>
                            </ul>
                        </div>
                    </div>
                    <div class="program-card" style="--program-color: #2E7D32;">
                        <div class="program-image" style="background-image: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=800&auto=format&fit=crop');">
                            <div class="program-overlay"></div>
                        </div>
                        <div class="program-content">
                            <div class="program-header">
                                <span class="program-badge">GENTA Pertanian</span>
                                <h3 class="program-title">Pertanian Berkelanjutan</h3>
                            </div>
                            <p class="program-desc">
                                Pelatihan pertanian organik, teknologi pertanian tepat guna, 
                                dan manajemen lahan untuk petani Banten.
                            </p>
                            <ul class="program-highlights">
                                <li><strong>Target:</strong> 10.000+ petani kecil di Banten</li>
                                <li><strong>Fokus:</strong> Organik, irigasi hemat air, varietas unggul</li>
                                <li><strong>Teknologi:</strong> IoT sensor tanah, drone monitoring, app mobile</li>
                                <li><strong>Hasil:</strong> Produksi naik 30%, biaya turun 20%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Gallery Section --}}
        <section id="galeri" class="section landing-section" style="background: var(--color-surface);">
            <div class="max-w-7xl mx-auto">
                <div class="section-header">
                    <h2 class="section-title">Galeri Kegiatan</h2>
                    <p class="section-subtitle">
                        Dokumentasi kegiatan tim lapangan dan kegiatan sosial GENTA Pangan Banten.
                    </p>
                </div>

                <div class="gallery-grid">
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-[var(--color-text-muted)]">Tim Lapangan GENTA</span>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-[var(--color-text-muted)]">Pelatihan Petani</span>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-[var(--color-text-muted)]">Pertanian Organik</span>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-[var(--color-text-muted)]">Peternakan Lokal</span>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-[var(--color-text-muted)]">UMKM Pangan</span>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-[var(--color-text-muted)]">Kegiatan Sosial</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section id="kontak" class="section landing-section">
            <div class="max-w-7xl mx-auto">
                <div class="section-header">
                    <h2 class="section-title">Hubungi Kami</h2>
                    <p class="section-subtitle">
                        Siap bekerjasama untuk ketahanan pangan Banten? Hubungi kami melalui:
                    </p>
                </div>

                <div class="contact-grid">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <x-heroicon name="location" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-bold mb-2">Kantor Pusat</h3>
                        <p class="text-[var(--color-text-muted)]">{{ settings('office_address') ?? 'Gedung Pemprov Banten, Serang' }}</p>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <x-heroicon name="mail" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-bold mb-2">Email</h3>
                        <p class="text-[var(--color-text-muted)]">info@gentabanten.go.id</p>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <x-heroicon name="phone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-bold mb-2">Telepon</h3>
                        <p class="text-[var(--color-text-muted)]">+62 254 123 456</p>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <x-heroicon name="users" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-bold mb-2">Media Sosial</h3>
                        <p class="text-[var(--color-text-muted)]">@gentabanten</p>
                    </div>
                </div>
            </div>
        </section>

    {{-- Footer --}}
    <footer class="landing-footer landing-section" role="contentinfo">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}" class="w-10 h-10 object-contain">
                    <span class="font-bold text-xl tracking-tight text-[var(--color-secondary)]">{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}</span>
                </div>
            </div>
        </div>

        {{-- Google Maps Location --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <h3 class="text-lg font-semibold text-[var(--color-secondary)] text-center mb-4">Lokasi Kantor Pusat</h3>
            <x-google-map :height="'350px'" :zoom="15" :show-marker="true" :interactive="false" />
        </div>

        {{-- Footer Bottom --}}
        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; {{ date('Y') }} {{ settings('app_name_web') ?? config('app.name') }} &mdash; {{ settings('app_tagline') ?? 'Gerakan Nasional Tani untuk Kemandirian Pangan Banten' }}
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

    {{-- Navbar Scroll Effect & Scroll Reveal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== NAVBAR SCROLL EFFECT =====
            const nav = document.getElementById('landing-nav');
            
            window.addEventListener('scroll', function() {
                const currentScrollY = window.scrollY;
                
                if (currentScrollY > 20) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            }, { passive: true });

            // ===== SCROLL REVEAL FOR SECTIONS (skip hero) =====
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: "0px 0px -10% 0px" });

            // Only observe non-hero sections
            document.querySelectorAll(".landing-section:not(.hero-section)").forEach(section => {
                revealObserver.observe(section);
            });
        });
    </script>
</body>
</html>