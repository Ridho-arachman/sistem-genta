# ARCHITECTURE — GENTA Pangan Banten

**Status**: aktif · 2026-11-18

## Topologi aliran data

```
CCTV dapur (RTSP, port 554, IP publik per dapur)
        │  pull on-demand (sourceOnDemand)
        ▼
MediaMTX di server pusat ──► HLS (/<stream_key>/index.m3u8) ──► Browser pengawas/admin (hls.js)
        ▲
        └─ Control API (:9997) ◄── Laravel (MediaMtxClient)

ffprobe (per 5 mnt, per dapur) ──► status cctvs (online/offline/maintenance) ──► FCM alert
Laravel (web + /api/mobile via Sanctum) ──► PostgreSQL (jobs/cache/sessions juga di DB)
```

Tidak ada rekaman; tidak ada broadcasting/WebSocket; tidak ada `routes/api.php` (routes mobile di `web.php`, prefix `/api/mobile`, middleware `auth:sanctum`).

## Lapisan aplikasi

| Lapisan | Lokasi | Catatan |
|---|---|---|
| Routing | `routes/web.php` | Semua route + Sanctum di sini; tidak ada `api.php` |
| Controller | `app/Http/Controllers/*` | AdminController, DapurController, CctvController, InspeksiController, LaporanController, SpWarningController, CctvAnomalyReportController, Api\MobileController |
| Model | `app/Models/*` | ULID string PK (`keyType=string`, `incrementing=false`); tabel `jobs`/`failed_jobs` sudah bigint auto-increment |
| Policy | `app/Policies/*` | Cctv, CctvAnomalyReport, Dapur, Laporan |
| Service | `app/Services/*` | MediaMtxClient, RtspProbe, GeolocationService, FcmService |
| Job | `app/Jobs/*` | CctvHealthCheckJob, CctvAlertJob |
| Command | `app/Console/Commands/*` | CctvHealthCheck (tiap 5 menit), MigrateLaporanPhotos |

## Boundary peran
Middleware `role:<nama>` (`App\Http\Middleware\RoleMiddleware`) dan `restrict.pengawas` (pengawas dibatasi ke halaman tertentu di web). Dashboard peran:
- `admin` → `admin.dashboard` (penuh)
- `manajerial` → `manajerial.dashboard` (read-only, ringkasan seluruh sistem)
- `kepala-dapur` → `kepala-dapur.dashboard` (dapur yang ditugaskan)
- `pengawas` → mobile/web terbatas

## Keputusan arsitektur
Lihat `docs/19-architecture-decisions.md` (index lama, akan dirapikan) dan bagian "Keputusan" di `TASKS.md`. Ringkasan keputusan terpenting:
1. Pull (bukan push) RTSP; `sourceOnDemand` agar kamera idle tidak mengonsumsi bandwidth/NVR-CPU.
2. HLS URL diturunkan dari `CCTV_HLS_BASE` + `stream_key` (tidak disimpan di DB; kolom `hls_url`/`ip_address` dihapus).
3. Queue driver database (tidak perlu Redis); `retry_after` 300 > timeout job 180.
4. Tidak ada microservice; satu Laravel monolith.

## Skala
400 dapur × 5 kamera = 2.000 probe / 5 menit ≈ **6,7 probe/detik**. `Schedule::withoutOverlapping()` mencegah tumpang tindih; butuh banyak worker kalau probe sering kena timeout 8 detik.
