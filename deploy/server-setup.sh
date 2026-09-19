#!/usr/bin/env bash
# One-time server preparation for Ubuntu 22.04 / 24.04.
# Run as root:  bash deploy/server-setup.sh
#
# Installs nginx, PHP 8.2 + extensions, MariaDB, Node 20, composer, certbot,
# creates the database and the /var/www/casino folder.

set -euo pipefail

DB_NAME="${DB_NAME:-casino}"
DB_USER="${DB_USER:-casino}"
DB_PASS="${DB_PASS:-$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)}"
APP_DIR="${APP_DIR:-/var/www/casino}"

echo "==> packages"
apt-get update
apt-get install -y software-properties-common curl git unzip ufw
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install -y nginx mariadb-server certbot python3-certbot-nginx \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl \
  php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-soap php8.2-readline

echo "==> node 20 + composer"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs
if ! command -v composer >/dev/null; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> php-fpm tuning"
sed -i 's/^;\?memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
sed -i 's/^;\?upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.2/fpm/php.ini
sed -i 's/^;\?post_max_size = .*/post_max_size = 64M/' /etc/php/8.2/fpm/php.ini
sed -i 's/^;\?expose_php = .*/expose_php = Off/' /etc/php/8.2/fpm/php.ini
systemctl restart php8.2-fpm

echo "==> database"
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"

echo "==> app folder"
mkdir -p "${APP_DIR}"
chown -R www-data:www-data "${APP_DIR}"

echo "==> firewall"
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

cat <<EOF

Server is ready.

  DB name:     ${DB_NAME}
  DB user:     ${DB_USER}
  DB password: ${DB_PASS}
  App folder:  ${APP_DIR}

Next: upload the release zip to ${APP_DIR}, copy deploy/.env.production.example
to .env and fill it in, then run: bash deploy/deploy.sh --first-run
EOF
