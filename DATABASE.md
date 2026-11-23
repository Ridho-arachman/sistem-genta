# DATABASE — Skema & data

**Status**: aktif · 2026-11-18 · DB dev: PostgreSQL `laravel` @ 127.0.0.1:5432

## Prinsip
- PK string ULID (`Str::ulid()`), tanpa auto-increment — **kecuali** `jobs`/`failed_jobs` (sudah diperbaiki jadi bigint auto-increment).
- Waktu lokal `Asia/Jakarta`; semua kolom timestamp `timestamp without time zone`.
- Tidak ada rekaman video; semua data disimpan sebagai record biasa.

## Tabel aktif (14)
| Tabel | PK | Isi utama |
|---|---|---|
| `users` | ULID | `name, email, password(hashed), role(admin/manajerial/pengawas/kepala_dapur), registered_by, dapur_id, created_by/updated_by/deleted_by, deleted_at` (soft deletes) |
| `dapurs` | ULID | `nama, lokasi, alamat, kecamatan, kabupaten_kota, provinsi, map_embed_url` |
| `dapur_user` | composite (`dapur_id`,`user_id`) | pivot assignment pengawas ↔ dapur |
| `cctvs` | ULID | `dapur_id, nama, rtsp_url, status(online/offline/maintenance), section, camera_number, stream_key(ULID, unik-ish), last_checked_at` |
| `laporans` | ULID | `dapur_id, status, catatan, selfie_photo(path di storage, fallback data URL), latitude/longitude, address, created_by` |
| `laporan_hasil` | ULID | `laporan_id, indikator, hasil, created_by` (satu baris per indikator) |
| `sp_warnings` | ULID | `user_id, admin_id, level(smallint), reason, status, issued_at` |
| `cctv_anomaly_reports` | ULID | `cctv_id, admin_id, description, severity, status` |
| `fcm_tokens` | ULID | `user_id, token, platform, last_used_at` |
| `settings` | ULID | key-value pengaturan aplikasi |
| `personal_access_tokens` | bigint | token Sanctum untuk mobile |
| `jobs` | bigint | antrean queue |
| `failed_jobs` | bigint | job gagal |
| `cache` | key | cache driver `database` |
| `sessions` | bigint/ULID | session driver `database` (di `0001_01_01_000000_create_users_table`) |

## Migrasi penting terakhir
- `2026_11_17_000000_add_stream_key_and_last_checked_at_to_cctvs_table.php`
- `2026_11_18_000000_fix_jobs_table_primary_keys.php` — `jobs`/`failed_jobs` PK → bigint auto-increment (hanya jika tabel kosong)
- `2026_11_18_000001_drop_cctv_legacy_url_columns.php` — **DROP `ip_address`, `hls_url`** (kolom tidak dipakai lagi; `stream_url` murni turunan `CCTV_HLS_BASE + stream_key`)
- `2026_11_18_000002_add_performance_indexes.php` — indeks: `laporans(dapur_id,status)`, `laporans(created_by)`, `laporans(created_at)`, `dapur_user(user_id)`

## Indeks yang sudah ada
`cctvs` (bawaan), `dapur_user(dapur_id+user_id)`, indeks performa di atas. Belum ada: `laporans(status)` terpisah, indeks pada `created_at` di tabel lain. Catat N+1: sebagian besar list view sudah eager-load; satu mismatch (`admin/dashboard` `->user` vs `createdBy`) sudah diperbaiki.

## Aturan data
- Jangan menghapus data produksi. Pengecualian darurat: rebuild `jobs`/`failed_jobs` saat tabel kosong.
- Sebelum migrasi destruktif, `pg_dump` dulu (`scripts/backup-db.sh`).
- `rtsp_url` boleh berisi token; jangan cetak ke log/page kecuali halaman detail admin.
