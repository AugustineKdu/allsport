#!/bin/bash

# CloudType Build Script
echo "🚀 Starting Laravel build process..."

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📦 Installing NPM dependencies..."
npm install

# Build frontend
echo "🔨 Building frontend assets..."
npm run build

# Setup environment
echo "⚙️ Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Setup database
echo "🗄️ Setting up database..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Cache optimization
echo "⚡ Optimizing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "✅ Build completed successfully!"