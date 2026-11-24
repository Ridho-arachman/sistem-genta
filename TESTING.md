# TESTING — Strategi & status tes

**Status**: aktif · 2026-11-23 · suite harus lulus penuh (angka tes mengikuti keluaran `vendor/bin/pest`)

## Stack
Pest v5 + phpunit v13 (region PHP 8.3/8.5), `sqlite :memory:` (`phpunit.xml`), `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `BCRYPT_ROUNDS=4`. `CCTV_HLS_BASE` & `CCTV_MEDIAMTX_API` dipaksa kosong (test hermetic; tidak menyentuh network nyata).

## Struktur
```
tests/Feature/    AuthTest, LoginRateLimitTest, CctvTest, CctvHealthCheckTest,
                 MediaMtxClientTest, DapurTest, LaporanTest, ExampleTest,
                 CctvAnomalyReportTest, SpWarningTest, PengawasSettingsTest,
                 RoleReadOnlyPageTest, MobileApiTest, InspeksiExportTest,
                 AdminDashboardTest, UserSoftDeleteTest, NoLazyLoadingTest
tests/Unit/       ExampleTest
tests/Pest.php, tests/TestCase.php
```

## Menjalankan
```
vendor/bin/pest                 # semua
vendor/bin/pest tests/Feature/CctvTest.php
php artisan test --compact      # lewat artisan
vendor/bin/pint --dirty --format agent   # wajib setelah ubah PHP
```

## Yang ditest
- Cctv CRUD, sync MediaMTX path (mock), stream_url derivation (kosong bila `CCTV_HLS_BASE` kosong).
- Health check job: dispatch `cctv:health-check` mengisi `last_checked_at`; probe sukses/gagal; transisi status; alert hanya saat transisi ke offline.
- Auth: login, logout, wrong password, akun soft-deleted, pengawas web-block, manajerial/kepala-dapur dashboard ok, rate limit 429 setelah 5 salah.
- Policy: `CctvAnomalyReportPolicy` (viewAny/view/create/update/export per role) dan `LaporanPolicy`; akses dapur/CCTV per user lewat `User::canAccessDapur()` di controller (bukan policy).
- Halaman dapur create/edit render (config `banten`), dan pagination view list dapur.
- Halaman role read-only (manajerial/kepala-dapur) memakai view admin yang sama: link/aksi harus ber-prefix rute aktif, bukan `admin.*` (`RoleReadOnlyPageTest`).
- Laporan: validasi, submit, list, ekspor.

## Yang TIDAK ditest (jujur)
- Halaman error custom 404/500 (belum dibuat).
- Integrasi FCM nyata (selalu di-mock).
- UI JS (tanpa browser/Playwright).
- Geofence Hriversine eksak jarak (unit test sebaiknya ditambah — lihat `TASKS.md`).
- N+1 query count (belum ada tes `DB::assertQueryCount`).

## Riwayat perbaikan penting
2026-11-18: view `manajerial.dashboard`, `kepala-dapur.dashboard`, `manajerial/kepala-dapur.settings` dibuat → 3 tes gagal hilang; `LaporanFactory` dibuat → `LaporanTest` hijau; `DapurTest` diselaraskan (`Data Dapur`); `user_can_login` dibandingkan lewat `(string) getKey()`; `AdminController::dashboard` relation `user`→`createdBy` diperbaiki.

## CI
GitHub Actions `.github/workflows/ci.yml`: Pint `--test` + full suite.
