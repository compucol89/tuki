FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    bash git curl nodejs npm \
    freetype-dev libpng-dev libjpeg-turbo-dev \
    libzip-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring xml gd bcmath zip soap

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run production
RUN php artisan storage:link --force || true

EXPOSE 8080

COPY docker-start.sh /docker-start.sh
RUN chmod +x /docker-start.sh
CMD ["/docker-start.sh"]
