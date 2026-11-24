# DATABASE — Skema & data

**Status**: aktif · 2026-11-24 · DB dev: PostgreSQL `laravel` @ 127.0.0.1:5432

## Prinsip
- PK string ULID (`Str::ulid()`), tanpa auto-increment — **kecuali** `jobs`/`failed_jobs` (sudah diperbaiki jadi bigint auto-increment).
- Waktu lokal `Asia/Jakarta`; semua kolom timestamp `timestamp without time zone`.
- Tidak ada rekaman video; semua data disimpan sebagai record biasa.

## Tabel aktif
| Tabel | PK | Isi utama |
|---|---|---|
| `users` | ULID | `name, email, password(hashed), role(admin/manajerial/pengawas/kepala_dapur), registered_by, dapur_id, created_by/updated_by/deleted_by, deleted_at` (soft deletes) |
| `dapurs` | ULID | `nama, alamat, kecamatan, kabupaten_kota, provinsi, map_embed_url` |
| `dapur_user` | composite (`dapur_id`,`user_id`) | pivot assignment pengawas ↔ dapur |
| `cctvs` | ULID | `dapur_id, nama, rtsp_url, status(online/offline/maintenance), section, camera_number, stream_key(ULID, unik-ish), last_checked_at` — `stream_url` (HLS) dan `web_rtc_url` (WHEP) adalah atribut turunan, bukan kolom |
| `laporans` | ULID | `dapur_id, status, catatan, selfie_photo(path di storage, fallback data URL), latitude/longitude, address, created_by` |
| `laporan_hasil` | ULID | `laporan_id, indikator, hasil, created_by` (satu baris per indikator) |
| `sp_warnings` | ULID | `user_id, admin_id, level(smallint), reason, status, issued_at` |
| `cctv_anomaly_reports` | ULID | `cctv_id, admin_id, description, severity, status` |
| `fcm_tokens` | ULID | `user_id, token, platform, last_used_at` |
| `settings` | ULID | key-value pengaturan aplikasi |
| `personal_access_tokens` | bigint | token Sanctum untuk mobile |
| `password_reset_tokens` | — | token reset password (migrasi bawaan Laravel) |
| `jobs` | bigint | antrean queue |
| `job_batches` | — | batch queue (migrasi bawaan Laravel) |
| `failed_jobs` | bigint | job gagal |
| `cache` | key | cache driver `database` |
| `cache_locks` | — | lock cache (migrasi bawaan Laravel) |
| `sessions` | bigint/ULID | session driver `database` (di `0001_01_01_000001_create_users_table`) |

## Tabel yang sudah dihapus
`absensis` (modul absensi) dan `laporan_photos` — foto laporan kini tinggal di satu kolom `laporans.selfie_photo`.

## Migrasi penting terakhir
Sejak 2026-11-24 migrasi lama dikonsolidasikan ke `database/migrations/` (tanpa schema dump). Efek perubahan terakhir yang masih terasa:
- `cctvs.stream_key` + `last_checked_at` — status kamera diverifikasi scheduler, bukan input manual.
- `jobs`/`failed_jobs` PK → bigint auto-increment (hanya jika tabel kosong).
- Kolom legacy `cctvs.ip_address` dan `cctvs.hls_url` dihapus; `stream_url` turunan `CCTV_HLS_BASE + stream_key` dan `web_rtc_url` turunan `CCTV_WEBRTC_BASE + stream_key` (keduanya atribut model, bukan kolom).
- Indeks performa: `laporans(dapur_id,status)`, `laporans(created_by)`, `laporans(created_at)`, `dapur_user(user_id)`.

## Indeks yang sudah ada
`cctvs` (bawaan), `dapur_user(dapur_id+user_id)`, indeks performa di atas. Belum ada: `laporans(status)` terpisah, indeks pada `created_at` di tabel lain. Catat N+1: sebagian besar list view sudah eager-load; satu mismatch (`admin/dashboard` `->user` vs `createdBy`) sudah diperbaiki.

## Aturan data
- Jangan menghapus data produksi. Pengecualian darurat: rebuild `jobs`/`failed_jobs` saat tabel kosong.
- Sebelum migrasi destruktif, `pg_dump` dulu (`scripts/backup-db.sh`).
- `rtsp_url` boleh berisi token; jangan cetak ke log/page kecuali halaman detail admin.
