# CHANGELOG — GENTA Pangan Banten

Format: [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Sumber kebenaran changelog sejak 2026-11-18 (sebelumnya di `docs/18-changelog.md`).

## [Unreleased] — 2026-11-18

### Removed
- **Absensi dihapus sepenuhnya** (sudah tidak dipakai sejak 2026-11-02): tabel `absensis` (migrasi drop), model, policy, service registrasi, controller, relasi `absensis()` di `User`/`Dapur`, command `DispatchAbsensiReminders` (sebelumnya dijadwalkan daily 06:50), `AbsensiReminderJob`, `AbsensiXlsxExport`. Alarm `CctvAlertJob` diganti query pivot `dapur_user`.
- Kolom `ip_address` dan `hls_url` di `cctvs` di-drop (migrasi `2026_11_18_000001`); `stream_url` kini murni turunan `CCTV_HLS_BASE + stream_key`.
- View `admin/cctvs/monitoring-dashboard.blade.php` (dead code, tidak ada route) dihapus.
- `LaporanController::create()` (tidak ada route) dihapus.
- `monitoringDashboard()` (dead code) dihapus.

### Added
- `manajerial.dashboard`, `manajerial.settings`, `kepala-dapur.dashboard`, `kepala-dapur.settings` views (sebelumnya 500).
- CI GitHub Actions (`.github/workflows/ci.yml`): Pint `--test` + full suite.
- `Dockerfile` + `docker-compose.yml` (app, queue, scheduler, MediaMTX) + `.dockerignore`.
- Rate limiting `throttle:5,1` pada `POST /login` dan `/forgot-password` + tes `LoginRateLimitTest`.
- Indeks `laporans(dapur_id,status)`, `laporans(created_by)`, `laporans(created_at)`, `dapur_user(user_id)` (migrasi `2026_11_18_000002`).
- `$tries=3` + `$backoff=[30,120,300]` pada `CctvHealthCheckJob`; `retry_after` queue 90→300.
- `scripts/backup-db.sh` (pg_dump + retensi 14 hari).
- `LaporanFactory`; dokumentasi lengkap di root (`PRD.md` … `TASKS.md`, `docs/` lama ditandai usang).

### Fixed
- `manajerial.dashboard` route memanggil `Laporan::with('user')` (relasi tidak ada) → `with('createdBy')`.
- `AdminController@dashboard` mengimpor statistik; `admin/dashboard.blade.php` memakai `createdBy` (sebelumnya `->user` yang selalu fallback); tag `</x-admin-page-heading>` yang tidak cocok.
- Toggle buttons tulis (Tambah Dapur/CCTV/Edit) tersembunyi untuk manajerial/kepala-dapur; tombol "Kembali" role-aware; view `admin/dapurs/show`, `admin/cctvs/show`, `admin/dapurs/index`, `admin/cctvs/index` diselaraskan (read-only untuk peran read-only).
- `user_can_login` test dibandingkan via `(string) getKey()`; `DapurTest` menyesuaikan judul halaman.
- Suite penuh: **48/48 lulus** (sebelumnya 5 gagal).

## [2.0.0] — 2026-10-26 · *usang sebagian*
- Layout shell admin/pengawas, password reset, 4 policy, FCM service, geoloc. **Catatan**: fitur Absensi yang disebut di sini sudah dihapus (lihat di atas); stack "Laravel 12/MySQL/Redis" di dokumen lama juga usang — lihat `TRD.md`.

## [1.0.0] — 2026-10-25 · MVP
- Core: auth, peran, dapur, CCTV, laporan, SP, anomaly, mobile API, FCM.
