# ENV — Konfigurasi lingkungan

**Status**: aktif · 2026-11-23 · sumber: `.env.example`, `config/*.php`

## Wajib di-set produksi (jangan isi di sini)
`APP_KEY`, `DB_PASSWORD`, `CCTV_FFPROBE` (kalau bukan `ffprobe` di PATH), `CCTV_MEDIAMTX_PASS` (kalau API terproteksi).

## Aplikasi
| Key | Contoh/default | Fungsi |
|---|---|---|
| `APP_NAME` | `Laravel` (ubah jadi `GENTA Pangan Banten`) | nama aplikasi |
| `APP_ENV` / `APP_DEBUG` | `local` / `true` (dev) | **harus `production` / `false` di server** |
| `APP_URL` | `http://localhost:8000` | base URL (harus `https://...` di produksi) |
| `APP_LOCALE` | `en` (pertimbangkan `id`) | bahasa |
| `BCRYPT_ROUNDS` | `12` | cost bcrypt |
| `LOG_CHANNEL` / `LOG_LEVEL` | `stack` / `debug` | logging |

## Database & driver
`DB_CONNECTION=pgsql`, `DB_HOST=127.0.0.1`, `DB_PORT=5432`, `DB_DATABASE=laravel`, `DB_USERNAME=postgres` (dev). `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, `CACHE_STORE=database` (semuanya database driver; tidak perlu Redis). `FILESYSTEM_DISK=local`.

## CCTV / MediaMTX
| Key | Default | Fungsi |
|---|---|---|
| `CCTV_HLS_BASE` | (kosong) | Base URL HLS, mis. `http://10.x.x.x:8888`. Kosong = preview kamera tidak tampil (`stream_url` = null) |
| `CCTV_MEDIAMTX_API` | (kosong) | Control API MediaMTX, mis. `http://10.x.x.x:9997`. Kosong = sinkronisasi path dinonaktifkan |
| `CCTV_MEDIAMTX_USER` / `_PASS` | (kosong) | Basic auth API (kosong = tanpa auth; set di produksi) |
| `CCTV_MEDIAMTX_RTSP` | `rtsp://127.0.0.1:8554` | alamat RTSP MediaMTX untuk path turunan (kamera dengan RTSP URL sama) |
| `CCTV_WEBRTC_BASE` | `http://127.0.0.1:8889` | Base URL WebRTC/WHEP media server (kosong = pemutar hanya HLS) |
| `CCTV_PROBE_TIMEOUT` | `8` | timeout ffprobe (detik) per kamera |
| `CCTV_FFPROBE` | `ffprobe` | path/nama binary ffprobe |

## Mail / integrasi
`MAIL_MAILER=log` (dev), `MAIL_*`, `AWS_*` (tidak dipakai saat ini), `VITE_APP_NAME`.

## Aturan
- `.env` tidak pernah di-commit (sudah `.gitignore`).
- `.env.example` tidak boleh berisi nilai rahasia.
- Perubahan key produksi lewat environment manager / CI secrets, bukan edit manual di server produksi (kecuali darurat).
- `phpunit.xml` memaksa `CCTV_HLS_BASE=""` dan `CCTV_MEDIAMTX_API=""` (test hermetic).
- Device API mobile menggunakan `APP_URL` sebagai base; tidak ada variabel `API_BASE_URL` terpisah.
