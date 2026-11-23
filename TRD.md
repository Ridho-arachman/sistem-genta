# TRD — Technical Requirements Document

**Status**: aktif · 2026-11-18

## Runtime
| Komponen | Versi / pilihan | Bukti |
|---|---|---|
| PHP | composer `^8.3`; mesin dev 8.5.12 | `composer.json`, `php -v` |
| Laravel | `^13.17` | `composer.json` |
| Database | PostgreSQL (dev `laravel`) | `.env` |
| Queue / cache / session | driver `database` (tabel `jobs`, `failed_jobs`, `cache`, `sessions`) | `.env`, `config/queue.php` |
| Redis | tidak dipakai | `.env.example` hanya placeholder |
| Web server | `php artisan serve` (dev) → nginx (prod, belum) | `README.md` |
| Bundler frontend | Vite (`laravel-vite-plugin`) + Tailwind v4 + daisyUI 5 | `package.json` |
| Mobile | Capacitor (`@capacitor/geolocation`) | `package.json` |

## Paket penting
`laravel/sanctum` (token mobile), `laravel/boost`, `laravel/tinker`, `laravel/pail` (dev), `pestphp/pest` (dev), `laravel/pint` (dev), `barryvdh/laravel-dompdf`, `intervention/image`. **Tidak ada**: `laravel/pulse`, `telescope`, `reverb`, `horizon`, Excel package apa pun (export pakai writer manual di `app/Exports`).

## API & layanan eksternal
- **MediaMTX v1.21+** — Control API `POST /v3/config/paths/replace/{name}`, `DELETE /v3/config/paths/delete/{name}`; HLS dipublikasikan di `http://<host>:8888`, `sourceOnDemand: yes`.
- **ffprobe** — health check RTSP (nyata, bukan TCP-connect; TCP tidak mendeteksi token kedaluwarsa/401).
- **FCM** — notifikasi mobile (lihat `app/Services/FcmService.php`).
- **Geolocation** — perhitungan geofence (Haversine) berjalan di aplikasi mobile, bukan di server; API mobile hanya menerima lat/long hasil trigger geofence.

## Target infrastruktur (produksi)
Satu server pusat: nginx → php 8.3 + Postgres, queue workers (±10), scheduler, MediaMTX. Dapur: CCTV di belakang NAT dengan port-forward 554 → padatan IPs publik; tanpa VPN untuk produksi. HTTPS wajib (lihat `SECURITY.md`).

## Batas & aturan
- Tidak menyimpan token RTSP asli di doc/repo (`.env` only).
- Health check hanya mengubah status saat transisi `online↔offline`; `maintenance` tidak ditimpa; alert hanya pada transisi.
- `jobs` & `failed_jobs` PK sudah auto-increment (migrasi `2026_11_18_000000_fix_jobs_table_primary_keys.php`).
- Zona waktu: `Asia/Jakarta` (WIB).
