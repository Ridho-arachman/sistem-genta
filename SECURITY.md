# SECURITY — Keamanan

**Status**: aktif · 2026-11-18

## Autentikasi & sesi
- Session (web) + Sanctum (mobile). `BCRYPT_ROUNDS=12`. Session driver `database`.
- **Rate limiting**: `POST /login` dan `POST /forgot-password` → `throttle:5,1` (429 setelah 5/menit). Counter per IP saat belum autentikasi, per user setelah login — kredensial salah tetap per IP, jadi 429 bisa dipakai untuk brute-force protection.
- Password reset token standar Laravel (`password_reset_tokens`).
- Soft delete user; `Auth::attempt` menolak `trashed()` dengan pesan "Akun dinonaktifkan".

## Otorisasi
Middleware `role:<admin|manajerial|pengawas|kepala_dapur>` (`RoleMiddleware`) + `canAccessDapur()` (untuk detail dapur/CCTV). Policy: `LaporanPolicy`, `CctvAnomalyReportPolicy`. Tidak ada auto-superadmin: admin tetap harus melewati route yang didaftarkan.

## Data & input
- Eloquent (parameterisasi) untuk semua query. Raw query hanya di health check job (durasi) dan debug sesekali.
- File upload: foto inspeksi/laporan disimpan ke disk privat lalu `selfie_photo` hanya menyimpan path-nya (fallback data URL lama).
- XSS: Blade escape literal; rich text Quill — output dirender dengan `{!! !!}`? **Periksa jika perlu mengizinkan HTML** (belum dikonfirmasi; jangan ubah tanpa audit).
- Mass assignment: `#[Fillable]` explicit + `guarded` default.

## CCTV & jaringan
- Port 554 per dapur di-NAT ke IP publik; isi RTSP URL admin (dengan token bila ada). **Token RTSP tidak disimpan di doc/repo.** `CCTV_MEDIAMTX_USER/PASS` kosong berarti API publik — set auth di produksi.
- HLS preview di-embed ke browser: gunakan `CCTV_HLS_BASE` internal/VPN bila memungkinkan; HLS `hlsAllowOrigins: ['*']` (dilebarkan agar embed bisa lintas domain) — persempit di produksi bila perlu.
- SSRF: fitur fetch URL bebas belum ada; IP allowlist privat ditunda (`docs` keputusan lama) karena akan memutus testing RTSP lokal. **Jangan tambah endpoint "fetch URL" bebas tanpa allowlist.**

## Web & HTTP
- HTTPS (TLS) belum terpasang di domain produksi (lihat `TASKS.md` Tier 1).
- Nginx `security headers` belum diverifikasi (HSTS, X-Content-Type-Options, Referrer-Policy).
- `APP_DEBUG=true` **hanya** di dev. Produksi: `false` + `php artisan config:cache`.

## Dependensi & secret
- `.env` gitignored. `.env.example` tidak berisi nilai rahasia.
- `composer audit` / `npm audit` dianjurkan sebelum rilis (belum terjadwal di CI).
- Personal Access Token Sanctum di-store hashed.

## Incident (ringkas)
Kamera offline massal → cek `failed_jobs`, log, MediaMTX; bocor token CCTV → ganti token di DB + MediaMTX path; butuh emergency: disable publik dengan menonaktifkan HLS base kosong.
