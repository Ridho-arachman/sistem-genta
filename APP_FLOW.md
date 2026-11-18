# APP_FLOW — Alur aplikasi

**Status**: aktif · 2026-11-18

## 1. Login & hak akses
1. `GET /login` → form. `POST /login` dibatasi `throttle:5,1` (429 setelah 5 percobaan/menit per IP bila gagal).
2. Setelah sukses, redirect `/dashboard` yang memilih halaman berdasarkan `role`:
   - admin → `admin.dashboard`; manajerial → `manajerial.dashboard`; kepala_dapur → `kepala-dapur.dashboard`; pengawas → dibatasi (`restrict.pengawas`) ke alur mobile/inspeksi.
3. Soft-deleted (`deleted_at`) ditolak dengan pesan "Akun dinonaktifkan".
4. Logout `POST /logout` → kembali ke landing.

## 2. Admin: kelola dapur
`admin/dapurs` (index/create/edit) → pilih wilayah Banten (dropdown kabupaten → kecamatan, `config/banten.php`) → simpan. Pengawas bisa ditugaskan lewat pivot `dapur_user`.

## 3. Admin: kelola CCTV
1. `admin/cctvs/create` → `dapur_id`, `nama`, `rtsp_url`, `section`, `camera_number`.
2. Saat store/update, `syncStreamPath()` mendaftarkan path ke MediaMTX (jika `CCTV_MEDIAMTX_API` terkonfigurasi).
3. Preview HLS di detail kamera: `config('cctv.hls_base') . '/' . $stream_key . '/index.m3u8'` (kosong bila `CCTV_HLS_BASE` tidak di-set).
4. Health check (`cctv:health-check` tiap 5 menit) memprobe tiap dapur (≤ 5 kamera × 8 detik), menulis `status` + `last_checked_at`, dan mengirim FCM hanya pada transisi ke offline.

## 4. Laporan pengawasan (semua peran, via mobile/web)
`pengawas/laporan` atau `admin/laporan` — list + detail + export. Isi: `laporan_hasil` per indikator, `selfie_photo` (base64 disimpan sebagai text), `latitude/longitude/address` (foto tidak menyimpan lat/long sendiri setelah migrasi Drop). Inspeksi check-in/out + export tersedia di `admin/inspeksi` dan `pengawas/inspeksi`.

## 5. Anomali & SP
Anomali CCTV: `admin/cctv-anomalies` (create/list/show). SP: `admin/sp-warnings` terikat ke `user_id`, ada level, status, export.

## 6. API mobile (`/api/mobile`, Sanctum)
- `POST /fcm-token` — simpan token FCM device.
- `POST /sync-locations` — lokasi gps (geofence).
- `GET /dapurs/geofences` — daftar geofence dapur.
- `POST /absensi/auto-checkin` — check-in via geofence (sekarang membuat `Laporan`).

## 7. Alur error utama
- Validasi gagal → redirect back + `withErrors` → `<x-toast />` (alert daisyUI kanan-bawah).
- 403 (peran tidak sesuai) → halaman 403/redirect login.
- 429 (rate limit) → halaman 429.
- Kamera offline → status per kamera di UI + notifikasi FCM admin.
- Mobile: error ditampilkan inline (lihat `ERROR_STATES.md`).

## 8. Yang TIDAK ada (jangan diharapkan di kode)
- Tidak ada route `/register`.
- Tidak ada `routes/api.php`, tidak ada WebSocket/reverb/broadcast.
- Tidak ada remember-me token rotation khusus (bawaan Laravel).
- Tidak ada fitur AI/LLM.
