#!/bin/bash

# CloudType Start Script (for NPM Build)
echo "🎯 Starting AllSports application..."

# Check if Laravel is properly set up
if [ ! -f .env ]; then
    echo "⚠️ .env not found, copying from example..."
    cp .env.example .env
    php artisan key:generate --force
fi

# Ensure database exists and is properly set up
if [ ! -f database/database.sqlite ]; then
    echo "⚠️ Database not found, creating and setting up..."
    touch database/database.sqlite
    chmod 664 database/database.sqlite 2>/dev/null || true

    # Try to run migrations, but don't fail if SQLite driver is missing
    echo "🔄 Attempting database migrations..."
    if php artisan migrate --force 2>/dev/null; then
        echo "✅ Migrations completed successfully"
        php artisan db:seed --force 2>/dev/null || echo "⚠️ Seeding failed, continuing without seed data"
    else
        echo "⚠️ Database migrations failed (possibly missing SQLite driver), continuing with file-based sessions"
    fi
fi

# Clear caches to prevent 500 errors (critical for NPM builds)
echo "🧹 Clearing all caches..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Ensure storage directories exist and have proper permissions
echo "📁 Setting up storage directories..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views 2>/dev/null || true
mkdir -p bootstrap/cache 2>/dev/null || true

# Final permission check
echo "🔒 Setting final permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod 664 database/database.sqlite 2>/dev/null || true

# Verify setup
echo "✅ Verifying setup..."
echo "Environment file: $([ -f .env ] && echo "✅ Found" || echo "❌ Missing")"
echo "Database file: $([ -f database/database.sqlite ] && echo "✅ Found" || echo "❌ Missing")"
echo "Storage writable: $([ -w storage ] && echo "✅ Yes" || echo "❌ No")"

# Start PHP built-in server for CloudType
# Use environment variable PORT if available, otherwise default to 9000
PORT=${PORT:-9000}
echo "🚀 Starting PHP server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT --no-reload