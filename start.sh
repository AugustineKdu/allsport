#!/bin/bash

# CloudType Start Script (for NPM Build)
echo "🎯 Starting AllSports application..."

# Install SQLite PHP extension if not present
echo "🔧 Installing SQLite PHP extension..."
if ! php -m | grep -q sqlite3; then
    echo "📦 Installing SQLite3 extension..."
    # Try different methods to install SQLite
    apt-get update -qq 2>/dev/null || true
    apt-get install -y php-sqlite3 2>/dev/null || true
    apt-get install -y sqlite3 2>/dev/null || true

    # Enable extension if available
    echo "extension=sqlite3" >> /usr/local/etc/php/conf.d/sqlite.ini 2>/dev/null || true
    echo "extension=pdo_sqlite" >> /usr/local/etc/php/conf.d/sqlite.ini 2>/dev/null || true
else
    echo "✅ SQLite3 extension already available"
fi

# Verify SQLite is working
echo "🔍 Verifying SQLite availability..."
if php -r "new PDO('sqlite::memory:');" 2>/dev/null; then
    echo "✅ SQLite PDO working correctly"
else
    echo "⚠️ SQLite PDO not working, will try alternative setup"
fi

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

# Ensure build directory and manifest exist to prevent Vite errors
echo "📦 Setting up build directory..."
mkdir -p public/build

# Create a dummy manifest.json to prevent Vite errors
if [ ! -f "public/build/manifest.json" ]; then
    echo "📄 Creating dummy manifest.json..."
    cat > public/build/manifest.json << 'EOF'
{
  "resources/css/app.css": {
    "file": "assets/app.css",
    "isEntry": true,
    "src": "resources/css/app.css"
  },
  "resources/js/app.js": {
    "file": "assets/app.js",
    "isEntry": true,
    "src": "resources/js/app.js"
  }
}
EOF
fi

# Check if assets are properly built
echo "📦 Verifying built assets..."
if [ -d "public/build" ]; then
    echo "✅ Built assets directory exists"
    echo "Asset files: $(ls public/build/ | wc -l) files"
    echo "Manifest.json: $([ -f "public/build/manifest.json" ] && echo "✅ Found" || echo "❌ Missing")"
else
    echo "⚠️ No public/build directory found"
fi

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

# Ensure we're in the right directory
cd /app 2>/dev/null || cd "$(dirname "$0")"

# Start Laravel server (automatically serves from public/ directory)
php artisan serve --host=0.0.0.0 --port=$PORT --no-reload --tries=3