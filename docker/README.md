# Kubernetes (Kustomize) — mode dev & prod

Docker Compose tidak dipakai lagi. Semua orkestrasi ada di Kubernetes. Pastikan context Docker Desktop aktif, lalu pilih mode dengan memilih direktori overlay di `kubectl apply -k`:

```bash
kubectl config use-context docker-desktop

# Dev (namespace genta-dev)
kubectl apply --validate=false -k docker/dev/k8s

# Prod (namespace genta-prod)
kubectl apply --validate=false -k docker/prod/k8s
```

Kedua mode bisa jalan bersamaan di cluster yang sama (namespace, PVC, dan secret terpisah).

## Struktur

```text
docker/
├── mediamtx/mediamtx.yml    # konfigurasi media server (referensi lokal)
├── base/                    # manifest bersama: Postgres, web, queue, scheduler, MediaMTX
├── dev/
│   ├── Dockerfile           # php:8.5-cli + artisan serve, composer WITH dev deps
│   └── k8s/                 # overlay namespace genta-dev, image tag dev
└── prod/
    ├── Dockerfile           # php:8.5-fpm + nginx + opcache, composer --no-dev
    ├── nginx/default.conf
    ├── php/opcache.ini
    └── k8s/
        ├── ingress.yaml     # ingress prod ke svc/genta-web
        ├── kustomization.yaml
        ├── patches/         # patch web (2 replika, php-fpm+nginx), queue (3 replika), mediamtx, configmap (APP_ENV=production, APP_DEBUG=false)
        └── tls-secret.example.yaml
```

## Setup sekali (per namespace)

```bash
# Buat namespace (abaikan kalau sudah ada)
kubectl create namespace genta-dev 2>/dev/null || true
kubectl create namespace genta-prod 2>/dev/null || true

# PVC PostgreSQL (data aman walau klaster dihapus dan dijalankan ulang; sengaja tidak masuk kustomize)
kubectl -n genta-dev apply --validate=false -f docker/base/postgres-pvc.yaml
kubectl -n genta-prod apply --validate=false -f docker/base/postgres-pvc.yaml

# Secret aplikasi dari .env (ulang perintah ini setiap kali .env berubah)
kubectl -n genta-dev create secret generic genta-app-secret \
  --from-env-file=.env --dry-run=client -o yaml | kubectl apply --validate=false -f -
kubectl -n genta-prod create secret generic genta-app-secret \
  --from-env-file=.env --dry-run=client -o yaml | kubectl apply --validate=false -f -
```

`DB_PASSWORD` di `.env` wajib diisi (dev: `genta`) — Postgres menolak password kosong. `CCTV_HLS_BASE`, `CCTV_WEBRTC_BASE`, dan kredensial MediaMTX dibaca dari Secret (`.env`), bukan dari ConfigMap.

## Menjalankan Dev

```bash
# Build image
docker build -f docker/dev/Dockerfile -t genta-app:dev .

# Deploy ke namespace genta-dev
kubectl apply --validate=false -k docker/dev/k8s

# Migrasi DB
kubectl -n genta-dev exec deploy/genta-web -- php artisan migrate --force

# Akses browser (terminal terpisah, biarkan tetap terbuka)
kubectl -n genta-dev port-forward svc/genta-web 8000:80
```

## Menjalankan Prod

```bash
# Build image
docker build -f docker/prod/Dockerfile -t genta-app:prod .

# Deploy ke namespace genta-prod
kubectl apply --validate=false -k docker/prod/k8s

# Migrasi DB
kubectl -n genta-prod exec deploy/genta-web -- php artisan migrate --force

# Akses browser (terminal terpisah, biarkan tetap terbuka)
kubectl -n genta-prod port-forward svc/genta-web 8001:80
```

Aplikasi memakai satu PostgreSQL di dalam cluster (`genta-postgres`) untuk DB, session, queue, dan cache — tanpa Redis. Migration dijalankan manual setelah web siap.

## Akses dari mesin lokal

```bash
# Dev
kubectl -n genta-dev port-forward svc/genta-web 8000:80
kubectl -n genta-dev port-forward svc/genta-mediamtx 8888:8888 8889:8889 9997:9997 8554:8554

# Prod
kubectl -n genta-prod port-forward svc/genta-web 8001:80
kubectl -n genta-prod port-forward svc/genta-mediamtx 8898:8888 8899:8889 9998:9997 8555:8554
```

`CCTV_MEDIAMTX_API` di dalam cluster memakai `http://genta-mediamtx:9997`; URL HLS/WHEP untuk browser di-set lewat secret (port-forward memakai `127.0.0.1`).

## Operasi

```bash
kubectl -n genta-dev get pods
kubectl -n genta-dev logs -f deployment/genta-web
kubectl -n genta-dev logs -f deployment/genta-queue
kubectl -n genta-dev rollout restart deployment/genta-web

# Hapus seluruh mode (PVC sengaja tidak ikut terhapus — data aman)
kubectl delete -k docker/dev/k8s

# PVC harus dihapus manual saja kalau benar-benar ingin membuang data:
# kubectl delete pvc genta-postgres-data -n genta-dev
```

Prod: aset frontend dibangun di repo (`npm run build`) dan ikut ke dalam image; jalankan ulang build sebelum `docker build` bila CSS/JS berubah. Untuk cluster remote, push image ke registry lalu ubah `images[].newName` di overlay yang dipakai.

## Hot reload (dev)

Overlay dev menjalankan image (tanpa mount source) supaya pod tetap stabil di Docker Desktop. Untuk hot reload, jalankan aplikasi langsung dari host; `vite.config.js` sudah mengarah ke `APP_URL=http://127.0.0.1:8000`:

```bash
# Terminal 1
npm run dev

# Terminal 2
php artisan serve
```

Buka `http://127.0.0.1:8000`. Perubahan PHP/Blade/CSS/JS langsung terlihat — **tidak perlu `npm run build` atau `docker build` ulang**. Prod tetap memakai aset hasil build. Host memakai database lokal dari `.env`; kalau ingin memakai DB cluster, jalankan `kubectl -n genta-dev port-forward svc/genta-postgres 5433:5432` lalu set `DB_PORT=5433`, `DB_DATABASE=genta`, `DB_USERNAME=genta` di `.env`. `public/hot` sudah di-gitignore dan dikecualikan dari image prod lewat `.dockerignore`.

## Ingress & TLS produksi

Prod sudah disertakan `ingress.yaml` yang mengarahkan trafik eksternal ke `svc/genta-web`:

```bash
kubectl -n genta-prod apply -f docker/prod/k8s/tls-secret.yaml   # buat dari tls-secret.example.yaml, isi sertifikat asli
kubectl -n genta-prod apply -k docker/prod/k8s
```

Prasyarat: cluster sudah terpasang ingress controller (mis. `ingress-nginx`). Di Docker Desktop, aktifkan dengan `minikube addons enable ingress` atau setara sesuai distribusi cluster.

Sertifikat TLS dibuat manual dan disimpan di Secret `genta-tls` di namespace `genta-prod`. Template ada di `docker/prod/k8s/tls-secret.example.yaml`. Ubah host `genta.example.com` di `ingress.yaml` sesuai domain produksi.

Kalau belum punya ingress controller, gunakan port-forward sementara:

```bash
kubectl -n genta-prod port-forward svc/genta-web 8000:80
```
