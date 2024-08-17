#!/bin/sh
set -e

if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
	composer install --prefer-dist --no-progress --no-interaction
fi

php bin/console doctrine:schema:update --force

exec docker-php-entrypoint "$@"
