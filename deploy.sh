#!/bin/bash
set -e

cd /code

# Generar .env desde variables de entorno de EasyPanel
env | grep -E '^(APP_|DB_|MAIL_|SESSION_|CACHE_|QUEUE_|LOG_|BROADCAST_|VAPID_|FACEBOOK_|GOOGLE_|OPENAI_|AI_EVENT_|REDIS_|PUSHER_)' > .env

# Laravel setup — orden importa: clear antes de cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan event:clear

php artisan storage:link --force

# Cachés de producción (mejora TTFB significativamente)
php artisan config:cache    # ~50ms → ~5ms en bootstrap
php artisan route:cache     # ~30ms → ~2ms en routing
php artisan view:cache      # Compila todas las Blade de una vez
php artisan event:cache     # Cache event listeners (Laravel 11+)

echo "Deploy completado: $(date)"
