#!/bin/sh

# Wait for any database to be ready (if using external DB)
# sleep 5

# Generate application key if not exists
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate app key if not set
php artisan key:generate --no-interaction --force

# Run database migrations
php artisan migrate --force --no-interaction

# Seed database if needed
php artisan db:seed --force --no-interaction

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf