FROM php:8-fpm-alpine AS base


FROM base AS setup
# Install Symfony CLI to match the tooling usages on development
COPY --link                                         \
    --from=ghcr.io/symfony-cli/symfony-cli:latest   \
    /usr/local/bin/symfony /usr/local/bin/symfony


# Enable 'intl' extension for Symfony Validator usages with the help from a GitHub
# repo that have quick installation scripts to handle any potential missing
# dependencies when installing this extension ourself
# (Ref: https://github.com/mlocati/docker-php-extension-installer).
ADD --chmod=0755                                                                                              \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    /usr/local/bin/

RUN install-php-extensions \
    intl                   \
    xdebug


# Typical codebase structure running instructions here
WORKDIR /var/www/app

COPY --from=base \
    ./ ./

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini


EXPOSE 8000/tcp

# NOTE:
# On production, we don't directly start up the Symfony server like this but wrap
# behind a Traefik reverse proxy setup
ENTRYPOINT [ "symfony", "--allow-all-ip", "local:server:start" ]
