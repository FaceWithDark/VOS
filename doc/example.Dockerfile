# Dockerfile build for PHP
FROM php:8.3-fpm-alpine3.21

RUN apk -U upgrade

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY './src' '/var/www/html'

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install \
    pdo \
    pdo_mysql

COPY --from=composer:2.8.4 /usr/bin/composer /usr/bin/composer
COPY ../../src/private/composer.* .

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction
RUN composer dump-autoload --optimize


# Dockerfile build for MariaDB
FROM mariadb:lts-noble

RUN apt-get -y update && \
    apt-get -y full-upgrade && \
    apt-get -y autoremove && \
    apt-get -y autoclean


# Dockerfile build for NGINX
FROM nginx:alpine

RUN apk -U upgrade

COPY ./docker/nginx/default.${ENV}.conf /etc/nginx/conf.d/default.conf
