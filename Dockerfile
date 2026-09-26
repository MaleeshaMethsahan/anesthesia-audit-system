FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx supervisor curl git libpng-dev libxml2-dev oniguruma-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 80

CMD php artisan config:clear && \
    php artisan migrate --force && \
    php-fpm -D && \
    nginx -g 'daemon off;'