#!/bin/bash
set -e

cd /app

# Generar .env desde variables de entorno del contenedor
printenv | grep -E '^(APP_|DB_|MAIL_|SESSION_|CACHE_|QUEUE_|LOG_|BROADCAST_|VAPID_|FACEBOOK_|GOOGLE_)' > .env

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=8080
