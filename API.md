# API — Mobile & Web (GENTA)

**Status**: aktif · 2026-11-23

Tidak ada `routes/api.php`. Semua endpoint API berada di `routes/web.php`:

## Web (session, untuk browser)
Semua route web terdokumentasi di `routes/web.php`. Ringkas: `/login`, `/password.*`, `/logout`, `/dashboard` (redirect per peran), `admin/*` (role:admin), `pengawas/*` (role:pengawas + `restrict.pengawas`), `manajerial/*` (role:manajerial, read-only), `kepala-dapur/*` (role:kepala_dapur, read-only).

Rate limit (cache `database`, tanpa Redis): endpoint kredensial (`/login`, `/forgot-password`, `/reset-password`, ganti profil/password) pakai limiter `auth` — 5/menit per IP dan 30/menit per email untuk guest, 5/menit per user untuk yang sudah login. Endpoint `/api/mobile/*` pakai limiter `mobile` — 30/menit per user. Semua rute web yang dilindungi autentikasi pakai limiter `web` — 60/menit per user per rute.

## Mobile (`/api/mobile`, Sanctum `Bearer`)
| Method | Path | Controller | Fungsi |
|---|---|---|---|
| POST | `/api/mobile/fcm-token` | `Api\MobileController@storeFcmToken` | simpan/update token FCM device |
| POST | `/api/mobile/absensi/auto-checkin` | `Api\MobileController@autoCheckin` | check-in via geofence (sekarang membuat `Laporan`) |

## Konvensi
- Response: biasanya redirect (web) atau JSON (mobile). Tidak ada format envelope standar; mobile mengikuti controller.
- Auth mobile: `Authorization: Bearer <token>` (Personal Access Token, abilities default).
- Validation: FormRequest manual di controller; tidak ada API Resource class (konvensi proyek ini).
- Tidak ada versioning (`/api/v1/*`) — satu versi aktif.
- **Rate limiting API** aktif pada `/api/mobile/*` — 30/menit per user (limiter `mobile`).

## Error model mobile
- 401 (token tidak valid/expired) → aplikasi menampilkan halaman login ulang.
- 422 (validation) → error message per field.
- 429 → terlalu banyak percobaan (RateLimitExceeded), dengan header `Retry-After`. Berlaku untuk `/login`, `/forgot-password`, `/reset-password`, ganti profil/password, dan `/api/mobile/*`.
