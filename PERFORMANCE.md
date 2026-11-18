# PERFORMANCE — Performa & anggaran

**Status**: aktif · 2026-11-18

## Anggaran CCTV health check
400 dapur × 5 kamera = 2.000 probe / 5 menit ≈ 6,7 probe/detik. Tiap probe ≤ 8 detik; satu job per dapur, `$timeout=180`, `$tries=3`, `backoff=[30,120,300]`, `retry_after=300`. `Schedule::withoutOverlapping()` → siklus terlambat dilewatkan (bukan double). Scale worker: `docker compose up -d --scale queue=10`.

## Frontend
- Tailwind v4 (JIT, hanya utilitas yang dipakai) + daisyUI.
- JS modular per halaman (`resources/js/components/*`), Alpine 3.
- Tidak ada CDN untuk CSS/JS/font (semua self-hosted) — LCP diperbaiki.
- HLS.js hanya di halaman detail CCTV; `hlsAllowOrigins: ['*']` (izinkan embed lintas domain bila perlu).
- Gambar landing lokal (`public/images/`).
- Pagination ketat: `paginate(10)/(15)`; list dapur >2000 dibuat scroll/panel, bukan render semua.

## Database
- Indeks `laporans(dapur_id,status)`, `laporans(created_by)`, `laporans(created_at)`, `dapur_user(user_id)` (migrasi `2026_11_18_000002`). Indeks `cctvs(dapur_id)` sudah bawaan.
- Eager load di list: `with()`/`withCount()` (contoh: `DapurController::show` → `load(['cctvs','pengawas','kepalaDapur'])`; `LaporanController::index` → `with(['dapur','createdBy'])`; anomaly/SP/Cctv semua `with`).
- N+1 audit belum formal (lihat `TASKS.md`); satu mismatch query/akses sudah diperbaiki (`admin/dashboard`).
- PostgreSQL: `timestamp without time zone`, ULID string — bandingkan string, bukan integer. Tidak ada full-text index; string match `where('nama', 'like')` terbatas pada admin.

## Queue & cache
Driver `database` (bukan Redis). `CACHE_STORE=database`; `SESSION_DRIVER=database`. Tidak perlu Redis untuk skala ini. Cache ringan: `Settings` via `SettingsServiceProvider` (key-value `settings` di-cache).

## Build
`vite build`; `php artisan config:cache route:cache view:cache` di produksi. `public/build/*` di-gitignore.

## Yang belum dilakukan
- Tidak ada load test (Artillery/k6) terdokumentasi.
- Tidak ada query-time observability (Pulse/Telescope) — monitoring produksi (Tier 1 `58/61/62`) belum.
- Image kamera/preview belum dioptimasi di jaringan dapur (HLS chunk besar).
