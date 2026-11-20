<div align="center">

<img src="public/logo.png" alt="GENTA Pangan Banten" width="120">

# GENTA Pangan Banten

**Sistem Pengawasan Dapur MBG (Makan Bergizi Gratis) — Provinsi Banten**

[![CI](https://github.com/Ridho-arachman/sistem-genta/actions/workflows/ci.yml/badge.svg)](https://github.com/Ridho-arachman/sistem-genta/actions/workflows/ci.yml)
[![Security](https://github.com/Ridho-arachman/sistem-genta/actions/workflows/security.yml/badge.svg)](https://github.com/Ridho-arachman/sistem-genta/actions/workflows/security.yml)

[![PHP](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Pest](https://img.shields.io/badge/Pest-5-8B5CF6?logo=php&logoColor=white)](https://pestphp.com/)
[![Release](https://img.shields.io/github/v/release/Ridho-arachman/sistem-genta?logo=github&label=release)](https://github.com/Ridho-arachman/sistem-genta/releases)
[![License](https://img.shields.io/badge/License-Internal-lightgrey)](#lisensi)

</div>

---

## Daftar Isi

- [About](#about)
- [Ringkasan](#ringkasan)
- [Fitur Utama](#fitur-utama)
- [Peran dan Hak Akses](#peran-dan-hak-akses)
- [Arsitektur](#arsitektur)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Akun Awal](#akun-awal)
- [API Mobile](#api-mobile)
- [Build Aplikasi Mobile](#build-aplikasi-mobile)
- [Pengujian dan Kualitas Kode](#pengujian-dan-kualitas-kode)
- [Keamanan](#keamanan)
- [Deployment](#deployment)
- [Release](#release)
- [Dokumentasi](#dokumentasi)
- [Lisensi](#lisensi)

## About

GENTA Pangan Banten adalah sistem pengawasan operasional program Makan Bergizi Gratis (MBG) di Provinsi Banten. Masalah yang dipecahkan: data dapur, kamera CCTV, laporan pengawas, dan surat peringatan (SP) selama ini terpencar dan tidak bisa dipantau dari satu tempat. GENTA menyatukannya dalam satu aplikasi web internal — pendataan dapur dan CCTV, health check kamera otomatis, alur laporan hingga tindak lanjut SP, serta API untuk aplikasi mobile pengawas.

Sistem dipakai empat peran: `admin` (kelola seluruh data), `manajerial` (pemantauan dan ekspor), `pengawas` (laporan lapangan), dan `kepala_dapur` (status dapur sendiri).

Status saat ini: **pengembangan menuju pilot** — belum dipakai penuh di produksi. Urutan rilis dan sisa pekerjaan dilacak di [TASKS.md](TASKS.md) serta [board Kanban](https://github.com/users/Ridho-arachman/projects/17); riwayat perubahan ada di [CHANGELOG.md](CHANGELOG.md).

## Ringkasan

Aplikasi dibangun di atas Laravel 13 dengan empat panel pengguna yang terpisah sesuai peran: administrator, manajerial, pengawas lapangan, dan kepala dapur.

## Fitur Utama

| Modul | Cakupan |
| --- | --- |
| **Autentikasi** | Login, logout, lupa dan reset password, redirect berbasis peran, rate limiting |
| **Dashboard** | Ringkasan statistik dan aktivitas sesuai peran pengguna |
| **Dapur** | CRUD data dapur, status operasional, progres, geofence, data wilayah |
| **CCTV** | Registrasi perangkat, health check otomatis (ffprobe), status, live preview HLS, monitoring terpusat |
| **Anomali CCTV** | Pelaporan, penanganan, dan ekspor data anomali |
| **Laporan** | Penyusunan laporan pengawas dan tindak lanjut |
| **Surat Peringatan** | Penerbitan SP1–SP3, riwayat, dan ekspor |
| **Manajemen Pengguna** | CRUD pengguna, arsip, pemulihan, hapus permanen, jejak audit |
| **Pengaturan** | Pengaturan aplikasi dan pengelolaan profil, email, serta password |
| **Notifikasi** | Push notification melalui Firebase Cloud Messaging |
| **API Mobile** | Registrasi token FCM, sinkronisasi lokasi, geofence, auto check-in |

## Peran dan Hak Akses

| Modul | `admin` | `manajerial` | `pengawas` | `kepala_dapur` |
| --- | :---: | :---: | :---: | :---: |
| Dashboard | Lihat | Lihat | Lihat | Lihat |
| Dapur | Kelola | Lihat | Lihat | Lihat |
| CCTV | Kelola | Lihat | Lihat | Lihat |
| Anomali CCTV | Kelola | Lihat + ekspor | — | Lihat |
| Laporan | Kelola | — | Buat | — |
| Surat Peringatan | Kelola | Lihat | — | — |
| Manajemen Pengguna | Kelola | — | — | — |
| Pengaturan | Kelola | Lihat | Lihat | Lihat |

Otorisasi diterapkan melalui `RoleMiddleware` pada level rute dan `Policy` pada level model.

## Arsitektur

### Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Backend | Laravel 13, PHP 8.4+, Laravel Sanctum |
| Frontend | Blade, Tailwind CSS 4, daisyUI 5, Alpine.js 3, Leaflet, Vite 8 |
| Database | PostgreSQL (driver `pgsql`) |
| Queue / cache / session | driver `database` |
| Streaming CCTV | MediaMTX (HLS) + health check ffprobe |
| Mobile | Capacitor (Android) |
| Notifikasi | Firebase Cloud Messaging |
| Ekspor Dokumen | barryvdh/laravel-dompdf |
| Pengujian | Pest 5 (58 tes / 153 assertion) |
| Kualitas Kode | Laravel Pint |
| Container | Docker + docker compose (app, queue, scheduler, MediaMTX) |

### Struktur Direktori

```text
app/
├── Console/                  # Perintah Artisan kustom
├── Exports/                  # Kelas ekspor data
├── Http/
│   ├── Controllers/          # Controller web per modul
│   │   ├── Api/              # MobileController
│   │   └── Auth/             # AuthController
│   ├── Middleware/           # RoleMiddleware, RestrictPengawasFromWeb
│   └── Requests/             # Validasi form request
├── Jobs/                     # Pekerjaan antrean
├── Models/                   # Model Eloquent
├── Policies/                 # Kebijakan otorisasi
├── Services/                 # GeolocationService, FcmService
└── helpers.php

resources/views/
├── admin/                    # Panel administrator
├── manajerial/               # Panel manajerial
├── pengawas/                 # Panel pengawas
├── kepala-dapur/             # Panel kepala dapur
├── auth/                     # Halaman autentikasi
├── components/               # Komponen Blade
└── layouts/                  # Layout utama

routes/web.php                # Rute web dan API mobile
database/migrations/          # Skema database
database/seeders/             # Data awal peran, dapur, CCTV, pengaturan
android/                      # Proyek Capacitor (Android)
tests/                        # Tes Pest (Feature & Unit)
```

## Instalasi

### Prasyarat

- PHP 8.4 atau lebih baru (proyek dikembangkan di 8.5) beserta ekstensi standar Laravel
- Composer 2
- Node.js 20 atau lebih baru dan npm
- PostgreSQL untuk database
- Android Studio atau Xcode, hanya jika membangun aplikasi mobile

### Langkah Cepat

```bash
composer run setup
```

Perintah tersebut menjalankan `composer install`, menyiapkan `.env`, membuat application key, menjalankan migrasi, memasang dependensi frontend, dan membangun aset produksi.

### Langkah Manual

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Aplikasi tersedia di `http://localhost:8000`.

### Mode Pengembangan

```bash
composer run dev
```

Perintah ini menjalankan server Laravel, Vite, antrean, dan log secara bersamaan.

**Konfigurasi IP Dev Server (wajib):**

Sebelum menjalankan `composer run dev`, sesuaikan IP di file berikut ke IP Wi-Fi laptop kamu saat ini (contoh: `192.168.101.6`):

1. **`.env`** — ubah `APP_URL`:
   ```env
   APP_URL=http://192.168.101.6:8000
   ```

2. **`vite.config.js`** — ubah `server.origin`:
   ```js
   server: {
       host: '0.0.0.0',
       port: 5173,
       origin: 'http://192.168.101.6:5173',  // sesuaikan IP
       watch: { usePolling: true },
   }
   ```

3. **`public/hot`** — isi manual (file ini di-`.gitignore`, tiap developer punya sendiri):
   ```
   http://192.168.101.6:5173
   ```

Akses aplikasi:

- **Dari PC (browser):** `http://192.168.101.6:8000`
- **Dari Android (browser / WebView Capacitor):** `http://192.168.101.6:8000` — harus satu jaringan Wi-Fi dengan PC.

> Catatan: IP Wi-Fi dari DHCP bisa berubah setiap restart router atau pindah jaringan. Jika CSS/JS tidak terbaca (`net::ERR_FAILED`), perbarui IP di ketiga file di atas dan restart `composer run dev`.

## Konfigurasi

Konfigurasi utama berada di `.env`. Variabel yang perlu diperhatikan:

| Variabel | Keterangan |
| --- | --- |
| `APP_URL` | URL dasar aplikasi |
| `DB_CONNECTION` | Driver database — proyek ini berjalan di PostgreSQL (`pgsql`) |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Driver session, cache, dan antrean (`database`) |
| `FCM_SERVER_KEY` | Server key Firebase Cloud Messaging untuk push notification |
| `CCTV_HLS_BASE` | Base URL HLS media server (kosong = preview kamera tidak tampil) |
| `CCTV_MEDIAMTX_API`, `CCTV_MEDIAMTX_USER`, `CCTV_MEDIAMTX_PASS` | Control API MediaMTX dan kredensialnya (kosong = sinkronisasi path nonaktif) |
| `CCTV_PROBE_TIMEOUT` | Batas waktu probe ffprobe per kamera (detik) |

Nilai sensitif seperti `.env`, keystore, dan cookie lokal tidak disertakan dalam repositori.

## Akun Awal

Setelah menjalankan `php artisan migrate --seed`, tersedia akun berikut:

| Email | Password | Peran |
| --- | --- | --- |
| `admin@test.com` | `password` | Administrator |
| `manajerial@test.com` | `password` | Manajerial |
| `pengawas@test.com` | `password` | Pengawas |
| `kepaladapur@test.com` | `password` | Kepala Dapur |

> Akun di atas hanya untuk pengembangan. Seluruh kredensial wajib diganti sebelum digunakan di lingkungan produksi.

## API Mobile

Endpoint mobile tersedia pada prefix `api/mobile` dan menggunakan autentikasi token Sanctum.

```http
Authorization: Bearer <token>
```

| Method | Endpoint | Deskripsi |
| --- | --- | --- |
| `POST` | `/api/mobile/fcm-token` | Menyimpan token FCM perangkat |
| `POST` | `/api/mobile/sync-locations` | Sinkronisasi titik lokasi |
| `GET` | `/api/mobile/dapurs/geofences` | Mengambil daftar geofence dapur |
| `POST` | `/api/mobile/absensi/auto-checkin` | Check-in otomatis berdasarkan lokasi |

## Build Aplikasi Mobile

```bash
npm run build
npx cap sync android
```

Alternatif melalui skrip yang tersedia di repositori:

```bash
./build-apk.ps1    # PowerShell
build-apk.bat      # Command Prompt
```

APK release ditandatangani menggunakan `gentapangan.keystore` yang tidak disimpan di repositori.

## Pengujian dan Kualitas Kode

```bash
php artisan test --compact
```

Cakupan pengujian mencakup autentikasi dan rate limit login, CCTV (health check, sinkronisasi MediaMTX, anomali), dapur, laporan, dan API mobile. Backend memakai SQLite in-memory dan server MediaMTX dipaksa nonaktif agar tes tetap terisolasi dari jaringan nyata.

Menjalankan formatter:

```bash
vendor/bin/pint --dirty
```

CI (GitHub Actions) menjalankan `composer validate`, Pint, Pest, dan build aset (`npm run build`) pada setiap push dan pull request — lihat [`.github/workflows/ci.yml`](.github/workflows/ci.yml).

## Keamanan

- Otorisasi berbasis peran pada rute dan kebijakan pada level model
- Proteksi CSRF, escaping output Blade, dan pencegahan SQL injection melalui Eloquent
- Rate limiting pada rute autentikasi (`POST /login`, `/forgot-password`); endpoint `/api/mobile/*` masih menunggu throttle — dilacak sebagai issue
- Hashing password dengan bcrypt
- Token API Sanctum dengan masa berlaku terkelola
- Kolom audit dan soft delete untuk manajemen pengguna

Laporkan potensi kerentanan secara internal kepada tim pengelola sistem, bukan melalui issue publik.

## Deployment

- Container Docker (`Dockerfile` + `docker compose`: app, queue, scheduler, MediaMTX)
- Web server nginx + TLS (Let's Encrypt) untuk produksi
- Build aset produksi melalui `npm run build`
- Konfigurasi berbasis environment untuk database, antrean, MediaMTX, dan notifikasi
- Worker antrean (`queue:work`) dan scheduler (`schedule:work`) sebagai container terpisah

Langkah umum di server produksi:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Release

Versi mengikuti [Semantic Versioning](https://semver.org/) `vMAJOR.MINOR.PATCH`. Riwayat rilis tercatat di [halaman Releases](https://github.com/Ridho-arachman/sistem-genta/releases) dan di [CHANGELOG.md](CHANGELOG.md).

Alur rilis:

1. Perbarui [CHANGELOG.md](CHANGELOG.md) — pindahkan isi `Unreleased` ke versi baru (format [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)).
2. Gunakan [Conventional Commits](https://www.conventionalcommits.org/) pada setiap commit: `feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `chore:`.
3. Buat tag lalu push (branch ikut terkirim agar commit rilis ada di remote):
   ```bash
   git tag v2.1.0
   git push origin master --follow-tags
   ```
4. Workflow [`.github/workflows/release.yml`](.github/workflows/release.yml) berjalan otomatis: validasi, Pint, Pest, dan build aset dijalankan ulang, lalu GitHub Release diterbitkan dengan catatan yang di-generate dari commit dan label pull request (kategori diatur di [`.github/release.yml`](.github/release.yml)).

Catatan: paket APK release belum dibuat otomatis oleh CI — build dan penandatanganan masih manual karena keystore (`gentapangan.keystore`) tidak disimpan di repositori.

## Dokumentasi
Penamaan docs utama di root repo: [PRD](PRD.md), [ARCHITECTURE](ARCHITECTURE.md), [TRD](TRD.md), [DATABASE](DATABASE.md), [API](API.md), [TESTING](TESTING.md), [CHANGELOG](CHANGELOG.md), [TASKS](TASKS.md).

## Lisensi

Aplikasi internal GENTA Pangan Banten. Seluruh hak penggunaan diatur oleh organisasi pengelola.
