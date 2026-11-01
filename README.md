# GENTA Pangan Banten

Sistem pengawasan dapur MBG (Makan Bergizi Gratis) untuk wilayah Banten. Aplikasi web untuk memantau dapur, CCTV, absensi pengawas berbasis GPS, laporan, dan surat peringatan, dilengkapi API untuk aplikasi mobile (Android/iOS).

## Fitur

| Modul | Keterangan |
|---|---|
| **Landing & Auth** | Halaman publik, login, logout, lupa/reset password, redirect sesuai peran |
| **Dashboard** | Ringkasan statistik per peran: jumlah dapur, CCTV, absensi, laporan |
| **Dapur** | CRUD data dapur, status operasional, progres, geofence (lat/lng/radius), wilayah |
| **CCTV** | Registrasi perangkat, status online/offline/maintenance, live preview (HLS), monitoring |
| **Anomali CCTV** | Pelaporan dan penanganan anomali kamera, export data |
| **Absensi** | Check-in/check-out dengan validasi geofence (Haversine), foto, status hadir/izin/cuti/alpha, export |
| **Laporan** | Laporan pengawas dan tindak lanjut |
| **Surat Peringatan** | SP1–SP3, riwayat dan export |
| **Manajemen Pengguna** | CRUD user, arsip (soft delete), restore/force delete, audit kolom |
| **Pengaturan** | Pengaturan aplikasi (admin) dan profil/email/password (semua peran) |
| **API Mobile** | Token FCM, sinkronisasi lokasi, geofence dapur, auto check-in (Sanctum) |

## Peran Pengguna

| Peran | Akses |
|---|---|
| `admin` | Semua modul, manajemen pengguna, pengaturan sistem |
| `manajerial` | Dashboard, dapur, CCTV, anomali, SP, absensi (read/export), pengaturan |
| `pengawas` | Dashboard, dapur, CCTV, absensi, laporan, profil |
| `kepala_dapur` | Dashboard, dapur, CCTV, anomali, SP terkait dapurnya |

## Teknologi

- **Backend**: Laravel 13, PHP 8.5, Laravel Sanctum (API), barryvdh/laravel-dompdf (export PDF)
- **Frontend**: Blade, Tailwind CSS 4, Alpine.js 3, Leaflet (peta), Vite 8
- **Database**: SQLite (default), MySQL/PostgreSQL didukung
- **Mobile**: Capacitor (Android & iOS), FCM untuk notifikasi
- **Testing**: Pest 5
- **Tooling**: Laravel Pint, Laravel Boost

## Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/        # Absensi, Admin, Cctv, Dapur, Laporan, SpWarning, ...
│   │   ├── Api/            # MobileController (API mobile)
│   │   └── Auth/           # AuthController
│   ├── Middleware/         # RoleMiddleware, RestrictPengawasFromWeb
│   └── Requests/
├── Models/                 # User, Dapur, Cctv, Absensi, Laporan, SpWarning, CctvAnomalyReport, Settings, FcmToken
├── Policies/               # Otorisasi per model
├── Services/               # Haversine/geofence, notifikasi FCM
└── helpers.php

resources/views/
├── admin/  manajerial/  pengawas/  kepala-dapur/   # Panel per peran
├── auth/   components/   layouts/
└── welcome.blade.php

routes/web.php              # Web + route API mobile (`api/mobile`, Sanctum)
database/migrations/        # Skema database
database/seeders/           # RoleSeeder, DapurSeeder, CctvSeeder, SettingsSeeder
android/  ios/              # Proyek Capacitor
docs/                       # Dokumentasi teknis per modul
```

## Persyaratan

- PHP 8.3+ (disarankan 8.5) dengan ekstensi standar Laravel
- Composer
- Node.js 20+ dan npm
- Android Studio / Xcode (hanya jika membangun aplikasi mobile)

## Instalasi

```bash
composer run setup
```

Perintah tersebut menjalankan `composer install`, menyalin `.env`, generate key, migrasi database, `npm install`, dan `npm run build`.

Jika ingin langkah manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Untuk pengembangan dengan hot reload:

```bash
composer run dev
```

## Akun Demo (setelah `php artisan migrate --seed`)

| Email | Password | Peran |
|---|---|---|
| `admin@test.com` | `password` | admin |
| `pengawas@test.com` | `password` | pengawas |
| `manajerial@test.com` | `password` | manajerial |
| `kepaladapur@test.com` | `password` | kepala_dapur |

> Ganti seluruh akun dan password demo sebelum dipakai di produksi.

## API Mobile

Semua endpoint berada di prefix `api/mobile` dan memerlukan token Sanctum (`Authorization: Bearer <token>`):

| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/api/mobile/fcm-token` | Simpan token FCM perangkat |
| POST | `/api/mobile/sync-locations` | Sinkronisasi titik lokasi |
| GET | `/api/mobile/dapurs/geofences` | Ambil geofence dapur |
| POST | `/api/mobile/absensi/auto-checkin` | Check-in otomatis berdasarkan lokasi |

## Build Aplikasi Android

```bash
npm run build
npx cap sync android
```

Atau gunakan skrip yang tersedia:

```bash
./build-apk.ps1      # PowerShell
build-apk.bat        # CMD
```

APK release ditandatangani dengan `gentapangan.keystore` (tidak disimpan di repositori).

## Testing

```bash
php artisan test --compact
```

Cakupan saat ini: autentikasi, dashboard, dapur, CCTV, absensi, dan API mobile.

## Kode Gaya

```bash
vendor/bin/pint --dirty
```

## Dokumentasi

Penjelasan detail tiap modul ada di [`docs/`](docs/), mulai dari [`docs/01-project-overview.md`](docs/01-project-overview.md).

## Lisensi

Aplikasi internal GENTA Pangan Banten.
