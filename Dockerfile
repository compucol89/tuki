FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring xml gd bcmath zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run production

RUN php artisan storage:link --force || true

EXPOSE 8080

COPY docker-start.sh /docker-start.sh
RUN chmod +x /docker-start.sh
CMD ["/docker-start.sh"]
