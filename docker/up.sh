#!/usr/bin/env bash
# Jalankan satu mode: ./docker/up.sh dev  atau  ./docker/up.sh prod
set -euo pipefail

MODE="${1:-}"
case "$MODE" in
    dev|prod) ;;
    *) echo "Usage: $0 dev|prod" >&2; exit 1 ;;
esac

NAMESPACE="genta-$MODE"
cd "$(dirname "${BASH_SOURCE[0]}")/.."

ENV_FILE="docker/$MODE/k8s/app-secret.env"
EXAMPLE_FILE="docker/$MODE/k8s/app-secret.example.env"

if [[ ! -f "$ENV_FILE" ]]; then
    cp "$EXAMPLE_FILE" "$ENV_FILE"
    echo "Dibuat $ENV_FILE — isi nilai asli sebelum pakai produksi."
fi

if grep -q '^APP_KEY=$' "$ENV_FILE"; then
    key="APP_KEY=base64:$(openssl rand -base64 32)"
    awk -v key="$key" '$0 == "APP_KEY=" { print key; next } { print }' "$ENV_FILE" > "$ENV_FILE.tmp"
    mv "$ENV_FILE.tmp" "$ENV_FILE"
fi

kubectl create namespace "$NAMESPACE" --dry-run=client -o yaml | kubectl apply -f -
if ! kubectl -n "$NAMESPACE" get secret genta-app-secret >/dev/null 2>&1; then
    kubectl -n "$NAMESPACE" create secret generic genta-app-secret --from-env-file="$ENV_FILE"
fi

docker build -f "docker/$MODE/Dockerfile" -t "genta-app:$MODE" .
kubectl apply -k "docker/$MODE/k8s"

for deployment in genta-postgres genta-web genta-queue genta-scheduler genta-mediamtx; do
    kubectl -n "$NAMESPACE" rollout status "deployment/$deployment" --timeout=180s
done

kubectl -n "$NAMESPACE" exec deployment/genta-web -- php artisan migrate --force

echo "Selesai ($MODE). Akses:"
echo "  kubectl -n $NAMESPACE port-forward svc/genta-web 8000:80"
echo "  kubectl -n $NAMESPACE port-forward svc/genta-mediamtx 8888:8888 8889:8889 9997:9997 8554:8554"
