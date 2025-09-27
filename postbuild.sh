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

# Ensure public/build directory exists and has proper assets
echo "📦 Checking built assets..."
if [ -d "public/build" ]; then
    echo "✅ Built assets found in public/build"
    ls -la public/build/ || true

    # Check if manifest.json exists
    if [ -f "public/build/manifest.json" ]; then
        echo "✅ Vite manifest.json found"
        echo "Manifest content preview:"
        head -5 public/build/manifest.json || true
    else
        echo "⚠️ Vite manifest.json missing, will use fallback CSS"
    fi
else
    echo "⚠️ No built assets found, will use CDN fallback"
    echo "Creating empty public/build directory for future builds..."
    mkdir -p public/build || true

    # Create a simple manifest for compatibility
    echo '{}' > public/build/manifest.json || true
fi

# Clear view cache to ensure latest compiled assets are used
echo "🧹 Clearing view cache for asset updates..."
php artisan view:clear 2>/dev/null || true

echo "✅ Laravel post-build setup completed!"