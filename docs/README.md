# Docs (modul lama — usang sebagian)

Dokumen di direktori ini ditulis untuk versi aplikasi 2026-10 (MVP 2.0.0). Banyak keputusan teknis berubah:

- **Absensi** dihapus (milik 2026-11-02); semua referensi Absensi di `07-absensi-system.md`, `03`, `05`, `09`, `12`, `13`, `17` adalah usang.
- **`ip_address` / `hls_url`** kolom sudah dihapus (2026-11-18); `06-cctv-monitoring.md`, `09-mobile-api.md`, `14-database-schema.md` masih menyebutnya.
- **Stack** sebenarnya: Laravel 13, PostgreSQL, queue/cache/session driver **database** (bukan Redis); lihat `../TRD.md`.
- **`routes/api.php` tidak ada**; API mobile di `routes/web.php` (`/api/mobile`, Sanctum). Lihat `../API.md`.

**Sumber kebenaran sekarang** adalah dokumen utama di root repo:

`../PRD.md` · `../ARCHITECTURE.md` · `../TRD.md` · `../DATABASE.md` · `../API.md` · `../APP_FLOW.md` · `../TESTING.md` · `../SECURITY.md` · `../PERFORMANCE.md` · `../CHANGELOG.md` · `../TASKS.md`

Dokumen modul lama tetap berguna untuk konteks historis, tetapi **jangan gunakan untuk keputusan baru**.
