#!/bin/sh
set -e

PORT="${PORT:-8000}"

# Install dependencies if vendor/ doesn't exist
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --no-dev --optimize-autoloader
fi

php artisan config:cache >/dev/null 2>&1 || true
php artisan route:cache >/dev/null 2>&1 || true
php artisan view:cache >/dev/null 2>&1 || true

php artisan migrate --force


echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
