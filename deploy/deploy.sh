#!/usr/bin/env bash
# Deploy / redeploy the application in place.
#
#   bash deploy/deploy.sh --first-run   # first install: keys, DB import, storage link
#   bash deploy/deploy.sh               # later updates: migrate, build, cache
#
# Run from the application root (/var/www/casino) as root or with sudo.
# Expects .env to exist. For --first-run, put the SQL dump next to this script
# as deploy/database.sql (or set SQL_DUMP=/path/to/file.sql).

set -euo pipefail
cd "$(dirname "$0")/.."

FIRST_RUN=0
[[ "${1:-}" == "--first-run" ]] && FIRST_RUN=1

PHP="${PHP:-php}"
SQL_DUMP="${SQL_DUMP:-deploy/database.sql}"

[[ -f .env ]] || { echo "Missing .env (copy deploy/.env.production.example)"; exit 1; }

echo "==> maintenance mode"
$PHP artisan down --retry=30 || true

echo "==> composer"
composer install --no-dev --optimize-autoloader --no-interaction

if [[ $FIRST_RUN -eq 1 ]]; then
  echo "==> first run: keys"
  grep -q '^APP_KEY=.\+' .env || $PHP artisan key:generate --force
  grep -q '^JWT_SECRET=.\+' .env || $PHP artisan jwt:secret --force

  if [[ -f "$SQL_DUMP" ]]; then
    echo "==> importing $SQL_DUMP"
    DB_NAME=$(grep '^DB_DATABASE=' .env | cut -d= -f2)
    DB_USER=$(grep '^DB_USERNAME=' .env | cut -d= -f2)
    DB_PASS=$(grep '^DB_PASSWORD=' .env | cut -d= -f2)
    mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_DUMP"
  else
    echo "    (no dump at $SQL_DUMP, skipping import)"
  fi

  echo "==> storage link"
  $PHP artisan storage:link || true
fi

echo "==> migrations (catalogue trim + NOWPayments columns run here)"
$PHP artisan migrate --force

if [[ -d node_modules ]] || command -v npm >/dev/null; then
  echo "==> frontend build"
  npm ci --no-audit --no-fund 2>/dev/null || npm install --no-audit --no-fund
  npm run build
fi

echo "==> caches"
$PHP artisan optimize:clear
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan event:cache
# Not view:cache. The admin panel's forms are Livewire components, and a
# request that lands while the compiled views are being rewritten renders
# against a half-written cache and dies with "Undefined variable $errors".
# Blade compiles each view once on first use anyway, so the gain was noise.
$PHP artisan view:clear
$PHP artisan filament:upgrade || true

echo "==> permissions"
chown -R www-data:www-data .
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

echo "==> scheduler cron (idempotent)"
CRON_LINE="* * * * * cd $(pwd) && $PHP artisan schedule:run >> /dev/null 2>&1"
( crontab -u www-data -l 2>/dev/null | grep -v 'schedule:run' ; echo "$CRON_LINE" ) | crontab -u www-data -

echo "==> back online"
$PHP artisan up
echo "Deploy finished."
