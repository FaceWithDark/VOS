FROM php:8-fpm-alpine AS base


FROM base AS setup
# Install Symfony CLI to match the tooling usages on development
COPY --link											\
	--from=ghcr.io/symfony-cli/symfony-cli:latest	\
	/usr/local/bin/symfony /usr/local/bin/symfony


# Enable 'intl' extension for Symfony Validator usages with the help from a GitHub
# repo that have quick installation scripts to handle any potential missing
# dependencies when installing this extension ourself
# (Ref: https://github.com/mlocati/docker-php-extension-installer).
ADD --chmod=0755																							  \
	https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
	/usr/local/bin/

RUN install-php-extensions \
	intl				   \
	xdebug				   \
	pdo_pgsql


# Typical codebase structure running instructions here
WORKDIR /var/www/app

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Prevent the reinstallation of vendors at every changes in the source code
COPY --from=base ./composer.* ./symfony.* ./

# Then copy the rest of the source code
COPY --from=base ./ ./


EXPOSE 8000/tcp

ENTRYPOINT [ "sh", "./migrations/Utility/Migration.sh" ]
