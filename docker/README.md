# Kubernetes (Kustomize) — mode dev & prod

Docker Compose tidak dipakai lagi. Semua orkestrasi ada di Kubernetes; satu perintah memilih mode:

```bash
./docker/up.sh dev    # namespace genta-dev
./docker/up.sh prod   # namespace genta-prod
```

Kedua mode bisa jalan bersamaan di cluster yang sama (namespace, PVC, dan secret terpisah).

## Struktur

```text
docker/
├── up.sh                    # build image + secret + apply + migrate + wait
├── mediamtx/mediamtx.yml    # konfigurasi media server (referensi lokal)
├── base/                    # manifest bersama: Postgres, web, queue, scheduler, MediaMTX
├── dev/
│   ├── Dockerfile           # php:8.5-cli + artisan serve, composer WITH dev deps
│   └── k8s/                 # overlay namespace genta-dev, image tag dev
└── prod/
    ├── Dockerfile           # php:8.5-fpm + nginx + opcache, composer --no-dev
    ├── nginx/default.conf
    ├── php/opcache.ini
    └── k8s/                 # overlay namespace genta-prod, image tag prod, 2 web & 3 queue
```

## Menjalankan

Prasyarat: `docker`, `kubectl` yang terhubung ke cluster, dan StorageClass default (untuk PVC PostgreSQL).

1. Edit `.env` di root repo — itu sumber kebenaran satu-satunya (termasuk `APP_URL`, `DB_PASSWORD`, `FCM_SERVER_KEY`, `APP_ENV`, `APP_DEBUG`).
2. Jalankan `./docker/up.sh dev` atau `./docker/up.sh prod`. Script akan selalu sinkron `.env` → `app-secret.env` → K8s Secret, termasuk nilai yang sudah disesuaikan untuk klaster (`DB_HOST=genta-postgres`, dst).
3. Bila ingin mengubah nilai, edit `.env` lalu jalankan ulang `./docker/up.sh <mode>`. Secret di klaster akan di-apply ulang otomatis.
4. Aplikasi memakai satu PostgreSQL di dalam cluster (`genta-postgres`) untuk DB, session, queue, dan cache — tanpa Redis. Migration dijalankan otomatis sekali setelah web siap.

Akses dari mesin lokal:

```bash
kubectl -n genta-dev port-forward svc/genta-web 8000:80
kubectl -n genta-dev port-forward svc/genta-mediamtx 8888:8888 8889:8889 9997:9997 8554:8554
```

Ganti `genta-dev` dengan `genta-prod` sesuai mode. `CCTV_MEDIAMTX_API` di dalam cluster memakai `http://genta-mediamtx:9997`; URL HLS/WHEP untuk browser di-set lewat secret (port-forward memakai `127.0.0.1`).

## Operasi

```bash
kubectl -n genta-dev get pods
kubectl -n genta-dev logs -f deployment/genta-web
kubectl -n genta-dev logs -f deployment/genta-queue
kubectl -n genta-dev rollout restart deployment/genta-web

# Hapus seluruh mode (PVC tidak ikut terhapus otomatis)
kubectl delete -k docker/dev/k8s
kubectl delete pvc genta-postgres-data -n genta-dev
```

Aset frontend dibangun di repo (`npm run build`) dan ikut ke dalam image; jalankan ulang build sebelum `docker build` bila CSS/JS berubah. HTTPS/TLS produksi ditangani Ingress atau reverse proxy di depan `svc/genta-web` (port 80). Untuk cluster remote, push image ke registry lalu ubah `images[].newName` di overlay yang dipakai.
