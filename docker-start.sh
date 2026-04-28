#!/bin/bash
set -e

cd /app

# Generar .env desde variables de entorno del contenedor.
# Los valores se escriben entre comillas para soportar espacios.
quote_dotenv_value() {
    local value="$1"
    value="${value//\\/\\\\}"
    value="${value//\"/\\\"}"
    printf '"%s"' "$value"
}

printenv | while IFS='=' read -r key value; do
    if [[ "$key" =~ ^(APP_|DB_|MAIL_|SESSION_|CACHE_|QUEUE_|LOG_|BROADCAST_|VAPID_|FACEBOOK_|GOOGLE_|ARCA_|MERCADOPAGO_|STRIPE_|PAYPAL_) ]]; then
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

php artisan storage:link --force
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=8080
