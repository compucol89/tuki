#!/bin/bash
set -e

cd /code

# Generar .env desde variables de entorno de EasyPanel
env | grep -E '^(APP_|DB_|MAIL_|SESSION_|CACHE_|QUEUE_|LOG_|BROADCAST_|VAPID_|FACEBOOK_|GOOGLE_|REDIS_|PUSHER_)' > .env

# Laravel setup
php artisan config:clear
php artisan cache:clear
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deploy completado: $(date)"
