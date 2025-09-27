#!/bin/bash

# PostBuild Script for CloudType NPM Build
echo "🚀 Running Laravel post-build setup..."

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
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Setup database
echo "🗄️ Setting up database..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi
chmod 664 database/database.sqlite 2>/dev/null || true

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

echo "✅ Laravel post-build setup completed!"