#!/bin/sh
set -e

cd /var/www/html

ENV_STORE=storage/app/private/.env

if [ -f "$ENV_STORE" ]; then
    cp "$ENV_STORE" .env
elif [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

cp .env "$ENV_STORE"

php artisan migrate --force

if [ ! -f storage/app/private/.seeded ]; then
    php artisan db:seed --force && touch storage/app/private/.seeded
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache database/data

exec "$@"
