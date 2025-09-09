# ============================== #
#       PHP DOCKERFILE SETUP     #
# ============================== #

FROM php:8.3-fpm-alpine3.21

RUN apk -U upgrade

COPY ./src /var/www/html

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN docker-php-ext-install \
    pdo \
    pdo_mysql


# ============================== #
#   MARIADB DOCKERFILE SETUP     #
# ============================== #

FROM mariadb:lts-noble

RUN apt-get -y update && \
    apt-get -y full-upgrade && \
    apt-get -y autoremove && \
    apt-get -y autoclean


# ============================== #
#     NGINX DOCKERFILE SETUP     #
# ============================== #

FROM nginx:alpine

RUN apk -U upgrade

COPY ./src /var/www/html
RUN rm -rf /var/www/html/private

# 3 available options for build stage are: [dev], [stage], [prod]
COPY ./docker/nginx/nginx.<build-stage>.conf /etc/nginx/nginx.conf
