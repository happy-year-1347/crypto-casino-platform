#!/usr/bin/env bash
# Nightly backup of the database and uploaded files. Keeps 14 days.
#
#   crontab -e  ->  30 3 * * * /var/www/casino/deploy/backup.sh >> /var/log/casino-backup.log 2>&1
#
# Copy the backup folder off the server (rclone/scp) for real disaster recovery.

set -euo pipefail
APP_DIR="${APP_DIR:-/var/www/casino}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/casino}"
KEEP_DAYS="${KEEP_DAYS:-14}"

cd "$APP_DIR"
DB_NAME=$(grep '^DB_DATABASE=' .env | cut -d= -f2)
DB_USER=$(grep '^DB_USERNAME=' .env | cut -d= -f2)
DB_PASS=$(grep '^DB_PASSWORD=' .env | cut -d= -f2)
STAMP=$(date +%Y%m%d-%H%M%S)

mkdir -p "$BACKUP_DIR"
mysqldump -u"$DB_USER" -p"$DB_PASS" --single-transaction --quick "$DB_NAME" | gzip > "$BACKUP_DIR/db-$STAMP.sql.gz"
tar -czf "$BACKUP_DIR/storage-$STAMP.tar.gz" storage/app/public .env

find "$BACKUP_DIR" -type f -mtime +"$KEEP_DAYS" -delete
echo "$(date -Is) backup ok: db-$STAMP.sql.gz storage-$STAMP.tar.gz"
