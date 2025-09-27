#!/bin/bash

# Simple CloudType Build Script (No Frontend Build)
echo "🚀 Starting simple Laravel build process..."

# Check PHP version
echo "🐘 PHP Version: $(php --version | head -1)"

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Setup environment
echo "⚙️ Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Clear any existing caches first
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Setup database
echo "🗄️ Setting up database..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi
chmod 664 database/database.sqlite

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "✅ Simple build completed successfully!"