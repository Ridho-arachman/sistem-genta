#!/usr/bin/env bash
set -euo pipefail

OUT_DIR="${BACKUP_DIR:-storage/backups}"
mkdir -p "$OUT_DIR"

STAMP="$(date +%Y%m%d-%H%M%S)"
FILE="$OUT_DIR/db-$STAMP.dump"

pg_dump \
    -h "${DB_HOST:-127.0.0.1}" \
    -p "${DB_PORT:-5432}" \
    -U "${DB_USERNAME:-postgres}" \
    -d "${DB_DATABASE:-laravel}" \
    -Fc > "$FILE"

# Simpan 14 hari terakhir.
find "$OUT_DIR" -name 'db-*.dump' -mtime +14 -delete

echo "Backup tersimpan: $FILE"
