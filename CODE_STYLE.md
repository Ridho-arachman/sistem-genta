# CODE_STYLE — Gaya kode

**Status**: aktif · 2026-11-23

## PHP / Laravel
- PSR-12 dipaksa via **Laravel Pint** (`vendor/bin/pint --dirty --format agent` sebelum commit PHP).
- Selalu pakai kurung kurawal untuk semua branch (`if/else/foreach/match`), termasuk satu baris.
- Constructor property promotion; explicit return type di semua method publik; PHPDoc untuk array shape.
- `Str::ulid()` untuk PK string; `keyType='string'` + `public $incrementing = false` untuk model ULID.
- Route besar di `routes/web.php`; gunakan route model binding; jangan gunakan Facade di tempat yang bisa di-inject.
- Relasi di model; eager loading wajib di list view (`with(...)` / `withCount(...)`).
- Migration: tidak destruktif (kecuali sudah di-backup); FK dengan `cascadeOnDelete`/`nullOnDelete` harus sadar data.

## Blade / frontend
- Tailwind v4 + daisyUI 5. Buat komponen `resources/views/{area}/components/` bila dipakai > 1 kali.
- Gunakan variabel CSS `var(--color-*)` dan class utilitas; jangan tulis CSS custom baru kecuali terpaksa.
- Alpine.js untuk interaksi kecil; tidak ada React (meski `@heroicons/react` terpasang, jangan dipakai di Blade).
- Token desain terpusat di `resources/css/app.css` (lihat `DESIGN_SYSTEM.md`).
- `x-toast` untuk notifikasi; `@error('field')` untuk pesan validasi inline.

## JavaScript / CSS
- Font self-hosted via `@fontsource/*` (Inter, Space Grotesk, JetBrains Mono).
- `resources/js/components/hls-player.js` untuk preview CCTV (WebRTC/WHEP dulu, `hls.js` dimuat lazy sebagai fallback), `leaflet` untuk peta, `quill` untuk rich text.
- Import CSS: `resources/css/app.css` (Tailwind + daisyUI) dan `resources/css/vendor.css`.
- Jangan inline `<script>` besar baru; modul JS di `resources/js/components/*`.

## Testing
- Pest; `tests/Feature` (HTTP/policy/DB) dan `tests/Unit`.
- RefreshDatabase + sqlite :memory: (default `phpunit.xml`), BCRYPT_ROUNDS=4.
- CCTV/MediaMTX dipaksa mati di env test; tidak boleh mengenai network nyata.
- Jangan hapus/rename tes yang ada tanpa persetujuan.

## Nama & konvensi domain
Indonesia: `dapur`, `pengawas`, `kepala_dapur` (snake), `laporan`, `anomali`, `sp`. Inggris: nama teknis internal (`Cctv`, `StreamKey`, `HealthCheck`).
