#!/usr/bin/env bash
# Barlas Trailer — günlük yedek.
#
# writable/ altındaki HER ŞEY işin gerçek verisidir: lead'ler, teklifler,
# AI soruları, ajan durumları ve panel kullanıcılarının olduğu SQLite.
# Bunlar git'te DEĞİLDİR (bilerek) — tek kopya bu yedeklerdir.
#
# Kurulum: deploy/crontab içindeki satır bunu her gece 04:30'da çalıştırır.
# Geri dönüş: tar -xzf /var/backups/barlas/writable-<tarih>.tar.gz -C $APP_DIR
set -euo pipefail

APP_DIR="${APP_DIR:-/opt/barlas-trailer}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/barlas}"
KEEP_DAYS="${KEEP_DAYS:-14}"

mkdir -p "$BACKUP_DIR"
STAMP="$(date +%Y%m%d-%H%M%S)"

tar -czf "$BACKUP_DIR/writable-$STAMP.tar.gz" \
    --exclude='writable/cache' \
    --exclude='writable/session' \
    --exclude='writable/debugbar' \
    -C "$APP_DIR" writable

# Saklama süresi dolan yedekleri temizle
find "$BACKUP_DIR" -name 'writable-*.tar.gz' -mtime "+$KEEP_DAYS" -delete

echo "Yedek alındı: $BACKUP_DIR/writable-$STAMP.tar.gz"
