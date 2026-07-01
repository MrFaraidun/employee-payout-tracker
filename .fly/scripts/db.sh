#!/usr/bin/env bash

# Create SQLite database file if it does not exist
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database

# Run migrations and seed database
/usr/bin/php /var/www/html/artisan migrate:fresh --force --no-ansi --seed

# Fix SQLite database ownership and permissions after migration creation
chown -R www-data:www-data /var/www/html/database
chmod 770 /var/www/html/database
chmod 660 /var/www/html/database/database.sqlite
