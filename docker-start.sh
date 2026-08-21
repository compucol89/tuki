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

# Brand favicon: SIEMPRE sobrescribir en el volume (no es upload de usuario).
# El volume oculta el archivo de la imagen; sin esto Google/Meta quedan con el PNG viejo.
BRAND_FAVICON_SRC="/app/public/brand/icon-32.png"
BRAND_FAVICON_DST="/app/public/assets/admin/img/favicon.png"
if [ -f "$BRAND_FAVICON_SRC" ]; then
    mkdir -p "$(dirname "$BRAND_FAVICON_DST")"
    cp -f "$BRAND_FAVICON_SRC" "$BRAND_FAVICON_DST"
    # También alinear roots clásicos (por si un crawler pide /favicon.ico sin mirar <link>)
    if [ -f /app/public/brand/favicon.ico ]; then
        cp -f /app/public/brand/favicon.ico /app/public/favicon.ico
    fi
    if [ -f /app/public/brand/icon-32.png ]; then
        cp -f /app/public/brand/icon-32.png /app/public/favicon-32x32.png
    fi
    if [ -f /app/public/brand/icon-192.png ]; then
        cp -f /app/public/brand/icon-192.png /app/public/android-chrome-192x192.png
    fi
    if [ -f /app/public/brand/apple-touch-icon.png ]; then
        cp -f /app/public/brand/apple-touch-icon.png /app/public/apple-touch-icon.png
    fi
fi

php artisan storage:link --force
php artisan migrate --force
php artisan view:cache || true
php artisan event:cache || true

# Scheduler de Laravel (reconcile de stock H4, reset de views, futuros schedulados).
# EasyPanel no tiene cron por servicio: crond dentro del contenedor (busybox alpine).
mkdir -p /app/storage/logs
printf '*/1 * * * * php /app/artisan schedule:run >> /app/storage/logs/cron.log 2>&1\n' > /etc/crontabs/root
crond -b -l 2 || true

# === Scheduler de Laravel (reconcile de stock H4, reset de views, futuros schedulados).
# EasyPanel no tiene cron por servicio: crond dentro del contenedor (busybox alpine).
mkdir -p /app/storage/logs
printf '*/1 * * * * php /app/artisan schedule:run >> /app/storage/logs/cron.log 2>&1\n' > /etc/crontabs/root
crond -b -l 2 || true

# === Queue workers ===
# Los workers de cola corren en el SERVICIO DEDICADO "worker" de EasyPanel (comando
# queue:work con flags por cola). El contenedor web NO procesa colas: se evita la
# duplicación de workers y se aísla el procesamiento pesado (AI) del tráfico web.
# (Incidente 2026-08-11: ver docs/ops/worker-incident-2026-08-11.md)

mkdir -p /app/.router-root
# zlib.output_compression: gzip del HTML (los estáticos los comprime docker-router.php vía ob_gzhandler)
php -d upload_max_filesize=8M -d post_max_size=12M -d memory_limit=512M \
    -d zlib.output_compression=1 -d zlib.output_compression_level=5 \
    -d display_errors=0 -d log_errors=1 \
    -S 0.0.0.0:8080 -t /app/.router-root /app/docker-router.php &

# Supervisión: si muere un worker o el web server, el contenedor termina
# y EasyPanel lo reinicia completo (self-healing). Requiere bash >= 4.3
# (alpine instala bash 5.x — verificado en el Dockerfile).
wait -n
