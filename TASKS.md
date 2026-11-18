# TASKS — Backlog & status

**Status**: aktif · 2026-11-18 · bekerja paling baik bersama `CHANGELOG.md`

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
- [ ] `docs/06-cctv-monitoring.md`, `docs/09-mobile-api.md`, `docs/14-database-schema.md` — ditandai "usang, lihat root docs" (masih salah soal Absensi/IP/HLS)
- [ ] Sidebar di `admin/layouts/app.blade.php` menampilkan link admin-only untuk manajerial/kepala-dapur (mengklik → 403). Buat sidebar role-aware atau layout read-only terpisah saat dashboard pimpinan dirancang
- [ ] `FcmService::sendAbsensiReminder()` belum dipanggil dari mana pun (Absensi dihapus) — hapus atau daur ulang untuk pengingat laporan
- [ ] `cctv:health-check` belum mengukur durasi per batch (untuk skala worker nyata)
- [ ] N+1 formal audit + tes query-count untuk list 500+ item (dapur/cctv list)
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
