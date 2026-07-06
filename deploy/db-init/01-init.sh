#!/bin/sh
# MariaDB ilk kurulum: gömülü uygulama veritabanları + tek uygulama kullanıcısı.
# Yalnız BOŞ db-data volume ile ilk açılışta koşar (docker-entrypoint-initdb.d).
# .sql yerine .sh: parolayı ortamdan ($APPS_DB_PASSWORD) okuyabilmek için.
set -eu

mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" <<SQL
CREATE DATABASE IF NOT EXISTS qr_sistemi   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS fatura_takip CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'apps'@'%' IDENTIFIED BY '${APPS_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON qr_sistemi.*   TO 'apps'@'%';
GRANT ALL PRIVILEGES ON fatura_takip.* TO 'apps'@'%';
FLUSH PRIVILEGES;
SQL
