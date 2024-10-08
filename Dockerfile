FROM php:8.3-fpm-alpine@sha256:840f0cb4aa0cbd262ef727a10b04b68ee3cc6b658ef6f15ad8c4dad3e30e583b

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