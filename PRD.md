# PRD — GENTA Pangan Banten (Pengawasan Dapur MBG)

**Status**: aktif · **Terakhir diperbarui**: 2026-11-18

## Ringkasan
Aplikasi web + mobile untuk pengawasan operasional dapur Pangan Banten (Program MBG).
~**400 dapur**, @ 3–5 kamera CCTV (**±1.600 kamera**), 1 pengawas untuk tiap **5 dapur**,
pimpinan (manajerial) melihat dashboard read-only.

## Pengguna & peran
| Peran | Akses | Catatan |
|---|---|---|
| `admin` | penuh (users, dapur, CCTV, laporan, anomaly, SP, pengaturan) | 1–beberapa orang |
| `manajerial` | read-only (dashboard, dapur, CCTV, anomaly, SP, inspeksi, ekspor) | pimpinan |
| `pengawas` | mobile app + web obstruksi (`restrict.pengawas` blocks web untuk sebagian besar halaman), inspeksi, laporan | 1 per 5 dapur |
| `kepala_dapur` | read-only (dashboard, dapur, CCTV, anomaly) | per dapur |

## Fitur inti
1. Manajemen dapur (CRUD admin) + wilayah Banten (provinsi/kabupaten/kecamatan) + geofence (map_embed_url).
2. Manajemen CCTV: `rtsp_url` + `stream_key`, preview HLS, health check otomatis tiap menit (MediaMTX/HLS, ffprobe sebagai fallback), status online/offline/maintenance, alerta FCM saat transisi ke offline.
3. Laporan pengawasan harian (`laporans` + `laporan_hasil` + selfie disimpan sebagai file, kolom hanya berisi path).
4. Inspeksi (check-in/check-out + export).
5. SP Warning, Anomaly CCTV, FCM push notification, mobile API (Sanctum).
6. Dashboard pimpinan: ringkas (manajerial, `kepala-dapur`), masih read-only sederhana.

## Non-goals
- **Rekaman video** tidak dipegang sistem ini. NVR hardware milik tim hardware.
- Tidak membangun Cloudflare One Client untuk produksi; server pusat **pull RTSP dari IP publik dapur lewat port-forward 554** (user menangani workaround ISP).
- Tidak ada fitur AI/LLM di rilis ini.
- Tidak ada multi-tenancy di luar peran di atas; tidak ada billing; tidak ada SSO.

## Skala & SLA (target)
- 400 job health-check/menit; tiap job probe ≤ 5 kamera × 8 detik.
- Praktis: `docker compose up -d --scale queue=10` di server produksi, atau longgarkan jadwal health check bila worker terbatas.
- Waktu kerja pengawas: web pakai aplikasi mobile (Capacitor), foto disimpan sebagai file (path di DB), geofence dihitung di perangkat mobile.

## Definisi selesai (rilis ini)
- Full test suite hijau (80 tes / 234 assertion).
- CI GitHub Actions (Pint + full suite).
- Doc suite (root) lengkap dan sinkron dengan kode.

## Risiko terbuka
Lihat `TASKS.md`. Yang paling penting: belum ada TLS/domain produksi, belum ada monitoring produksi, dashboard pimpinan masih stub, dan 4 view pengawas (`pengawas.cctvs.*`) yang diminta dulu belum ada karena memang tidak ada route-nya.
