# CHANGELOG — GENTA Pangan Banten

Format: [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Sumber kebenaran changelog sejak 2026-11-18 (`docs/` lama dihapus).

## [Unreleased]

### Added
- Rate limit bawaan Laravel (cache `database`, tanpa Redis): 3 named limiter di `AppServiceProvider` — `auth` (5/menit IP + 30/menit email untuk guest, 5/menit per user untuk yang login) pada endpoint kredensial, `mobile` (30/menit per user) pada `/api/mobile/*`, dan `web` (60/menit per user per rute) pada semua rute terproteksi. Tes regresi: `LoginRateLimitTest` dan `MobileApiTest`.
- Tes regresi untuk perbaikan di bawah: `MobileApiTest` (check-in hanya dapur assignment + geofence berulang tak membuat laporan ganda + simpan FCM token), `InspeksiExportTest` (XLSX admin memuat semua laporan, XLSX berskope `createdBy` hanya memuat milik sendiri), `AdminDashboardTest` (progress/status dapur benar), `UserSoftDeleteTest` (restore mempertahankan assignment + statistik tak menghitung user terhapus), dan satu tes di `LaporanTest` (foto dengan ekstensi non-gambar disimpan sebagai `.jpg`).
- `NoLazyLoadingTest` (audit N+1 formal): 17 halaman daftar/dashboard dijalankan dengan `Model::preventLazyLoading()` — hijau sekarang; kalau ada view yang membaca relasi tak di-eager load, tes ini gagal alih-alih menambah query per baris.
- Tes untuk `cctv:health-check` (perintahnya, bukan cuma job): memastikan satu job per dapur bersangkutan (dapur tanpa kamera dilewati) dan tiap batch mencatat durasinya di log `cctv:health-check batch`.
- README: bagian **Menjalankan semua komponen secara lokal (tanpa Docker)** — empat terminal (`serve`, `queue:work`, `schedule:work`, `mediamtx.exe mediamtx.yml`), verifikasi Control API `:9997` dan HLS `:8888`, konfigurasi `.env` lokal, dan catatan Docker sebagai alternatif.
- Pemutar CCTV WebRTC/WHEP (commit fitur sebelumnya belum masuk changelog): kartu Monitoring CCTV dan halaman detail membuka sesi WHEP (`CCTV_WEBRTC_BASE/{stream_key}/whep`) lebih dulu, lalu fallback ke HLS (hls.js dimuat lazy). Badge kartu jadi `connecting` selama video belum mulai, supaya tidak muncul jeda `offline` setelah kamera disimpan. Env baru `CCTV_WEBRTC_BASE` dan `CCTV_MEDIAMTX_RTSP` didokumentasikan di `.env.example`, `README.md`, dan `ENV.md`.

### Changed
- IP dev server: `vite.config.js` membaca `APP_URL` (`.env`) untuk `server.origin`, sehingga ganti Wi-Fi cukup satu tempat (`.env`) — sebelumnya `server.origin` ditulis tangan lagi dengan IP yang sama.
- SP kini punya policy (`SpWarningPolicy`) dan `SpWarningController` memakai `$this->authorize(...)` di semua aksi, mengikuti pola `CctvAnomalyReportPolicy` (kelola = admin, lihat = admin/manajerial).
- `App\Http\Controllers\Controller` memakai trait `AuthorizesRequests`, jadi empat controller (CCTV, CCTV anomali, Dapur, Inspeksi) tidak perlu mendeklarasikan trait yang sama lagi.
- `.env.example` mencatat `SANCTUM_STATEFUL_DOMAINS` dan `SANCTUM_TOKEN_PREFIX` (kosong default; mobile sudah pakai token Bearer).
- Komponen Blade pengawas vs admin (`status-badge`, `page-heading`) sengaja **tidak** digabung: keduanya beda props dan kelas (badge status laporan `diperiksa/temuan/...` vs badge `ok/warn/off` kamera & dapur), jadi menggabungkan akan menambah props kondisional, bukan mengurangi kode.

### Fixed
- README salah soal `public/hot`: file itu di-generate plugin Laravel saat dev server Vite mulai (isinya `server.origin`), bukan diisi manual. Konsekuensinya `public/fonts-manifest.dev.json` juga bacaan IP-nya dari `public/hot` — kalau `public/hot` masih sisa run lama, manifest ikut memakai IP lama dan menyebabkan `net::ERR_FAILED` saat load font; cara memperbaiki kini tertulis (hapus kedua file, jalankan ulang `npm run dev`).
- Hint form Admin → Tambah User kini menyebut "Minimal 8 karakter" saja, sesuai `Password::defaults()` yang hanya `min:8` — sebelumnya mengharuskan "kombinasikan huruf & angka" yang tidak dicek validasi.
- `DATABASE.md` mengikuti konsolidasi migrasi: tidak lagi menyebut jumlah file (bisa berubah) dan rujukan tabel `sessions` menunjuk `0001_01_01_000001_create_users_table.php`, bukan nama lama yang sudah tidak ada.
- Aturan password sekali jalan: reset password dan ganti password profil kini memakai `Password::defaults()` (seperti `storeUser`), bukan `min:8`/`confirmed` tulis tangan per metode — `AuthController::reset` dan `AuthController::updatePassword`.
- Pesan validasi mati di `AdminController::storeUser` dibuang (pesan `dapur_id.required`/`dapur_ids.*` sudah disediakan di blok validasi khusus role; di blok pertama yang nullable ia tak pernah memicu).
- PWA/service worker: precache tidak lagi memuat nama aset ber-hash (`app-*.css/js`) yang berubah setiap `npm run build` — satu URL 404 membuat `cache.addAll` di event `install` reject sehingga service worker gagal install dan mode offline/cache tidak pernah aktif (`public/sw.js`). Precache kini hanya URL stabil (`/`, `/manifest.json`) dengan `CACHE_NAME` dinaikkan ke `gentapangan-v4`; aset ber-hash tetap di-cache on-demand oleh handler fetch yang sudah ada.
- Dokumen tidak lagi menunjuk file yang sudah dihapus: `DATABASE.md` (schema dump yang dihapus saat migrasi dikonsolidasikan jadi 13 file), `PERFORMANCE.md` dan `TRD.md` (nama migrasi `2026_11_18_000002` / `2026_11_18_000000_fix_jobs_table_primary_keys.php`) — efek skema yang sama dipertahankan, nama filenya tidak.
- Keamanan upload foto laporan: ekstensi file kini dari allowlist gambar (`jpg/jpeg/png/webp`), selain itu disimpan sebagai `jpg` — sebelumnya ekstensi diambil dari data-URL yang dikirim klien, jadi `data:image/php;base64,...` bisa tersimpan sebagai file `.php` di `storage/app/public` (tertaut ke web via `public/storage`) dan dieksekusi bila URL-nya dibuka (`LaporanController::store`).
- Ekspor XLSX laporan: ekspor sekarang menerima batas akses (`createdBy`) sama seperti CSV — `LaporanXlsxExport` mengekspor seluruh laporan tanpa memfilter pengawas, sehingga cabang XLSX tidak sejalan dengan cabang CSV (`InspeksiController::export`).
- API mobile `absensi/auto-checkin`: pengawas hanya bisa check-in ke dapur yang ditugaskan (`canAccessDapur`, 403 bila bukan) — sebelumnya hanya divalidasi `exists:dapurs,id`, berbeda dari form web `pengawas.laporan.store` yang sudah membatasi assignment.
- Dashboard admin "Status Dapur": progress dan status kamera kini benar — view membaca `$dapur->progress`/`$dapur->status` yang tidak ada pada model, jadi semua dapur tampil 0% dengan progress bar merah dan label badge kosong. Aturan kini jadi accessor `Dapur::progress`/`Dapur::status` (dipakai dashboard dan halaman Data Dapur, duplikasi inline dihapus).
- Statistik peran di Dashboard admin/manajerial tidak lagi menghitung user soft-deleted (`active()` ditambahkan pada hitungan `total_pengawas`/`total_manajerial`/`total_kepala_dapur`/`pengawas_without_dapur`).
- Restore user: pengawas tidak lagi kehilangan assignment dapur setelah soft delete → pivot `dapur_user` kini hanya dilepas saat hard delete (sebelumnya dilepas pada soft delete, sehingga setelah restore pengawas kehilangan semua akses dapur).
- SP hanya bisa diterbitkan untuk pengawas: validasi `user_id` dibatasi `role = pengawas` — sebelumnya admin bisa menerbitkan SP untuk akun admin/manajerial lain (`SpWarningController::store`).
- Halaman "Alamat server" aplikasi mobile (`public/index.html`) tidak lagi menyuruh menjalankan `switch-mode.bat mobile` yang tidak ada di repo; teks diganti penjelasan IP laptop server.
- Dokumentasi IP dev server di README/TASKS.md: `public/fonts-manifest.dev.json` (hasil generate `npm run dev`/build) dicatat sebagai file ke-6 yang memuat IP beserta catatan salinan lama di `android/app/src/main/assets/`.
- Halaman Admin → Data Dapur → Tambah/Edit (`admin.dapurs.create`/`admin.dapurs.edit`): 500 di `DapurController::BANTEN_KABUPATEN_KOTA` / `getKnownKecamatan()` yang dihapus — dropdown kabupaten/kota, daftar kecamatan, dan kolom wilayah form kini dari `config/banten.php` (satu sumber kebenaran; ≈ −45 baris duplikat di controller). Bug ini lolos karena tak ada tes yang membuka halaman ini (70 tes lulus pun tetap hijau).
- Typo validasi Admin User: "Menimpah" → "Menimpa" (`AdminController`).
- Sidebar `admin/layouts/app.blade.php` sekarang role-aware: manajerial/kepala-dapur dapat menu lengkap (sebelumnya ngarah ke halaman 403).
- Halaman role read-only (manajerial/kepala-dapur) tidak lagi menampilkan/link-ke rute `admin.*` sehingga kena 403: `cctv-anomalies/index` + `show`, `cctvs/index` (form filter + "Lihat detail" + "Laporkan anomali"), `dapurs/index` (URL detail di JS `dapurList`), `dapurs/show` (link CCTV), `inspeksi/index` (link "Lihat" detail laporan hanya untuk admin). Prefix diambil dari nama rute aktif (`strtok(Route::currentRouteName(), '.')`), dan aksi admin-only (buat laporan, ubah status, ekspor PDF, ekspor inspeksi) disembunyikan sesuai `RoleMiddleware`/policy.
- Data Dapur untuk manajerial/kepala-dapur: halaman `dapurs/index` tadinya 500 (`Undefined array key "kabupaten_kota"` — filter wilayah dirender walau `$filterOptions` hanya dibikin untuk admin) **dan** daftarnya selalu kosong karena `$dapurData` hanya di-map untuk admin. Filter wilayah sekarang hanya untuk admin (controller memang hanya memfilter untuk admin), data Alpine di-map untuk semua role yang share view ini.
- CCTV monitoring: kartu Monitoring CCTV kini memutar HLS selama `stream_key` terdaftar, tidak lagi menunggu `status === 'online'` (status sekarang diperbarui tiap menit, jadi sebelumnya layar hitam walau stream sudah mengalir). Token pada `rtsp_url` dimask di halaman detail CCTV agar tidak terekspos. README menerangkan bahwa `CCTV_HLS_BASE` wajib `http://127.0.0.1:8888` untuk preview dari PC (cookie `Secure` MediaMTX) dan HLS via IP LAN butuh HTTPS/WebRTC.
- CCTV health check: MediaMTX kini jadi acuan utama status kamera (`CctvHealthCheckJob`), bukan ffprobe yang bisa timeout — sumber RTSP uji publik membuat ffprobe timeout 8 detik walau MediaMTX tetap `online`, sehingga badge sempat tetap `offline` (dan memicu `CctvAlertJob` palsu). `MediaMtxClient::isReady()` membaca field `online` (bukan `ready`, yang selalu false saat idle karena `sourceOnDemand`) dan mengembalikan `null` bila path belum terdaftar (404) supaya health check jatuh ke ffprobe. **Perubahan ini butuh `php artisan queue:restart`** bila worker queue sedang berjalan.
- CCTV health check: status kini diuji dari playlist HLS (`MediaMtxClient::isServing()`, meminta `index.m3u8?cookieCheck=1`), bukan field `online` — field itu hanya berarti "path punya sumber terdaftar", jadi RTSP yang mati/kedaluwarsa (mis. link uji 20 menit) tak pernah terhapus dan badge tetap `online`. Request ini juga memicu pull RTSP on-demand, sehingga sumber mati terdeteksi sebagai `offline` (waktu tunggu maksimal `sourceOnDemandStartTimeout`, default 10 detik). `isReady()` hanya jadi fallback saat HLS tak bisa dihubungi.
- CCTV admin: menyimpan kamera (`CctvController::update`) kini langsung menjalankan health check untuk kamera itu (`CctvHealthCheckJob` menerima `$cctvId` opsional) — sebelumnya badge menunggu jadwal berikutnya, jadi setelah RTSP URL segar (link uji kedaluwarsa tiap 20 menit) preview sudah jalan tapi badge masih `offline`. Scheduler tetap jalan sebagai jaring pengaman. **Perubahan signature job ini butuh `php artisan queue:restart`** bila worker queue sedang berjalan.
- README diselaraskan dengan kode: perintah build APK yang menunjuk skrip `build-apk.ps1`/`.bat` (sudah dihapus di 2.1.0) diganti alur Android Studio/Gradle; `CCTV_FFPROBE` dan `FCM_SERVER_KEY` masuk tabel konfigurasi (`FCM_SERVER_KEY` kini juga ada di `.env.example`); status peran `pengawas` dikoreksi sesuai `RestrictPengawasFromWeb` (web hanya dari Capacitor, tanpa CCTV/anomali) dan modul Inspeksi dicatat; deskripsi seeder dan status `sanctum.expiration` (`null`) diperbaiki; workflow `release.yml` disebut tanpa `composer validate` (khusus `ci.yml`).

### Removed
- **Dependensi produksi dihapus**: `barryvdh/laravel-dompdf` dan `intervention/image` dari `composer.json` (tak ada pemakaian kode lagi).
- Tombol `Excel`: SP → diubah jadi `Cetak` (format `xlsx` → view cetak browser HTML, bukan `.xls` dengan HTML kustom yang rentan XSS); anomaly CCTV tombol `Excel` dihapus (`export` hanya `pdf` dan CSV sekarang).

### Changed
- Dokumentasi diselaraskan dengan kode: policy `CctvPolicy`/`DapurPolicy` dan `GeolocationService` yang sudah dihapus tidak lagi ditulis di `ARCHITECTURE.md`/`SECURITY.md`/`README.md`/`TRD.md`; `selfie_photo` didokumentasikan sebagai path file (bukan base64) di `APP_FLOW.md`/`SECURITY.md`/`COST_AND_RATE_LIMITS.md`/`PRD.md`; daftar file tes di `TESTING.md` diperbarui; jadwal health check "tiap menit" (bukan 5 menit) konsisten di `ARCHITECTURE.md`/`APP_FLOW.md`/`COST_AND_RATE_LIMITS.md`/`PRD.md`/`CHANGELOG.md`.
- Klaim yang gampang basi dikurangi: angka tes (`README`/`TESTING`/`PRD`), jumlah tabel (`DATABASE.md`), frekuensi breakpoint (`RESPONSIVE.md`), dan tanggal di komentar CI (`ci.yml`) tidak lagi ditulis sebagai angka pasti — diganti acuan "ikut keluaran perintah".
- Kecil: minimum PHP di README/badge disamakan dengan `composer.json` (`8.3+`), `DashboardController` + `Auth\AuthController` masuk daftar controller `ARCHITECTURE.md`, baris pilot `IMPLEMENTATION_PLAN.md` menyebut WHEP/HLS.

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