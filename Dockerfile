# ==== BUILDER STAGE ── build de PHP + JS ============
FROM php:8.4-cli-alpine AS builder

RUN apk add --no-cache \
    bash git curl nodejs npm \
    freetype-dev libpng-dev libjpeg-turbo-dev libwebp-dev \
    libzip-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring xml gd bcmath zip soap

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# CUIDADO: .dockerignore filtra lo que entra aquí
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run production

# =====================================================

# ==== RUNTIME STAGE ── imagen final =================
FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    bash \
    freetype-dev libpng-dev libjpeg-turbo-dev libwebp-dev \
    libzip-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring xml gd bcmath zip soap

RUN printf '%s\n' \
    'upload_max_filesize=8M' \
    'post_max_size=12M' \
    'memory_limit=256M' \
    > /usr/local/etc/php/conf.d/tukipass-uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar app completa desde builder
COPY --from=builder /app /app

# Limpiar lo que no va en runtime
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

# Link de storage
RUN php artisan storage:link --force || true

EXPOSE 8080

COPY docker-start.sh /docker-start.sh
RUN chmod +x /docker-start.sh

CMD ["/docker-start.sh"]
