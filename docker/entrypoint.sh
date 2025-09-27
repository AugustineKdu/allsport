#!/bin/sh

# Wait for any database to be ready (if using external DB)
# sleep 5

# Generate application key if not exists
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Set production environment variables
echo "APP_NAME=AllSports" >> /var/www/html/.env
echo "APP_ENV=production" >> /var/www/html/.env
echo "APP_DEBUG=false" >> /var/www/html/.env
echo "DB_CONNECTION=mysql" >> /var/www/html/.env
echo "DB_HOST=localhost" >> /var/www/html/.env
echo "DB_PORT=3306" >> /var/www/html/.env
echo "DB_DATABASE=allsports" >> /var/www/html/.env
echo "DB_USERNAME=root" >> /var/www/html/.env
echo "DB_PASSWORD=" >> /var/www/html/.env

# Generate app key if not set
php artisan key:generate --no-interaction --force

# Create MySQL database if it doesn't exist
mysql -u root -e "CREATE DATABASE IF NOT EXISTS allsports CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || echo "MySQL not available, using existing database"

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
