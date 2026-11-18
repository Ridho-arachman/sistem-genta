# IMPLEMENTATION_PLAN — Rencana implementasi & rilis

**Status**: aktif · 2026-11-18

## Urutan rilis
1. **Rilis dev (sekarang)** — kode hijau, dokumen root lengkap, tools lokal jalan (`php artisan serve`, MediaMTX, worker, scheduler).
2. **Pilot 1 dapur** — 1 dapur dengan IP publik + port-forward 554 diverifikasi menyeluruh: preview HLS, health check nyata, FCM alert, skenario token RTSP kedaluwarsa. Backup/restore drill dilakukan sekali.
3. **Pilot ~20 dapur** — amati durasi probe & failure rate; tetapkan jumlah worker (target 5–10); verifikasi tidak ada job timeout.
4. **Produksi penuh ±400 dapur** — TLS + domain, monitoring minimum (queue depth, error rate), jadwal backup harian.
5. **Panen Tier 2** — drill DR, alert playbook, capacity review.

## Milestone & exit criteria
| Milestone | Kriteria selesai |
|---|---|
| Pilot 1 | 100% probe pilot 1 dapur OK; `/up` 200; backup-restore diverifikasi |
| Pilot 20 | Durasi 1 batch health check < 2 menit; `failed_jobs` kosong; tidak ada 401 token |
| Produksi | SLO: 99% probe harian menampilkan status nyata; alert transisi < 1 mnt |

## Prioritas teknik
1. TLS/domain + hardening Nginx + `APP_DEBUG=false` (rilis).
2. Monitoring dasar (queue depth, worker mati, `failed_jobs`, log).
3. Dashboard pimpinan (read-only sudah ada; desain penuh menyusul).
4. N+1 audit + pengujian dengan data 400×5.
5. SSRF allowlist (post-prod).

## Yang TIDAK dikerjakan di rilis ini
- Tidak ada AI/LLM features.
- Tidak ada WebSocket/notifikasi real-time (FCM saja).
- Tidak ada auto-scaling worker (compose scale manual).
- Tidak ada monitoring observability stack lengkap (Pulse/Telescope/OpenTelemetry) — dipantau manual dulu.
