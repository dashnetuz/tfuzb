FROM php:8.2-fpm

ARG PHP_MEMORY_LIMIT
ARG PHP_UPLOAD_MAX_FILESIZE
ARG PHP_POST_MAX_SIZE

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git curl libonig-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath intl

RUN echo "memory_limit=${PHP_MEMORY_LIMIT}" > /usr/local/etc/php/conf.d/custom.ini
RUN echo "upload_max_filesize=${PHP_UPLOAD_MAX_FILESIZE}" >> /usr/local/etc/php/conf.d/custom.ini
RUN echo "post_max_size=${PHP_POST_MAX_SIZE}" >> /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
