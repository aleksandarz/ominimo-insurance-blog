FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
        nginx \
        supervisor \
        sqlite \
        oniguruma \
        libzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        oniguruma-dev \
        libzip-dev \
    && docker-php-ext-install bcmath opcache pdo_mysql mbstring zip \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

FROM base AS build

RUN apk add --no-cache git nodejs npm

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN composer dump-autoload --optimize --no-interaction \
    && npm run build \
    && rm -rf node_modules

FROM base AS production

COPY --from=build /var/www/html /var/www/html

RUN mkdir -p \
        storage/logs \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/app/private \
        database/data \
    && touch database/data/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database/data

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
