# composer.lock butuh PHP >=8.4 (Symfony 8.x); CI dan mesin dev memakai 8.5.
FROM php:8.5-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --no-progress

COPY . .
RUN composer dump-autoload --no-dev --optimize

EXPOSE 8000
