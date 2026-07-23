#!/bin/bash
set -e

cd /app

rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/services.php

# Generar .env desde variables de entorno del contenedor.
# Los valores se escriben entre comillas para soportar espacios.
quote_dotenv_value() {
    local value="$1"
    value="${value//\\/\\\\}"
    value="${value//\"/\\\"}"
    printf '"%s"' "$value"
}

printenv | while IFS='=' read -r key value; do
    if [[ "$key" =~ ^(APP_|DB_|MAIL_|SESSION_|CACHE_|QUEUE_|LOG_|BROADCAST_|VAPID_|FACEBOOK_|GOOGLE_|OPENAI_|AI_EVENT_|ARCA_|MERCADOPAGO_|STRIPE_|PAYPAL_) ]]; then
        printf '%s=' "$key"
        quote_dotenv_value "$value"
        printf '\n'
    fi
done > .env

# Escribir certificados ARCA desde B64 si no hay rutas de archivo
if [ -z "$ARCA_CERT_PATH" ] && { [ -n "$ARCA_CERT_B64" ] || [ -n "$ARCA_CERT_B64_1" ]; }; then
    mkdir -p /app/storage/app/arca
    CERT_B64="${ARCA_CERT_B64:-${ARCA_CERT_B64_1}${ARCA_CERT_B64_2}}"
    echo "$CERT_B64" | base64 -d > /app/storage/app/arca/cert.crt
    echo "ARCA_CERT_PATH=/app/storage/app/arca/cert.crt" >> .env
fi
if [ -z "$ARCA_KEY_PATH" ] && { [ -n "$ARCA_KEY_B64" ] || [ -n "$ARCA_KEY_B64_1" ]; }; then
    mkdir -p /app/storage/app/arca
    KEY_B64="${ARCA_KEY_B64:-${ARCA_KEY_B64_1}${ARCA_KEY_B64_2}}"
    echo "$KEY_B64" | base64 -d > /app/storage/app/arca/private.key
    chmod 600 /app/storage/app/arca/private.key
    echo "ARCA_KEY_PATH=/app/storage/app/arca/private.key" >> .env
fi

php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true
php artisan event:clear || true

# Restaurar imágenes seed del repositorio al volumen persistente.
# EasyPanel monta /app/public/assets/admin/img/ como volumen, por eso los
# assets versionados en esa carpeta pueden quedar ocultos. Copiamos solo
# faltantes para no pisar uploads reales del admin/organizador.
SEED_IMG_SRC="/app/public/assets/admin/img.seed"
if [ -d "$SEED_IMG_SRC" ]; then
    find "$SEED_IMG_SRC" -type f | while read -r seed_file; do
        relative_path="${seed_file#$SEED_IMG_SRC/}"
        target_file="/app/public/assets/admin/img/$relative_path"
        mkdir -p "$(dirname "$target_file")"
        cp -n "$seed_file" "$target_file" 2>/dev/null || true
    done
    touch /app/public/assets/admin/img/.seed-restored
fi

php artisan storage:link --force
php artisan migrate --force

mkdir -p /app/.router-root
php -d upload_max_filesize=8M -d post_max_size=12M -d memory_limit=256M -S 0.0.0.0:8080 -t /app/.router-root /app/docker-router.php
