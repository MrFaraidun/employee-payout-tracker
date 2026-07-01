#!/usr/bin/env bash

# Create SQLite database file if it does not exist
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database

# Run migrations and seed database
/usr/bin/php /var/www/html/artisan migrate --force --no-ansi --seed
