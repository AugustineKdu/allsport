#!/bin/bash

# CloudType Start Script
echo "🎯 Starting AllSports application..."

# Final permission check
chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null || true
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 664 database/database.sqlite 2>/dev/null || true

# Start PHP built-in server for CloudType
echo "🚀 Starting PHP server on port 8080..."
php artisan serve --host=0.0.0.0 --port=8080