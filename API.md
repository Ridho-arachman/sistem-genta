# API — Mobile & Web (GENTA)

**Status**: aktif · 2026-11-23

Tidak ada `routes/api.php`. Semua endpoint API berada di `routes/web.php`:

## Web (session, untuk browser)
Semua route web terdokumentasi di `routes/web.php`. Ringkas: `/login`, `/password.*`, `/logout`, `/dashboard` (redirect per peran), `admin/*` (role:admin), `pengawas/*` (role:pengawas + `restrict.pengawas`), `manajerial/*` (role:manajerial, read-only), `kepala-dapur/*` (role:kepala_dapur, read-only). POST `/login` dan `/forgot-password` dibatasi `throttle:5,1` (429).

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
- **Rate limiting API belum ada** pada endpoint `/api/mobile/*` — lihat `TASKS.md` (keamanan).

## Error model mobile
- 401 (token tidak valid/expired) → aplikasi menampilkan halaman login ulang.
- 422 (validation) → error message per field.
- 429 → pesan coba lagi (hanya `/login` web yang dibatasi).
