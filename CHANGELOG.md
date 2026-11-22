# CHANGELOG — GENTA Pangan Banten

Format: [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Sumber kebenaran changelog sejak 2026-11-18 (`docs/` lama dihapus).

## [Unreleased]

### Added
- README: bagian **Menjalankan semua komponen secara lokal (tanpa Docker)** — empat terminal (`serve`, `queue:work`, `schedule:work`, `mediamtx.exe mediamtx.yml`), verifikasi Control API `:9997` dan HLS `:8888`, konfigurasi `.env` lokal, dan catatan Docker sebagai alternatif. Dukungan `Ekspor` dokumen barryvdh/laravel-dompdf dan `intervention/image` sudah tercatat di stack.

### Fixed
- CCTV monitoring: kartu Monitoring CCTV kini memutar HLS selama `stream_key` terdaftar, tidak lagi menunggu `status === 'online'` (status diperbarui tiap 5 menit, jadi sebelumnya layar hitam walau stream sudah mengalir). Token pada `rtsp_url` dimask di halaman detail CCTV agar tidak terekspos. README menerangkan bahwa `CCTV_HLS_BASE` wajib `http://127.0.0.1:8888` untuk preview dari PC (cookie `Secure` MediaMTX) dan HLS via IP LAN butuh HTTPS/WebRTC.
- CCTV health check: MediaMTX kini jadi acuan utama status kamera (`CctvHealthCheckJob`), bukan ffprobe yang bisa timeout — sumber RTSP uji publik membuat ffprobe timeout 8 detik walau MediaMTX tetap `online`, sehingga badge sempat tetap `offline` (dan memicu `CctvAlertJob` palsu). `MediaMtxClient::isReady()` membaca field `online` (bukan `ready`, yang selalu false saat idle karena `sourceOnDemand`) dan mengembalikan `null` bila path belum terdaftar (404) supaya health check jatuh ke ffprobe. **Perubahan ini butuh `php artisan queue:restart`** bila worker queue sedang berjalan.
- README diselaraskan dengan kode: perintah build APK yang menunjuk skrip `build-apk.ps1`/`.bat` (sudah dihapus di 2.1.0) diganti alur Android Studio/Gradle; `CCTV_FFPROBE` dan `FCM_SERVER_KEY` masuk tabel konfigurasi (`FCM_SERVER_KEY` kini juga ada di `.env.example`); status peran `pengawas` dikoreksi sesuai `RestrictPengawasFromWeb` (web hanya dari Capacitor, tanpa CCTV/anomali) dan modul Inspeksi dicatat; deskripsi seeder dan status `sanctum.expiration` (`null`) diperbaiki; workflow `release.yml` disebut tanpa `composer validate` (khusus `ci.yml`).

## [2.1.1] — 2026-11-20 · *perbaikan CCTV + keamanan dependensi*

### Added
- `CctvAnomalyReportTest` (3 tes): akses form anomaly create, preselect `cctv_id` dari query string, non-admin 403.
- Dependabot: ekosistem `github-actions` + `docker` di samping composer/npm, prefix commit `build(deps)`/`build(deps-dev)`, Dependabot alerts + security updates diaktifkan.

### Fixed
- `Dockerfile` base image `php:8.5-cli` — sebelumnya `php:8.3-cli` padahal `composer.lock` butuh PHP >=8.4, sehingga `composer install` dalam image pasti gagal (input untuk #21).
- `docker-compose.yml`: image MediaMTX di-pin `1.21.4` (sebelumnya `latest`, tidak reproducible dan tak bisa di-update Dependabot).
- `composer audit` di `security.yml` sekarang mem-block merge — 10 advisory Composer bersih setelah PHPUnit 13.4.7 (CVE-2026-119193) + update dependensi dev lain (Pest 5.3.7, paratest, php-code-coverage, pail, boost, tinker, pint, collision, vite, laravel-vite-plugin, @tailwindcss/vite, concurrently). Advisory npm/Quill 1.x sudah tertutup lewat upgrade Quill 2.0.2 di bawah.
- Form CCTV admin (`create`/`edit`) dirapikan grid 2 kolom (pola user form), input `w-full`, urutan: Dapur+Status → Nama → Area+No Kamera → RTSP. Form anomaly create dirapikan grid 2 kolom + panel `warning`; form anomaly create kini preselect `cctv_id` dari query string (diabaikan sebelumnya). Dropdown CCTV di form anomaly create diganti combobox Alpine (`cctv-autocomplete.js`): pencarian berdasarkan nama CCTV atau nama dapur, navigasi keyboard (↑↓ Enter Esc). Data CCTV yang dirender hanya `id`/`label`/`dapur_name` (URL RTSP tidak dikirim ke browser). Sidebar admin widened ke `admin.cctvs.*` agar menu Monitoring CCTV aktif di halaman create/edit/show.
- Suite penuh: **58/58 lulus** (sebelumnya 5 gagal, lalu 50/50, lalu 54/55 setelah penambahan tes SP).
- Dependency produksi di-update lewat PR Dependabot yang di-merge: `laravel/sanctum` 4.3.3 → 4.5.0, `daisyui` 5.7.50 → 5.8.1, `actions/checkout|setup-php|cache` versi baru (memperbaiki peringatan Node 20 deprecated di runner), dan **Quill 1.3.4 → 2.0.2**. API yang dipakai `resources/js/pages/admin/laporan-show.js` (lazy `import('quill')`, `{ default: Quill }`, `theme: 'snow'`, toolbar array) tidak berubah di Quill 2 dan bundle sukses memuat `2.0.2`.
- `security.yml`: `npm audit` juga mem-block merge lagi. Quill dihentikan di **2.0.2**, bukan 2.0.3 — karena advisory GHSA-v3m3-f69x-jf25 (`CVE-2025-15056`, XSS fitur HTML export) menyasar tepat versi 2.0.3 dan belum ada patched release, sedangkan 2.0.2 tidak masuk rentang advisory Quill mana pun (`<1.3.7`, `<=1.3.7`, `=2.0.3`). `composer audit` dan `npm audit` bersih nol; upgrade ke 2.0.4 bila upstream merilis patch. Audit XSS output `{!! !!}` di halaman laporan dilanjutkan terpisah di #26.

## [2.1.0] — 2026-11-20 · *rilis CI & dokumentasi*

### Removed
- **Absensi dihapus sepenuhnya** (sudah tidak dipakai sejak 2026-11-02): tabel `absensis` (migrasi drop), model, policy, service registrasi, controller, relasi `absensis()` di `User`/`Dapur`, command `DispatchAbsensiReminders` (sebelumnya dijadwalkan daily 06:50), `AbsensiReminderJob`, `AbsensiXlsxExport`. Alarm `CctvAlertJob` diganti query pivot `dapur_user`.
- Kolom `ip_address` dan `hls_url` di `cctvs` di-drop (migrasi `2026_11_18_000001`); `stream_url` kini murni turunan `CCTV_HLS_BASE + stream_key`.
- View `admin/cctvs/monitoring-dashboard.blade.php` (dead code, tidak ada route) dihapus.
- `LaporanController::create()` (tidak ada route) dihapus.
- `monitoringDashboard()` (dead code) dihapus.
- Direktori `docs/` (20 file) dihapus; isinya usang/salah (Absensi, `ip_address`/`hls_url`, MySQL/Redis, Laravel Cloud) dan digantikan dokumen root.
- 5 route mati dihapus: `admin/manajerial/pengawas.inspeksi.store` (stub redirect ke POST route), `admin/pengawas.inspeksi.checkout` (`authorize('checkOut')` di `LaporanPolicy` tidak ada → selalu 403), beserta method `InspeksiController::store()` dan `checkOut()` serta dependensi `GeolocationService` yang tidak dipakai.

### Added
- `manajerial.dashboard`, `manajerial.settings`, `kepala-dapur.dashboard`, `kepala-dapur.settings` views (sebelumnya 500).
- CI GitHub Actions (`.github/workflows/ci.yml`): Pint `--test` + full suite.
- `Dockerfile` + `docker-compose.yml` (app, queue, scheduler, MediaMTX) + `.dockerignore`.
- Rate limiting `throttle:5,1` pada `POST /login` dan `/forgot-password` + tes `LoginRateLimitTest`.
- Indeks `laporans(dapur_id,status)`, `laporans(created_by)`, `laporans(created_at)`, `dapur_user(user_id)` (migrasi `2026_11_18_000002`).
- `$tries=3` + `$backoff=[30,120,300]` pada `CctvHealthCheckJob`; `retry_after` queue 90→300.
- `scripts/backup-db.sh` (pg_dump + retensi 14 hari).
- `LaporanFactory`; dokumentasi lengkap di root (`PRD.md` … `TASKS.md`).
- `SpWarningTest` (5 tes): akses baca SP untuk manajerial, tombol kelola hanya admin, kepala_dapur 403.
- CI diperluas (`.github/workflows/ci.yml`): `composer validate`, verifikasi build aset (`npm run build`), `concurrency` + `permissions: contents: read`.
- Workflow `security.yml`: `composer audit` + `npm audit` pada push/PR dan mingguan — belum mem-block, advisory dibersihkan di #30.
- Workflow `release.yml`: tag `v*` → tes + build aset → `gh release create --generate-notes`; kategori catatan rilis per label (`.github/release.yml`).
- Dependabot (composer + npm, mingguan), template issue (bug/fitur) + PR, `CODEOWNERS`.
- README: bagian **About**, bagian **Release** (SemVer + Conventional Commits), koreksi stack yang usang (PHP 8.3+, PostgreSQL, tanpa Absensi/Laravel Cloud/MySQL/SQLite), dokumentasi komponen CSS dihapus (sudah pakai daisyUI).

### Fixed
- Fix 500 pada `PUT /pengawas/settings/profile` — closure memanggil `Hash::make` tanpa import (dipindah ke `AuthController::updatePassword()`).
- `routes/web.php` 277→178 baris — deklarasi saja; logika closure dipindah ke `DashboardController` (dashboard per peran), `LaporanController::indexPengawas()`, `AuthController::updatePassword()`. Nama route/URI identik (diverifikasi `route:list` before/after).
- `manajerial.dashboard` route memanggil `Laporan::with('user')` (relasi tidak ada) → `with('createdBy')`.
- `AdminController@dashboard` mengimpor statistik; `admin/dashboard.blade.php` memakai `createdBy` (sebelumnya `->user` yang selalu fallback); tag `</x-admin-page-heading>` yang tidak cocok.
- Toggle buttons tulis (Tambah Dapur/CCTV/Edit) tersembunyi untuk manajerial/kepala-dapur; tombol "Kembali" role-aware; view `admin/dapurs/show`, `admin/cctvs/show`, `admin/dapurs/index`, `admin/cctvs/index` diselaraskan (read-only untuk peran read-only).
- `manajerial.sp-warnings.*` kini bisa diakses manajerial (sebelumnya 100% 403: route `role:manajerial` tapi controller memaksa `role:admin`). Parameter route `{warning}` diselaraskan ke `{sp_warning}` agar implicit binding bekerja. View SP menyembunyikan aksi kelola (buat/ubah status/export) untuk non-admin dan route Kembali/Detail/Detail-button role-aware.
- `user_can_login` test dibandingkan via `(string) getKey()`; `DapurTest` menyesuaikan judul halaman.


## [2.0.0] — 2026-10-26 · *usang sebagian*
- Layout shell admin/pengawas, password reset, 4 policy, FCM service, geoloc. **Catatan**: fitur Absensi yang disebut di sini sudah dihapus (lihat di atas); stack "Laravel 12/MySQL/Redis" di dokumen lama juga usang — lihat `TRD.md`.

## [1.0.0] — 2026-10-25 · MVP
- Core: auth, peran, dapur, CCTV, laporan, SP, anomaly, mobile API, FCM.