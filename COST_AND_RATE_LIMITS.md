# COST_AND_RATE_LIMITS — Biaya & batas

**Status**: tidak ada fitur AI · 2026-11-18

## Biaya (dev & produksi)
| Komponen | Biaya | Catatan |
|---|---|---|
| Hosting server pusat (1 VPS, ±4 vCPU/8 GB) | tergantung penyedia (±Rp 400rb–1jt/bln) | Windows dev: tidak ada biaya runtime |
| PostgreSQL (self-hosted) | Rp 0 | masuk server yang sama |
| MediaMTX | Rp 0 (open source) | di server yang sama |
| Bandwidth CCTV pull | tergantung bitrate kamera; mode `sourceOnDemand` hemat | hanya saat ditonton/probe |
| FCM | Rp 0 | push notification |
| LLM/AI API | **Rp 0 (belum ada)** | tidak dipakai |

## Rate limit yang aktif
- `POST /login` & `/forgot-password`: 5 percobaan / 1 menit (HTTP 429 setelahnya) — per IP kalau belum login, per user kalau sudah.
- **Endpoint `/api/mobile/*` belum dibatasi** — risiko abuse; lihat `TASKS.md`.
- Queue: `retry_after=300` mencegah job terlindas ganda; `tries=3`.

## Batas operasional (skala)
- Probe: 6,7 probe/detik pada puncak (2.000 probe / 5 menit). Kalau probe rata-rata kena timeout 8 detik, butuh ≥ 13 worker untuk 5 menit penuh tanpa tumpang tindih — mulai dengan 5–10 dan ukur.
- Preview HLS: bandwidth MediaMTX keluar ke pengawas/pimpinan; batasi concurrent viewers bila perlu (jangan dulu).
- Foto laporan: base64 di kolom text; 400 dapur × N laporan/hari bisa membengkak — pertimbangkan kompresi/resize sebelum simpan (belum diimplementasi).

## Keputusan biaya
- Tidak ada Blaze/cloud-specific service; semua bisa dipindah ke VPS manapun dengan Docker.
- Tidak ada biaya per-request ke pihak ketiga selain bandwidth CCTV.
