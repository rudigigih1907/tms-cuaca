#!/bin/bash

set -e

DATE=$(date +"%Y-%m-%d_%H-%M-%S")

BACKUP_DIR="/backup/database"

FILE="${BACKUP_DIR}/${MYSQL_DATABASE}_${DATE}.sql.gz"

mkdir -p "${BACKUP_DIR}"

echo "========================================"
echo "START DATABASE BACKUP"
echo "========================================"

echo "Database : ${MYSQL_DATABASE}"
echo "Host     : ${MYSQL_HOST}"
echo "Date     : ${DATE}"
echo "File     : ${FILE}"

export MYSQL_PWD="${MYSQL_PASSWORD}"

mysqldump \
    --host="${MYSQL_HOST}" \
    --user="${MYSQL_USER}" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --hex-blob \
    --no-tablespaces \
    "${MYSQL_DATABASE}" \
    | gzip > "${FILE}"

DUMP_STATUS=${PIPESTATUS[0]}

unset MYSQL_PWD

if [ "${DUMP_STATUS}" -ne 0 ]; then
    echo "Database backup FAILED"
    rm -f "${FILE}"
    exit 1
fi

if [ ! -s "${FILE}" ]; then
    echo "Database backup FAILED: file is empty"
    rm -f "${FILE}"
    exit 1
fi

echo "Database backup SUCCESS"

ls -lh "${FILE}"

echo ""
echo "Cleaning old database backups..."

find "${BACKUP_DIR}" \
    -type f \
    -name "*.sql.gz" \
    -mtime +"${BACKUP_RETENTION_DAYS}" \
    -delete

echo "Database backup completed."

exit 0