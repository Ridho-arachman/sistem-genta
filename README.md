<div align="center">

<img src="public/logo.png" alt="GENTA Pangan Banten" width="120">

# GENTA Pangan Banten

**Sistem Pengawasan Dapur MBG (Makan Bergizi Gratis) — Provinsi Banten**

[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Pest](https://img.shields.io/badge/Pest-5-8B5CF6?logo=php&logoColor=white)](https://pestphp.com/)
[![License](https://img.shields.io/badge/License-Internal-lightgrey)](#lisensi)

</div>

---

## Daftar Isi

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
- [Dokumentasi](#dokumentasi)
- [Lisensi](#lisensi)

## Ringkasan

GENTA Pangan Banten adalah aplikasi web internal untuk pengawasan operasional dapur MBG. Sistem menangani pendataan dapur, pemantauan CCTV, absensi pengawas berbasis lokasi, pelaporan, dan surat peringatan, serta menyediakan API untuk aplikasi mobile Android dan iOS.

Aplikasi dibangun di atas Laravel 13 dengan empat panel pengguna yang terpisah sesuai peran: administrator, manajerial, pengawas lapangan, dan kepala dapur.

## Fitur Utama

| Modul | Cakupan |
| --- | --- |
| **Autentikasi** | Login, logout, lupa dan reset password, redirect berbasis peran, rate limiting |
| **Dashboard** | Ringkasan statistik dan aktivitas sesuai peran pengguna |
| **Dapur** | CRUD data dapur, status operasional, progres, geofence, data wilayah |
| **CCTV** | Registrasi perangkat, status perangkat, live preview HLS, monitoring terpusat |
| **Anomali CCTV** | Pelaporan, penanganan, dan ekspor data anomali |
| **Absensi** | Check-in/check-out dengan validasi geofence, foto, rekap dan ekspor |
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
| Absensi | Kelola + ekspor | Check-in + ekspor | Check-in/out | — |
| Laporan | Kelola | — | Buat | — |
| Surat Peringatan | Kelola | Lihat | — | — |
| Manajemen Pengguna | Kelola | — | — | — |
| Pengaturan | Kelola | Lihat | Lihat | Lihat |

Otorisasi diterapkan melalui `RoleMiddleware` pada level rute dan `Policy` pada level model.

## Arsitektur

### Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Backend | Laravel 13, PHP 8.5, Laravel Sanctum |
| Frontend | Blade, Tailwind CSS 4, Alpine.js 3, Leaflet, Vite 8 |
| Database | SQLite (default), MySQL, PostgreSQL |
| Mobile | Capacitor (Android dan iOS) |
| Notifikasi | Firebase Cloud Messaging |
| Ekspor Dokumen | barryvdh/laravel-dompdf |
| Pengujian | Pest 5 |
| Kualitas Kode | Laravel Pint |

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
android/  ios/                # Proyek Capacitor
docs/                         # Dokumentasi teknis per modul
```

## Instalasi

### Prasyarat

- PHP 8.3 atau lebih baru (disarankan 8.5) beserta ekstensi standar Laravel
- Composer 2
- Node.js 20 dan npm
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
| `DB_CONNECTION` | Driver database (`sqlite`, `mysql`, `pgsql`) |
| `FCM_SERVER_KEY` | Server key Firebase Cloud Messaging untuk push notification |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Driver session, cache, dan antrean |

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

Cakupan pengujian mencakup autentikasi, dashboard, dapur, CCTV, absensi, dan API mobile.

Menjalankan formatter:

```bash
vendor/bin/pint --dirty
```

## Keamanan

- Otorisasi berbasis peran pada rute dan kebijakan pada level model
- Proteksi CSRF, escaping output Blade, dan pencegahan SQL injection melalui Eloquent
- Rate limiting pada rute autentikasi
- Hashing password dengan bcrypt
- Token API Sanctum dengan masa berlaku terkelola
- Kolom audit dan soft delete untuk manajemen pengguna

Laporkan potensi kerentanan secara internal kepada tim pengelola sistem, bukan melalui issue publik.

## Deployment

- Kompatibel dengan Laravel Cloud
- Mendukung container Docker
- Build aset produksi melalui `npm run build`
- Konfigurasi berbasis environment untuk database, antrean, dan notifikasi

Langkah umum di server produksi:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Dokumentasi

Dokumentasi teknis per modul tersedia di direktori [`docs/`](docs/), dimulai dari [Project Overview](docs/01-project-overview.md).

## Dokumentasi Komponen CSS (`resources/css/app.css`)

Komponen CSS di `resources/css/app.css` menggunakan Tailwind CSS v4 dengan `@layer components`. Komponen-komponen berikut tersedia untuk digunakan di semua panel (admin, manajerial, pengawas, kepala dapur) agar tampilan konsisten tanpa perlu membuat class baru dari nol.

### Warna (CSS Custom Properties)

Semua warna didefinisikan sebagai CSS variable dan dapat digunakan langsung di class atau inline style:

| Variabel | Nilai | Keterangan |
|----------|-------|------------|
| `--color-primary` | `#E50000` | Warna utama merah |
| `--color-primary-dark` | `#B20000` | Hover state |
| `--color-primary-light` | `#FF3333` | |
| `--color-secondary` | `#000000` | Judul teks |
| `--color-background` | `#FFFFFF` | Background halaman |
| `--color-surface` | `#F5F5F5` | Background card/panel |
| `--color-border` | `#E0E0E0` | Border |
| `--color-text` | `#000000` | Teks utama |
| `--color-text-muted` | `#666666` | Teks sekunder |

### Tombol (Buttons)

| Class | Keterangan | Contoh |
|-------|------------|--------|
| `.btn-primary` | Tombol utama warna merah | `<button class="btn-primary">Simpan</button>` |
| `.btn-secondary` | Tombol abu-abu dengan border | `<button class="btn-secondary">Batal</button>` |
| `.btn-danger` | Tombol merah untuk aksi menghapus | |
| `.btn-outline-primary` | Outline merah | |
| `.btn-light` | Putih dengan border | |
| `.btn-action` | Tombol aksi pada halaman (lebih lebar) | `.btn-action.btn-action-primary` |
| `.btn-action-primary` | Tombol aksi warna utama | |
| `.btn-action-secondary` | Tombol aksi warna abu-abu | |
| `.btn-action-ghost` | Tombol aksi tanpa background | |

### Form Input

| Class | Keterangan | Contoh |
|-------|------------|--------|
| `.input-field` | Input standar (rounded-xl, focus:ring-primary) | `<input class="input-field">` |
| `.input-field.select-enhanced` | Select dengan ikon panah di kanan | |
| `.input-with-icon` | Wrapper input dengan ikon di kiri | |
| `.input-icon-inner` | Ikon di dalam input | |
| `.form-field` | Wrapper label + input (flex column) | `<div class="form-field">...</div>` |
| `.form-label` | Label form | |
| `.form-label .required` | Bintang merah untuk field wajib | |
| `.form-hint` | Teks petunjuk kecil di bawah input | |
| `.form-error` | Pesan error | |
| `.input-field-error` | State input error (border merah) | |

### Card & Section

| Class | Keterangan | Contoh |
|-------|------------|--------|
| `.card` | Card standar (putih, border, rounded-xl, shadow-sm) | `<div class="card">...</div>` |
| `.panel` | Sama seperti card | |
| `.form-section` | Section form dengan padding 24px | |
| `.form-section-title` | Judul section form | |
| `.section-icon` | Ikon kecil di judul section | |

### Tabel

| Class | Keterangan |
|-------|------------|
| `.table-th` | Header tabel (text-xs, uppercase, bg-surface) |
| `.table-td` | Sel data tabel (text-sm) |

### Badge & Status

| Class | Keterangan | Contoh |
|-------|------------|--------|
| `.badge` | Badge dasar (rounded-full, text-xs) | `<span class="badge badge-primary">...</span>` |
| `.badge-primary` | Badge warna utama | |
| `.badge-success` | Badge warna hijau | |
| `.badge-warning` | Badge warna kuning | |
| `.badge-danger` | Badge warna merah tua | |
| `.badge-gray` | Badge warna abu-abu | |
| `.status-ok` | Status aktif (hijau muda) | |
| `.status-warn` | Status peringatan (kuning muda) | |
| `.status-off` | Status tidak aktif (merah muda) | |

### Layout Shell

| Class | Keterangan |
|-------|------------|
| `.shell` | Wrapper layout utama (flex, min-height layar penuh) |
| `.topbar` | Atas halaman (72px, warna utama merah) |
| `.aside` | Sidebar kiri (230px, posisi fixed) |
| `.main` | Konten utama (margin-left: 230px untuk ruang sidebar) |
| `.heading` | Header halaman dengan judul dan tombol aksi |
| `.eyebrow` | Teks kecil di atas judul utama |
| `.main h1` | Judul halaman utama (font Space Grotesk) |
| `.muted` | Teks abu-abu untuk keterangan |

### Statistik

| Class | Keterangan |
|-------|------------|
| `.stats` | Grid statistik (responsif: 4 kolom desktop, 2 tablet, 1 mobile) |
| `.stat` | Satu kartu statistik |
| `.stat-value` | Angka besar (font Space Grotesk) |
| `.stat-icon` | Ikon di dalam kartu statistik |
| `.stat-foot` | Keterangan kecil di bawah angka |

### Komponen Lainnya

| Class | Keterangan |
|-------|------------|
| `.search` | Input pencarian (max-width 280px, focus:ring-primary) |
| `.kitchen-card` | Kartu dapur dengan flex layout |
| `.progress-bar` / `.progress-fill` | Progress bar |
| `.progress-ok` / `.progress-warn` / `.progress-off` | Warna progres |
| `.activity` / `.activity-row` | Feed aktivitas |
| `.camera` / `.camera-art` | Panel kamera |
| `.footer` | Footer halaman (padding 16px, border atas) |

### Transisi & Animasi

| Class | Keterangan |
|-------|------------|
| `.transition-scale` | Transisi transform + opacity |
| `.transition-slide` | Transisi transform |
| `.animate-scale-in` | Animasi skala masuk |
| `.spinner` | Ikon loading berputar |

### Contoh Penggunaan Lengkap

```blade
@extends('admin.layouts.app', ['pageTitle' => 'Halaman Baru'])

@section('page_content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="heading">
        <div>
            <div class="eyebrow">Genta · Panel Baru</div>
            <h1>Judul Halaman</h1>
            <div class="muted">Keterangan halaman</div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <span class="section-icon">⌂</span>
            Judul Section
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-field">
                <label class="form-label" for="nama">Nama <span class="required">*</span></label>
                <input type="text" id="nama" name="nama" class="input-field" required>
            </div>
            <div class="form-field">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="input-field select-enhanced">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-actions flex justify-end gap-3 mt-4">
        <a href="{{ route('admin.index') }}" class="btn-action btn-action-secondary">Batal</a>
        <button type="submit" class="btn-action btn-action-primary">Simpan</button>
    </div>
</div>
@endsection
```

## Lisensi

Aplikasi internal GENTA Pangan Banten. Seluruh hak penggunaan diatur oleh organisasi pengelola.
