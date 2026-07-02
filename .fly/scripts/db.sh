#!/usr/bin/env bash

# Create SQLite database file if it does not exist
mkdir -p /data
touch /data/database.sqlite
chmod 777 /data/database.sqlite
chmod 777 /data

# Run migrations
/usr/bin/php /var/www/html/artisan migrate --force --no-ansi

# If the database has no users, seed it (clean install)
USER_COUNT=$(/usr/bin/php /var/www/html/artisan tinker --execute="echo App\\Models\\User::count();" 2>/dev/null | tail -n 1)
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" -eq 0 ]; then
    echo "Database is empty. Seeding..."
    /usr/bin/php /var/www/html/artisan db:seed --force --no-ansi
fi

# Fix SQLite database ownership and permissions after migration creation
chown -R www-data:www-data /data
chmod 770 /data
chmod 660 /data/database.sqlite
