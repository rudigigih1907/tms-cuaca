#!/bin/bash

set -e

DATE=$(date +"%Y-%m-%d_%H-%M-%S")

BACKUP_DIR="/backup/uploads"

FILE="${BACKUP_DIR}/uploads_${DATE}.tar.gz"

mkdir -p "${BACKUP_DIR}"

echo "========================================"
echo "START UPLOAD BACKUP"
echo "========================================"

echo "Source : /data/uploads"
echo "Date   : ${DATE}"
echo "File   : ${FILE}"

echo ""

if [ ! -d "/data/uploads" ]; then
    echo "ERROR: /data/uploads does not exist"
    exit 1
fi

tar \
    -czf "${FILE}" \
    -C /data \
    uploads

echo ""

if [ -f "${FILE}" ]; then
    echo "Upload backup SUCCESS"
    ls -lh "${FILE}"
else
    echo "Upload backup FAILED"
    exit 1
fi

echo ""
echo "Cleaning old upload backups..."

find "${BACKUP_DIR}" \
    -type f \
    -name "*.tar.gz" \
    -mtime +"${BACKUP_RETENTION_DAYS}" \
    -delete

echo "Upload backup completed."

exit 0