# Image prod: dependency tanpa dev, nginx + php-fpm, opcache aktif.
# Build dari root repo: docker build -f docker/prod/Dockerfile -t genta-app:prod .
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader

FROM php:8.5-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        git \
        unzip \
        zip \
        curl \
        libpq-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" mbstring xml zip bcmath pdo_pgsql gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor /app/vendor
COPY . .
COPY docker/prod/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/prod/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN composer dump-autoload --no-dev --optimize

EXPOSE 8000

# php-fpm daemon, nginx tetap di foreground supaya container hidup selama web aktif.
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
