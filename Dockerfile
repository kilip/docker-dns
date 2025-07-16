FROM php:8.3-fpm-alpine@sha256:22ebf7c37ec7d654f9d4e5a344c8a8abd63d2a2bfc42b43dacb1bfee96e7e515

# hadolint ignore=DL3018
RUN apk add --no-cache --virtual \
	acl \
	file \
	gettext \
	git

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN set -eux; \
	install-php-extensions \
	@composer \
	intl \
	zip \
	pcntl \
	;

WORKDIR /app
VOLUME ["/app/var"]
COPY --chmod=755 docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]
CMD ["/app/bin/start", "-vv"]


# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* symfony.* ./
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress
COPY --link .  ./

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;