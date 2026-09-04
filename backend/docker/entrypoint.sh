#!/bin/sh
set -e

# Ensure dependencies exist when the bind mount overrides the image vendor/
if [ ! -f /var/www/html/vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

exec "$@"
