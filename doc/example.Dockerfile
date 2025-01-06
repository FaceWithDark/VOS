FROM php:8.3-fpm-alpine

WORKDIR /var/www

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2.8.4 /usr/bin/composer /usr/bin/composer

# Cool trick utilising Docker's caching feature
COPY ../../src/private/composer.* .

# This the default folder to test NGINX working or not but we changed it already at the top
RUN rm -rf html/

COPY ../../src .

RUN docker-php-ext-install \
    pdo \
    pdo_mysql

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

RUN composer dump-autoload --optimize
