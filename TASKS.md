# TASKS — Backlog & status

**Status**: aktif · 2026-11-23 · bekerja paling baik bersama `CHANGELOG.md`

## Selesai (2026-11-23)
- [x] Audit konsistensi repo: upload foto (ekstensi dari allowlist), ekspor XLSX berskope pengawas, API mobile check-in hanya dapur assignment, dashboard status dapur (progress/status dari accessor `Dapur`), restore user mempertahankan pivot, statistik tak menghitung user terhapus, SP hanya untuk pengawas, teks `switch-mode.bat` yang tidak ada di `public/index.html` — plus 12 tes regresi
- [x] Link/aksi role read-only di view admin (`cctv-anomalies`, `cctvs`, `dapurs`, `inspeksi`) dibetulkan agar tidak kena 403, plus tes `RoleReadOnlyPageTest` (8 tes)
- [x] Data Dapur read-only: halaman index 500 (`$filterOptions` hanya admin) + daftar kosong (`$dapurData` hanya admin) diperbaiki
- [x] Dokumen disinkronkan dengan kode: policy/service yang dihapus, `selfie_photo` sebagai path, angka suite, jadwal health check "tiap menit"

## Selesai (2026-11-18, lanjutan)
- [x] Sidebar `admin/layouts/app.blade.php` role-aware (manajerial/kepala-dapur punya menu lengkap; sebelumnya ngarah ke halaman 403)
- [x] Hapus dead-code `FcmService::sendAbsensiReminder()` (Absensi sudah dihapus sejak 2026-11-02)

## Selesai (2026-11-18)
- [x] Hapus kolom `ip_address`/`hls_url`; `stream_url` murni HLS (`CCTV_HLS_BASE + stream_key`)
- [x] Fix `jobs`/`failed_jobs` PK; `retry_after` 300; `CctvHealthCheckJob` tries/backoff
- [x] Rate limit login & forgot-password (5/menit)
- [x] Indeks `laporans` & `dapur_user(user_id)`
- [x] CI penuh Hijau (48 tes) + Pint
- [x] Hapus dead code Absensi; 4 view role read-only dibuat
- [x] Dockerize app/queue/scheduler/MediaMTX (config valid; **image belum ke-build**, Docker Desktop mati)

## Untuk rilis produksi (Tier 1 — butuh server/domain)
- [ ] Nginx + TLS (Let's Encrypt) + domain; hapus `APP_DEBUG=true`
- [ ] HSTS + security headers (X-Content-Type-Options, Referrer-Policy, CSP sesuai kebutuhan HLS/leaflet)
- [ ] Port-forward 554 per dapur (±400) — pekerjaan ISP/lapangan; verifikasi 1 dapur dulu
- [ ] `pg_dump` terjadwal (cron/sistemd timer pakai `scripts/backup-db.sh`)
- [ ] Monitoring: queue depth, worker mati, ffprobe timeout rate; alert ke masalah admin
- [ ] `CCTV_MEDIAMTX_USER/PASS` diisi; `CCTV_HLS_BASE` internal + HLS origin dipersempit
- [ ] `scripts/backup-db.sh` diverifikasi restore setidaknya 1×

## Penting, bisa dikerjakan sekarang

- [ ] `cctv:health-check` belum mengukur durasi per batch (untuk skala worker nyata)
- [ ] N+1 formal audit + tes query-count untuk list 500+ item (dapur/cctv list)
- [ ] IP dev server hardcode di 6 file (`.env`, `vite.config.js`, `public/hot`, `capacitor.config.json`, `public/index.html`, `public/fonts-manifest.dev.json`) — satu sumber saja (mis. satu env `DEV_SERVER_URL` yang dibaca semua file) supaya ganti IP cukup satu tempat; `fonts-manifest.dev.json` juga salinan lama di `android/app/src/main/assets/`
- [ ] Perbaiki salah ketik/inkonsistensi dokumen lama yang tidak otomatis tercover root docs

## Tier 2 (sesuai arsitektur 111)
- Deploy strategy (blue/green / rolling), on-call, capacity planning, runbook, DR/failover, on-call drill, log aggregation, metrics (queue, probe rate), alerting playbooks, webhooks → semua butuh server produksi (belum). SSRF mitigation (IP allowlist) menunggu environment produksi (menutup testing RTSP lokal).

## Keputusan yang berlaku
- Pull RTSP, bukan push; MediaMTX sebagai proxy HLS; tidak ada rekaman (NVR hardware tim).
- Server pusat adalah satu-satunya yang mengakses RTSP dapur; pengawas tidak punya akses langsung.
- WIB (`Asia/Jakarta`); tidak ada konversi timezone.
- Tidak menggunakan Redis/Horizon/Pulse/Telescope saat ini.
- Tidak ada fitur AI/LLM; dok AI di root adalah panduan saja.

## Catatan pengguna
- "Data sekarang masih dev" — perubahan destruktif boleh, tapi backup dulu (`scripts/backup-db.sh`).
- User menangani workaround ISP/NAT sendiri untuk RTSP port-forward.
- Dashboard pimpinan (`manajerial`/`kepala-dapur`) sengaja read-only minimal; desain penuh nanti.
