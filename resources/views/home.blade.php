<!DOCTYPE html>
<html lang="id" data-theme="genta">
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
</head>
<body class="overflow-x-clip font-sans antialiased">

    {{-- Navigation (komponen bersama dengan halaman auth) --}}
    <x-navbar-guest />

    {{-- Main Content --}}
    <div class="relative z-10 min-h-screen" x-data="revealSections">

        {{-- Hero Section --}}
        <section class="relative flex min-h-screen items-center justify-center px-6">
            <div class="mx-auto max-w-[800px] text-center">
                <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}" class="mx-auto mb-6 h-[120px] w-[120px] animate-fade-up object-contain motion-reduce:animate-none">

                <h1 class="mb-4 animate-fade-up text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.1] font-bold text-[var(--color-secondary)] [animation-delay:100ms] motion-reduce:animate-none">
                    {!! str_replace('Pangan Banten', '<span class="text-[var(--color-primary)]">Pangan Banten</span>', e(settings('app_name_web') ?? 'GENTA Pangan Banten')) !!}
                </h1>

                <p class="mx-auto mb-8 max-w-[600px] animate-fade-up text-[clamp(1.125rem,2.5vw,1.5rem)] leading-relaxed text-[var(--color-text-muted)] [animation-delay:200ms] motion-reduce:animate-none">
                    {{ settings('app_tagline') ?? 'Gerakan Nasional Tani untuk Kemandirian Pangan , ketahanan pangan melalui pertanian, peternakan, perikanan, dan UMKM' }}
                </p>

                <div class="flex animate-fade-up flex-col justify-center gap-4 [animation-delay:300ms] motion-reduce:animate-none sm:flex-row">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex min-w-[200px] items-center justify-center gap-2 rounded-lg bg-[var(--color-primary)] px-[34px] py-4 font-semibold text-white shadow-lg ring-1 ring-[var(--color-primary)]/20 transition-all duration-200 hover:bg-[var(--color-primary-dark)] hover:shadow-2xl focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:outline-none active:scale-95">
                            <x-heroicon name="home" class="h-5 w-5" />
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex min-w-[200px] items-center justify-center gap-2 rounded-lg bg-[var(--color-primary)] px-[34px] py-4 font-semibold text-white shadow-lg ring-1 ring-[var(--color-primary)]/20 transition-all duration-200 hover:bg-[var(--color-primary-dark)] hover:shadow-2xl focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:outline-none active:scale-95">
                            <x-heroicon name="user" class="h-5 w-5" />
                            Masuk
                        </a>
                        <a href="#kontak" class="inline-flex min-w-[200px] items-center justify-center gap-2 rounded-lg border-2 border-[var(--color-border)] bg-[var(--color-surface)] px-[34px] py-4 font-semibold text-[var(--color-text)] shadow-sm transition-all duration-200 hover:border-[var(--color-primary)]/50 hover:bg-[var(--color-surface-hover)] hover:shadow-md focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:outline-none active:scale-95">
                            <x-heroicon name="mail" class="h-5 w-5" />
                            Hubungi Kami
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- About Section --}}
        <section id="tentang" data-reveal class="px-4 py-16 opacity-0 translate-y-8 transition-[opacity,transform] duration-700 ease-out data-[visible]:translate-y-0 data-[visible]:opacity-100 motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none md:px-6 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center">
                    <h2 class="mb-3 text-[clamp(2rem,4vw,2.75rem)] font-bold text-[var(--color-secondary)]">Tentang GENTA Pangan Banten</h2>
                    <p class="mx-auto max-w-[600px] text-lg leading-[1.7] text-[var(--color-text-muted)]">
                        Gerakan Nasional Tani untuk Kemandirian Pangan Banten adalah wadah kolaboratif untuk
                        memacu ketahanan pangan melalui pertanian, peternakan, perikanan, dan UMKM.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)] hover:shadow-[0_8px_30px_rgb(229_0_0_/_0.08)]">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-agri-light text-white">
                            <x-heroicon name="sparkles" class="h-8 w-8" />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-[var(--color-secondary)]">Visi</h3>
                        <p class="text-sm leading-relaxed text-[var(--color-text-muted)]">Terwujudnya ketahanan pangan Banten melalui sinergi petani, nelayan, dan pelaku UMKM lokal.</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)] hover:shadow-[0_8px_30px_rgb(229_0_0_/_0.08)]">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-[var(--color-primary)]">
                            <x-heroicon name="arrow-up-right" class="h-8 w-8" />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-[var(--color-secondary)]">Misi</h3>
                        <p class="text-sm leading-relaxed text-[var(--color-text-muted)]">Memperkuat kapasitas produksi pangan lokal melalui pelatihan, teknologi, dan akses pasar.</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)] hover:shadow-[0_8px_30px_rgb(229_0_0_/_0.08)]">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFF3E0] text-[#F57C00]">
                            <x-heroicon name="shield-check" class="h-8 w-8" />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-[var(--color-secondary)]">Nilai Kami</h3>
                        <p class="text-sm leading-relaxed text-[var(--color-text-muted)]">Kolaborasi, Keberlanjutan, dan Kreativitas sebagai dasar setiap langkah kami.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sectors Section (Bidang Pertanian Utama) --}}
        <section id="sektor" data-reveal class="bg-[var(--color-surface)] px-4 py-16 opacity-0 translate-y-8 transition-[opacity,transform] duration-700 ease-out data-[visible]:translate-y-0 data-[visible]:opacity-100 motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none md:px-6 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center">
                    <h2 class="mb-3 text-[clamp(2rem,4vw,2.75rem)] font-bold text-[var(--color-secondary)]">Bidang Pertanian Utama</h2>
                    <p class="mx-auto max-w-[600px] text-lg leading-[1.7] text-[var(--color-text-muted)]">
                        Empat pilar komoditas tanaman untuk kemandirian pangan ,  selaras semangat GENTA: Gerakan Nasional Tani untuk Kemandirian Pangan.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white transition-all duration-300 before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-(--sector-color) hover:-translate-y-2 hover:shadow-[0_16px_48px_rgb(0_0_0_/_0.1)]" style="--sector-color: #2E7D32;">
                        <div class="relative h-[180px] bg-cover bg-center" style="background-image: url('{{ asset('images/tanaman-pangan.webp') }}');"></div>
                        <div class="p-6 text-center">
                            <h3 class="mb-2 text-lg font-semibold text-[var(--color-secondary)]">Tanaman Pangan</h3>
                            <p class="text-sm leading-relaxed text-[var(--color-text-muted)]">Beras, jagung, kedelai, kacang tanah ,  komoditas utama ketahanan pangan nasional.</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white transition-all duration-300 before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-(--sector-color) hover:-translate-y-2 hover:shadow-[0_16px_48px_rgb(0_0_0_/_0.1)]" style="--sector-color: #43A047;">
                        <div class="relative h-[180px] bg-cover bg-center" style="background-image: url('{{ asset('images/hortikultura.webp') }}');"></div>
                        <div class="p-6 text-center">
                            <h3 class="mb-2 text-lg font-semibold text-[var(--color-secondary)]">Hortikultura</h3>
                            <p class="text-sm leading-relaxed text-[var(--color-text-muted)]">Sayuran, buah-buahan, tanaman obat, dan rempah-rempah bernilai ekonomi tinggi.</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white transition-all duration-300 before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-(--sector-color) hover:-translate-y-2 hover:shadow-[0_16px_48px_rgb(0_0_0_/_0.1)]" style="--sector-color: #2E7D32;">
                        <div class="relative h-[180px] bg-cover bg-center" style="background-image: url('{{ asset('images/perkebunan.webp') }}');"></div>
                        <div class="p-6 text-center">
                            <h3 class="mb-2 text-lg font-semibold text-[var(--color-secondary)]">Perkebunan</h3>
                            <p class="text-sm leading-relaxed text-[var(--color-text-muted)]">Kelapa sawit, kelapa, kakao, kopi, karet ,  komoditas ekspor dan industri hulu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Programs Section --}}
        <section id="program" data-reveal class="px-4 py-16 opacity-0 translate-y-8 transition-[opacity,transform] duration-700 ease-out data-[visible]:translate-y-0 data-[visible]:opacity-100 motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none md:px-6 md:py-20">
            <div class="mx-auto max-w-[1000px]">
                <div class="mb-10 text-center">
                    <h2 class="mb-3 text-[clamp(2rem,4vw,2.75rem)] font-bold text-[var(--color-secondary)]">Program Unggulan GENTA</h2>
                    <p class="mx-auto max-w-[600px] text-lg leading-[1.7] text-[var(--color-text-muted)]">
                        Program-program andalan untuk mewujudkan kemandirian pangan melalui inovasi dan kolaborasi.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white transition-all duration-300 before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-(--program-color) hover:-translate-y-2 hover:shadow-[0_16px_48px_rgb(0_0_0_/_0.1)]" style="--program-color: #E50000;">
                        <div class="relative h-[180px] bg-cover bg-center" style="background-image: url('/images/programs/MBG-2-765x510.webp');">
                            <div class="absolute inset-0 bg-linear-to-b from-black/30 to-black/50"></div>
                        </div>
                        <div class="p-6 text-center">
                            <div class="mb-4 flex items-center justify-center gap-3">
                                <span class="rounded-full bg-(--program-color) px-3.5 py-1.5 text-[0.8125rem] font-semibold text-white">GENTA MBG</span>
                                <h3 class="text-xl font-bold text-[var(--color-secondary)]">Program Makan Bergizi</h3>
                            </div>
                            <p class="mb-4 leading-[1.7] text-[var(--color-text-muted)]">
                                Program pangan berbasis kedaulatan pangan lokal dengan standar gizi
                                untuk masyarakat Banten.
                            </p>
                            <ul class="m-0 list-none divide-y divide-[var(--color-border)] p-0 text-left">
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Target:</strong> 500.000+ siswa SD/MI di Banten</li>
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Cakupan:</strong> 5.000+ sekolah di 6 kabupaten/kota</li>
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Menu:</strong> Beras merah, ikan, telur, sayur, buah lokal</li>
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Manfaat:</strong> Meningkatkan konsentrasi & prestasi belajar</li>
                            </ul>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white transition-all duration-300 before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-(--program-color) hover:-translate-y-2 hover:shadow-[0_16px_48px_rgb(0_0_0_/_0.1)]" style="--program-color: #2E7D32;">
                        <div class="relative h-[180px] bg-cover bg-center" style="background-image: url('{{ asset('images/perkebunan.webp') }}');">
                            <div class="absolute inset-0 bg-linear-to-b from-black/30 to-black/50"></div>
                        </div>
                        <div class="p-6 text-center">
                            <div class="mb-4 flex items-center justify-center gap-3">
                                <span class="rounded-full bg-(--program-color) px-3.5 py-1.5 text-[0.8125rem] font-semibold text-white">GENTA Pertanian</span>
                                <h3 class="text-xl font-bold text-[var(--color-secondary)]">Pertanian Berkelanjutan</h3>
                            </div>
                            <p class="mb-4 leading-[1.7] text-[var(--color-text-muted)]">
                                Pelatihan pertanian organik, teknologi pertanian tepat guna,
                                dan manajemen lahan untuk petani Banten.
                            </p>
                            <ul class="m-0 list-none divide-y divide-[var(--color-border)] p-0 text-left">
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Target:</strong> 10.000+ petani kecil di Banten</li>
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Fokus:</strong> Organik, irigasi hemat air, varietas unggul</li>
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Teknologi:</strong> IoT sensor tanah, drone monitoring, app mobile</li>
                                <li class="py-1.5 text-[0.8125rem] leading-relaxed text-[var(--color-text-muted)]"><strong class="text-[var(--color-text)]">Hasil:</strong> Produksi naik 30%, biaya turun 20%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Gallery Section --}}
        <section id="galeri" data-reveal class="bg-[var(--color-surface)] px-4 py-16 opacity-0 translate-y-8 transition-[opacity,transform] duration-700 ease-out data-[visible]:translate-y-0 data-[visible]:opacity-100 motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none md:px-6 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center">
                    <h2 class="mb-3 text-[clamp(2rem,4vw,2.75rem)] font-bold text-[var(--color-secondary)]">Galeri Kegiatan</h2>
                    <p class="mx-auto max-w-[600px] text-lg leading-[1.7] text-[var(--color-text-muted)]">
                        Dokumentasi kegiatan tim lapangan dan kegiatan sosial GENTA Pangan Banten.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    @foreach (['Tim Lapangan GENTA', 'Pelatihan Petani', 'Pertanian Organik', 'Peternakan Lokal', 'UMKM Pangan', 'Kegiatan Sosial'] as $galleryLabel)
                        <div class="aspect-[4/3] overflow-hidden rounded-xl bg-[var(--color-surface)] transition-all duration-200 hover:scale-[1.015] hover:shadow-[0_8px_30px_rgb(0_0_0_/_0.08)]">
                            <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-[var(--color-text-muted)]">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs">{{ $galleryLabel }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section id="kontak" data-reveal class="px-4 py-16 opacity-0 translate-y-8 transition-[opacity,transform] duration-700 ease-out data-[visible]:translate-y-0 data-[visible]:opacity-100 motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none md:px-6 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center">
                    <h2 class="mb-3 text-[clamp(2rem,4vw,2.75rem)] font-bold text-[var(--color-secondary)]">Hubungi Kami</h2>
                    <p class="mx-auto max-w-[600px] text-lg leading-[1.7] text-[var(--color-text-muted)]">
                        Siap bekerjasama untuk ketahanan pangan Banten? Hubungi kami melalui:
                    </p>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)]">
                        <div class="mx-auto mb-4 flex h-[50px] w-[50px] items-center justify-center rounded-2xl bg-red-50 text-[var(--color-primary)]">
                            <x-heroicon name="location" class="h-5 w-5" />
                        </div>
                        <h3 class="mb-2 text-lg font-bold">Kantor Pusat</h3>
                        <p class="text-[var(--color-text-muted)]">{{ settings('office_address') ?? 'Gedung Pemprov Banten, Serang' }}</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)]">
                        <div class="mx-auto mb-4 flex h-[50px] w-[50px] items-center justify-center rounded-2xl bg-red-50 text-[var(--color-primary)]">
                            <x-heroicon name="mail" class="h-5 w-5" />
                        </div>
                        <h3 class="mb-2 text-lg font-bold">Email</h3>
                        <p class="text-[var(--color-text-muted)]">info@gentabanten.go.id</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)]">
                        <div class="mx-auto mb-4 flex h-[50px] w-[50px] items-center justify-center rounded-2xl bg-red-50 text-[var(--color-primary)]">
                            <x-heroicon name="phone" class="h-5 w-5" />
                        </div>
                        <h3 class="mb-2 text-lg font-bold">Telepon</h3>
                        <p class="text-[var(--color-text-muted)]">+62 254 123 456</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--color-border)] bg-white p-8 text-center transition-all duration-300 hover:-translate-y-1 hover:border-[var(--color-primary)]">
                        <div class="mx-auto mb-4 flex h-[50px] w-[50px] items-center justify-center rounded-2xl bg-red-50 text-[var(--color-primary)]">
                            <x-heroicon name="users" class="h-5 w-5" />
                        </div>
                        <h3 class="mb-2 text-lg font-bold">Media Sosial</h3>
                        <p class="text-[var(--color-text-muted)]">@gentabanten</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer data-reveal role="contentinfo" class="relative border-t border-[var(--color-border)] bg-[var(--color-background)] px-6 pt-12 pb-6 opacity-0 translate-y-8 transition-[opacity,transform] duration-700 ease-out before:absolute before:inset-x-0 before:top-0 before:h-[3px] before:bg-linear-to-r before:from-[var(--color-primary)] before:via-[var(--color-primary-light)] before:to-agri-green data-[visible]:translate-y-0 data-[visible]:opacity-100 motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none">
            <div class="mx-auto flex max-w-7xl justify-center">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('logo.png') }}" alt="{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}" class="h-10 w-10 object-contain">
                        <span class="text-xl font-bold tracking-tight text-[var(--color-secondary)]">{{ settings('app_name_web') ?? 'GENTA Pangan Banten' }}</span>
                    </div>
                </div>
            </div>

            {{-- Peta Lokasi Kantor Pusat --}}
            <div class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
                <h3 class="mb-4 text-center text-lg font-semibold text-[var(--color-secondary)]">Lokasi Kantor Pusat</h3>
                <x-google-map :height="'350px'" :zoom="15" :show-marker="true" :interactive="false" />
            </div>

            {{-- Footer Bottom --}}
            <div class="mx-auto mt-12 flex max-w-7xl flex-wrap items-center justify-between gap-4 border-t border-[var(--color-border)] pt-6">
                <p class="text-[0.8125rem] text-[var(--color-text-muted)]">
                    &copy; {{ date('Y') }} {{ settings('app_name_web') ?? config('app.name') }} &mdash; {{ settings('app_tagline') ?? 'Gerakan Nasional Tani untuk Kemandirian Pangan Banten' }}
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
