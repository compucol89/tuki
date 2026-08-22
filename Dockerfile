# ==== PHP EXTENSIONS ── se compilan UNA sola vez =========
# Esta capa solo se invalida si cambia la base PHP o este bloque.
FROM php:8.4-cli-alpine AS php-ext

RUN apk add --no-cache \
        $PHPIZE_DEPS \
        freetype-dev libpng-dev libjpeg-turbo-dev libwebp-dev \
        libzip-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo pdo_mysql mbstring xml gd bcmath zip soap

# ==== BUILDER ── composer + assets JS ====================
FROM php:8.4-cli-alpine AS builder

# Extensiones ya compiladas (sin recompilar)
COPY --from=php-ext /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Runtime libs de las extensiones + toolchain de build de app
RUN apk add --no-cache \
    bash git curl nodejs npm \
    freetype libpng libjpeg-turbo libwebp \
    libzip libxml2 oniguruma

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Cache de dependencias: cambios de favicon/blade NO invalidan estas capas
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader

COPY package.json package-lock.json ./
RUN npm ci

# Código de la app (favicon, blades, etc.) — capa barata
COPY . .

RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && npm run production

# ==== RUNTIME ── imagen final liviana ===================
FROM php:8.4-cli-alpine

COPY --from=php-ext /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-ext /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Solo librerías de runtime (sin -dev)
RUN apk add --no-cache \
    bash \
    freetype libpng libjpeg-turbo libwebp \
    libzip libxml2 oniguruma

RUN printf '%s\n' \
    'upload_max_filesize=8M' \
    'post_max_size=12M' \
    'memory_limit=512M' \
    > /usr/local/etc/php/conf.d/tukipass-uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY --from=builder /app /app

# Seed de img para el volume de Easypanel (favicon/brand versionados en la imagen)
RUN mkdir -p /app/public/assets/admin/img.seed \
    && if [ -f /app/public/brand/icon-32.png ]; then \
         cp -f /app/public/brand/icon-32.png /app/public/assets/admin/img.seed/favicon.png; \
         cp -f /app/public/brand/icon-32.png /app/public/assets/admin/img/favicon.png; \
       fi

RUN rm -rf \
    /app/node_modules \
    /app/.git \
    /app/.gitignore \
    /app/.dockerignore \
    /app/vendor/bin \
    /app/vendor/phpunit \
    /app/vendor/mockery \
    /app/vendor/fakerphp \
    /app/tests \
    /app/storage/debugbar \
    /app/storage/logs/laravel.log

RUN rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/services.php \
    && php artisan storage:link --force || true

EXPOSE 8080

COPY docker-start.sh /docker-start.sh
RUN chmod +x /docker-start.sh

CMD ["/docker-start.sh"]
