#!/usr/bin/env sh

# Run user scripts, if they exist
for f in /var/www/html/.fly/scripts/*.sh; do
    # Bail out this loop if any script exits with non-zero status code
    bash "$f" -e
done

# ALWAYS enforce correct permissions and ownership on startup to prevent "Permission denied" errors
echo "Enforcing storage, cache, and database permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/storage/database
chmod -R 770 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/storage/database
if [ -f /var/www/html/storage/database/database.sqlite ]; then
    chmod 660 /var/www/html/storage/database/database.sqlite
fi

if [ $# -gt 0 ]; then
    # If we passed a command, run it as root
    exec "$@"
else
    exec supervisord -c /etc/supervisor/supervisord.conf
fi
