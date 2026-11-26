#!/usr/bin/env bash
# Jalankan satu mode: ./docker/up.sh dev  atau  ./docker/up.sh prod
set -euo pipefail

# --- Temukan `kubectl` tanpa path statis, supaya jalan di device mana pun ---
# 1) Coba re-exec lewat login shell agar PATH milik device (bashrc/profile) ikut
#    termuat. Guard `GENTA_LOGIN_RETRY` mencegah infinite loop.
if [[ -z "${GENTA_LOGIN_RETRY:-}" ]] && ! command -v kubectl >/dev/null 2>&1; then
    GENTA_LOGIN_RETRY=1 exec bash -lc 'exec "$@"' bash "$0" "$@"
fi

# 2) Fallback: cari `kubectl` di lokasi umum tanpa path statis per device.
#    Folder ini diturunkan dari env var / command lain, tidak hardcode drive atau user.
if ! command -v kubectl >/dev/null 2>&1; then
    for dir in \
        "$(command -v docker >/dev/null 2>&1 && dirname "$(command -v docker)")" \
        "$(command -v cygpath >/dev/null 2>&1 && cygpath -u "${PROGRAMFILES:-}" 2>/dev/null)/Docker/Docker/resources/bin" \
        "${HOME}/AppData/Local/Microsoft/WinGet/Links" \
        /opt/homebrew/bin /usr/local/bin "${HOME}/.local/bin"
    do
        if [[ -n "$dir" && (-x "$dir/kubectl" || -x "$dir/kubectl.exe") ]]; then
            PATH="$dir:$PATH"
            export PATH
            break
        fi
    done
fi

# 3) Error jelas kalau tetap tidak ketemu, baru di baris pemanggilan asli.
for bin in docker kubectl; do
    if ! command -v "$bin" >/dev/null 2>&1; then
        echo "$bin tidak ditemukan. Install $bin / aktifkan Kubernetes di Docker Desktop." >&2
        exit 1
    fi
done

MODE="${1:-}"
case "$MODE" in
    dev|prod) ;;
    *) echo "Usage: $0 dev|prod" >&2; exit 1 ;;
esac

NAMESPACE="genta-$MODE"
cd "$(dirname "${BASH_SOURCE[0]}")/.."

ENV_FILE="docker/$MODE/k8s/app-secret.env"

# Selalu sinkron dari .env root supaya satu tempat ngubah nilai.
cp .env "$ENV_FILE"

# Sanitasi nilai agar sesuai klaster (bukan 127.0.0.1 / localhost host laptop)
sed -i 's/^DB_HOST=.*/DB_HOST=genta-postgres/' "$ENV_FILE"
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=genta/' "$ENV_FILE"
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=genta/' "$ENV_FILE"
if grep -q '^DB_PASSWORD=$' "$ENV_FILE"; then
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=genta_${MODE}_password/" "$ENV_FILE"
fi
sed -i 's#^CCTV_MEDIAMTX_API=.*#CCTV_MEDIAMTX_API=http://genta-mediamtx:9997#' "$ENV_FILE"
sed -i 's#^CCTV_MEDIAMTX_RTSP=.*#CCTV_MEDIAMTX_RTSP=rtsp://genta-mediamtx:8554#' "$ENV_FILE"
sed -i 's#^CCTV_HLS_BASE=.*#CCTV_HLS_BASE=http://127.0.0.1:8888#' "$ENV_FILE"
sed -i 's#^CCTV_WEBRTC_BASE=.*#CCTV_WEBRTC_BASE=http://127.0.0.1:8889#' "$ENV_FILE"

# Prod: paksa production mode
if [[ "$MODE" == "prod" ]]; then
    sed -i 's/^APP_ENV=.*/APP_ENV=production/' "$ENV_FILE"
    sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' "$ENV_FILE"
fi

kubectl create namespace "$NAMESPACE" --dry-run=client -o yaml | kubectl apply -f -

# PVC dipersiapkan terlebih dahulu dan dikeluarkan dari kustomize
# supaya `kubectl delete -k` tidak ikut menghapus data PostgreSQL.
kubectl apply -f docker/base/postgres-pvc.yaml -n "$NAMESPACE"

# Upsert Secret (selalu sinkron, kalau sudah ada akan di-apply ulang)
kubectl create secret generic genta-app-secret \
    --from-env-file="$ENV_FILE" \
    --dry-run=client -o yaml | kubectl apply -n "$NAMESPACE" -f -

docker build -f "docker/$MODE/Dockerfile" -t "genta-app:$MODE" .
kubectl apply -k "docker/$MODE/k8s"

for deployment in genta-postgres genta-web genta-queue genta-scheduler genta-mediamtx; do
    kubectl -n "$NAMESPACE" rollout status "deployment/$deployment" --timeout=180s
done

kubectl -n "$NAMESPACE" exec deployment/genta-web -- php artisan migrate --force

echo "Selesai ($MODE). Akses:"
echo "  kubectl -n $NAMESPACE port-forward svc/genta-web 8000:80"
echo "  kubectl -n $NAMESPACE port-forward svc/genta-mediamtx 8888:8888 8889:8889 9997:9997 8554:8554"
