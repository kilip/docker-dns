FROM php:8.3-fpm-alpine@sha256:14c0faa46fc5c34c662950b607562f67de5c34a5df4d431274fc13ad76744060

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