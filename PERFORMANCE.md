# PERFORMANCE — Performa & anggaran

**Status**: aktif · 2026-11-24

## Anggaran CCTV health check
400 dapur × 5 kamera = 2.000 probe / menit ≈ 33,3 probe/detik. Tiap probe ≤ 8 detik; satu job per dapur, `$timeout=180`, `$tries=3`, `backoff=[30,120,300]`, `retry_after=300`. `Schedule::withoutOverlapping()` → siklus terlambat dilewatkan (bukan double). Begitu banyaknya worker praktis mustahil — longgarkan jadwal bila worker produksi sedikit. Scale worker: `docker compose up -d --scale queue=10`.

## Frontend
- Tailwind v4 (JIT, hanya utilitas yang dipakai) + daisyUI.
- JS modular per halaman (`resources/js/components/*`), Alpine 3.
- Tidak ada CDN untuk CSS/JS/font (semua self-hosted) — LCP diperbaiki.
- Pemutar CCTV (`resources/js/components/hls-player.js`) tampil di halaman Monitoring CCTV maupun detail kamera: WebRTC/WHEP dulu (latensi rendah), HLS via hls.js dimuat lazy sebagai fallback; `hlsAllowOrigins: ['*']` (izinkan embed lintas domain bila perlu).
- Gambar landing lokal (`public/images/`).
- Pagination ketat: `paginate(10)/(15)`; list dapur >2000 dibuat scroll/panel, bukan render semua.

## Database
- Indeks `laporans(dapur_id,status)`, `laporans(created_by)`, `laporans(created_at)`, `dapur_user(user_id)`. Indeks `cctvs(dapur_id)` sudah bawaan.
- Eager load di list: `with()`/`withCount()` (contoh: `DapurController::show` → `load(['cctvs','pengawas','kepalaDapur'])`; `LaporanController::index` → `with(['dapur','createdBy'])`; anomaly/SP/Cctv semua `with`).
- N+1 audit belum formal (lihat `TASKS.md`); satu mismatch query/akses sudah diperbaiki (`admin/dashboard`).
- PostgreSQL: `timestamp without time zone`, ULID string — bandingkan string, bukan integer. Tidak ada full-text index; string match `where('nama', 'like')` terbatas pada admin.

## Queue & cache
Driver `database` (bukan Redis). `CACHE_STORE=database`; `SESSION_DRIVER=database`. Tidak perlu Redis untuk skala ini. Cache ringan: `Settings` via `SettingsServiceProvider` (key-value `settings` di-cache).

### Operasi queue produksi (runbook)
- Pantau antrean: `php artisan queue:list` (belum terpasang) / cek tabel `jobs` langsung.
- Lihat gagal: `php artisan queue:failed`.
- Coba ulang gagal: `php artisan queue:retry {id}` atau `queue:retry all`.
- Bersihkan gagal lama: `php artisan queue:prune-failed --hours=48` (jadwalkan harian bila volume naik).
- Scale worker: `docker compose up -d --scale queue=10` (sesuai anggaran CCTV health check).
- Scheduler CCTV: `cctv:health-check` tiap menit dengan `withoutOverlapping()` — siklus terlambat dilewatkan, bukan double.

### Idempotensi & dedupe (tidak Redis)
- `CctvAlertJob`: `ShouldBeUnique` per `cctvId:status`, window 5 menit, `tries=3`, `backoff=[30,120]`, `timeout=60s`. Cegah alert CCTV ganda saat retry/race.
- `LaporanController@store`: transaksi + dedupe per pengawas + dapur + hari + status (`lockForUpdate`).
- `SpWarningController@store`: transaksi + dedupe per pengawas + admin + level + alasan + hari (`lockForUpdate`).
- `CctvAnomalyReportController@store`: transaksi + dedupe per kamera + admin + deskripsi + severity + hari (`lockForUpdate`).
- `CctvHealthCheckJob`: dievaluasi untuk `ShouldBeUnique` — **tidak dipakai** karena `CctvAlertJob` sudah melindungi dari alert ganda, dan job pemeriksaan itu sendiri idempoten (status sama ditimpa nilai sama).

### Correlation ID
Middleware `CorrelationIdMiddleware` (global) pasang `X-Request-ID` pada setiap request/response dan masukkan `correlation_id` ke konteks log. Job `CctvAlertJob` dan `CctvHealthCheckJob` menerima `correlationId` dan memulihkannya di `handle()` via `Log::withContext`, jejak HTTP → antrean tetap nyambung.

### Cache
`settings()` helper (`app/helpers.php`) baca dari `Cache::remember('settings:all', 86400)` bukan `Settings::get()` per kunci. `Settings::allKeyed()` sudah cache 24 jam + invalidasi otomatis saat `Settings::set()`. Jangan cache halaman dinamis/role-filtered/export/dashboard.

## Build
`vite build`; `php artisan config:cache route:cache view:cache` di produksi. `public/build/*` di-gitignore.

## Yang belum dilakukan
- Tidak ada load test (Artillery/k6) terdokumentasi.
- Tidak ada query-time observability (Pulse/Telescope) — monitoring produksi (Tier 1 `58/61/62`) belum.
- Image kamera/preview belum dioptimasi di jaringan dapur (HLS chunk besar).
