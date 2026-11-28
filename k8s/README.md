# Kubernetes (Kustomize) — prod

Prod memakai manifest di sini; dev memakai Docker Compose di [`docker/`](../docker/README.md). Jalankan dari **root repo**.

## Struktur

```text
k8s/
├── base/                  # manifest bersama: Postgres, web, queue, scheduler, MediaMTX
│   └── kustomization.yaml
└── overlays/prod/         # namespace genta-prod, image tag prod, ingress, patch
    ├── kustomization.yaml
    ├── ingress.yaml
    ├── tls-secret.example.yaml
    └── patches/           # web, queue, mediamtx, configmap
```

## Prasyarat

- `kubectl` terhubung ke klaster yang dituju (`kubectl config current-context`).
- StorageClass default aktif untuk PVC Postgres.
- Ingress controller (mis. `ingress-nginx`) dan sertifikat TLS untuk akses eksternal.
- Aset frontend sudah di-build (`npm run build`) sebelum image prod dibangun; prod tidak memakai Vite dev server.

## Setup sekali

```bash
kubectl create namespace genta-prod 2>/dev/null || true

# PVC sengaja di luar kustomize supaya tidak terhapus bersama deployment
kubectl -n genta-prod apply --validate=false -f k8s/base/postgres-pvc.yaml

# Secret dari .env (ulang setiap .env berubah)
kubectl -n genta-prod create secret generic genta-app-secret \
  --from-env-file=.env --dry-run=client -o yaml | kubectl apply --validate=false -f -
```

`.env` adalah sumber kebenaran untuk nilai sensitif (`APP_KEY`, `DB_PASSWORD`, `FCM_SERVER_KEY`, dll.). Nilai non-sensitif yang spesifik klaster (`DB_HOST=genta-postgres`, `DB_DATABASE`, `DB_USERNAME`) diatur di `k8s/base/app-configmap.yaml` dan mengalahkan nilai `.env` saat deploy. Pastikan `DB_PASSWORD` di `.env` cocok dengan data PVC yang sudah ada.

## Deploy

```bash
docker build -f docker/Dockerfile -t genta-app:prod .
kubectl apply --validate=false -k k8s/overlays/prod
kubectl -n genta-prod exec deploy/genta-web -- php artisan migrate --force
kubectl -n genta-prod port-forward svc/genta-web 8001:80
```

Buka `http://localhost:8001`. Untuk CCTV, port-forward juga Control API dan HLS/WHEP:

```bash
kubectl -n genta-prod port-forward svc/genta-mediamtx 8898:8888 8899:8889 9998:9997 8555:8554
```

`CCTV_HLS_BASE` dan `CCTV_WEBRTC_BASE` di `.env` harus memakai port hasil port-forward di atas (`127.0.0.1`) bila preview dibuka dari browser laptop.

## Ingress & TLS

Ubah host `genta.example.com` di `k8s/overlays/prod/ingress.yaml`, lalu buat Secret `genta-tls` dari `tls-secret.example.yaml` (atau `kubectl create secret tls genta-tls --key tls.key --cert tls.crt`). Trafik eksternal masuk lewat ingress ke `svc/genta-web`.

## Operasi

```bash
kubectl -n genta-prod get pods
kubectl -n genta-prod logs -f deployment/genta-web
kubectl -n genta-prod logs -f deployment/genta-queue
kubectl -n genta-prod rollout restart deployment/genta-web

# Hapus deployment tanpa menghapus data DB
kubectl delete -k k8s/overlays/prod
```
