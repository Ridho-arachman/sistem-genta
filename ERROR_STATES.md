# ERROR_STATES — Status error & cara ditampilkan

**Status**: aktif · 2026-11-18

## Filosofi
Jangan ubah perilaku error tanpa alasan. Pesan generik di login (`Email atau password salah`) menjaga keamanan (tidak membocorkan apakah email terdaftar). Notifikasi memakai `<x-toast />` (alert daisyUI di kanan-bawah, `alert-success`/`alert-error`).

## Tabel error

| Kelas | Trigger | Respons | Di mana |
|---|---|---|---|
| Login gagal | kredensial salah | redirect back + `errors` (email) | `AuthController@login` |
| Akun nonaktif | `deleted_at` terisi | `Akun dinonaktifkan` | idem |
| 429 | 5 percobaan/menit (login, forgot-password) | halaman 429 | `throttle:5,1` |
| 403 | peran tidak sesuai (`role:`, `canAccessDapur`) | halaman 403 | middleware |
| Validasi form | `validate()` gagal | kembali ke form + `withErrors` + `withInput` | semua controller |
| CCTV tidak reachable | `ffprobe` gagal / 401 / token invalid | status `offline`, `last_checked_at` terisi, FCM saat transisi | `CctvHealthCheckJob` |
| MediaMTX down | API `:9997` mati | `syncStreamPath` log warning, publish jalan terus | `MediaMtxClient` |
| Mobile 401 | token Sanctum invalid | aplikasi mobile → login ulang | `auth:sanctum` |
| Mobile 422 | validasi gagal | error per field | controller |
| View hilang | route menunjuk view tidak ada (pernah terjadi) | 500 `View [x] not found` | dideteksi di test (case lama sudah diperbaiki) |
| Queue gagal | job error | `failed_jobs`, coba ulang (tries=3, backoff) | `CctvHealthCheckJob` |

## UI pattern validasi inline
- Blade: `@error('email') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror` atau `old('email')` untuk isi ulang.
- 422 JSON untuk mobile: `{ "message": "...", "errors": { "field": ["..."] } }` (default Laravel).

## Cek manual
- `php artisan cctv:health-check` → lihat `last_checked_at` terisi & job gagal (kalau ada) di `failed_jobs`.
- `storage/logs/laravel.log` (mode debug) untuk stack 500 — **jangan tayang di produksi (`APP_DEBUG=false`)**.

## Belum ada
- Halaman error custom (404/500) belum dibuat — error umum memakai default Laravel.
- Tidak ada alert lintas halaman (mis. notif banner "sistem dalam pemeliharaan") selain toast inline.
