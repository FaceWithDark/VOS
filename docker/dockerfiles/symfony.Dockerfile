FROM php:8-fpm-alpine AS base


FROM base AS setup
COPY --link                                         \
    --from=ghcr.io/symfony-cli/symfony-cli:latest   \
    /usr/local/bin/symfony /usr/local/bin/symfony

COPY --from=base \
    ./ /var/www/html

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini


EXPOSE 8000/tcp
ENTRYPOINT [ "symfony", "--allow-all-ip", "local:server:start" ]
