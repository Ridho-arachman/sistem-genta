# Docker (dev) — Docker Compose

Mode dev memakai Docker Compose. Manifest Kubernetes hanya untuk prod dan ada di [`k8s/`](../k8s/README.md).

## Struktur

```text
docker/
├── Dockerfile              # image prod (nginx + php-fpm), dipakai k8s
├── Dockerfile.dev          # image dev (artisan serve), dipakai compose
├── docker-compose.yml      # app, queue, scheduler, vite, postgres, mediamtx
├── mediamtx.yml            # konfigurasi media server
├── nginx/default.conf      # konfigurasi nginx untuk image prod
└── php/opcache.ini         # konfigurasi opcache untuk image prod
```

## Menjalankan

Jalankan semua perintah dari **root repo**. Flag `--env-file .env` wajib supaya interpolasi `${...}` memakai `.env` root (bukan default bawaan compose).

```bash
docker compose --env-file .env -f docker/docker-compose.yml up -d --build
docker compose --env-file .env -f docker/docker-compose.yml exec app sh
```

`exec app sh` membuka shell di container; dari situ jalankan `php artisan migrate --force`, `php artisan db:seed`, dll. Buka `http://<IP LAN di APP_URL>:8000` (mis. `http://192.168.101.6:8000`).

Kalau belum ada `.env`, salin dari `.env.example` dan isi `APP_KEY`, `DB_PASSWORD`, serta kredensial MediaMTX/FCM. Compose menimpa `DB_CONNECTION=pgsql`, `DB_HOST=postgres`, dan `CCTV_MEDIAMTX_API=http://mediamtx:9997` di dalam container; nilai lain tetap dibaca dari `.env`. Postgres memakai nama database/user/password dari `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` di `.env`.

Service yang ikut jalan: `app` (`:8000`), `queue`, `scheduler`, `vite` (`:5173`), `postgres`, dan `mediamtx` (`:8554`, `:8888`, `:8889`, `:9997`). Health check MediaMTX bisa dicek dengan `curl http://127.0.0.1:9997/v3/paths/list`.

## Hot reload

Source repo di-bind-mount ke `/app`. Vite jalan sebagai service `vite` dan menulis `public/hot` ke source yang sama, jadi `@vite` otomatis mengambil aset dari `http://<IP LAN di APP_URL>:5173`. **Tidak perlu `npm run dev` di host** dan tidak perlu `npm run build` / `docker build` ulang saat CSS, Blade, atau JS berubah.

Volume anonim dipakai untuk `/app/vendor` (PHP) dan `/app/node_modules` (Vite) supaya dependency hasil install di Linux tidak ditimpa folder Windows dari host.

Kalau IP WiFi dipakai untuk akses dari Android, tetap ubah `APP_URL` di `.env` dan `capacitor.config.json` sesuai panduan di README root — `vite.config.js` membaca host dari `APP_URL`.

## Operasi

```bash
docker compose --env-file .env -f docker/docker-compose.yml ps
docker compose --env-file .env -f docker/docker-compose.yml logs -f app vite
docker compose --env-file .env -f docker/docker-compose.yml exec app php artisan queue:retry all

# Matikan: data Postgres aman di named volume
docker compose --env-file .env -f docker/docker-compose.yml down

# Buang data DB juga (hati-hati)
docker compose --env-file .env -f docker/docker-compose.yml down -v
```
