#!/bin/sh
set -eu

if [ -n "${DATABASE_URL:-}" ]; then
    export DB_CONNECTION=pgsql
    export DB_URL="$(printf '%s' "$DATABASE_URL" | sed 's#^postgres://#pgsql://#; s#^postgresql://#pgsql://#')"
    export DB_SSLMODE=require
fi

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
