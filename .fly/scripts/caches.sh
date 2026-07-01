#!/usr/bin/env bash

/usr/bin/php /var/www/html/artisan config:cache --no-ansi -q
/usr/bin/php /var/www/html/artisan route:cache --no-ansi -q
/usr/bin/php /var/www/html/artisan view:cache --no-ansi -q

# Fix ownership and permissions for storage and bootstrap/cache to prevent "Permission denied" errors
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 770 /var/www/html/storage /var/www/html/bootstrap/cache
