FROM php:8.1-fpm

MAINTAINER Ales Poljansek <poljansek@gmail.com>

RUN apt-get update \
    && apt-get install -y zlib1g-dev libpq-dev libicu-dev libxml2-dev libcurl4-openssl-dev \
    pkg-config libssl-dev libzip-dev zlib1g-dev libxpm-dev libvpx-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev

RUN docker-php-ext-configure gd --enable-gd --prefix=/usr --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-install -j$(nproc) pdo \
    && docker-php-ext-install -j$(nproc) pdo_mysql 

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer