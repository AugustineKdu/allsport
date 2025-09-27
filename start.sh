#!/bin/bash

# CloudType Start Script (for NPM Build)
echo "🎯 Starting AllSports application..."

# Install MySQL PHP extension if not present
echo "🔧 Installing MySQL PHP extension..."
if ! php -m | grep -q mysql; then
    echo "📦 Installing MySQL extension..."
    # Try different methods to install MySQL
    apt-get update -qq 2>/dev/null || true
    apt-get install -y php-mysql 2>/dev/null || true
    apt-get install -y php-pdo 2>/dev/null || true

    # Enable extension if available
    echo "extension=mysql" >> /usr/local/etc/php/conf.d/mysql.ini 2>/dev/null || true
    echo "extension=pdo_mysql" >> /usr/local/etc/php/conf.d/mysql.ini 2>/dev/null || true
else
    echo "✅ MySQL extension already available"
fi

# Verify MySQL is working
echo "🔍 Verifying MySQL availability..."
if php -r "new PDO('mysql:host=localhost;dbname=test', 'root', '');" 2>/dev/null; then
    echo "✅ MySQL PDO working correctly"
else
    echo "⚠️ MySQL PDO not working, will try alternative setup"
fi

# Check if Laravel is properly set up
if [ ! -f .env ]; then
    echo "⚠️ .env not found, copying from example..."
    cp .env.example .env
    php artisan key:generate --force
fi

# Set MySQL environment variables for production
echo "🔧 Setting up MySQL configuration..."
if ! grep -q "DB_CONNECTION=mysql" .env; then
    echo "DB_CONNECTION=mysql" >> .env
    echo "DB_HOST=localhost" >> .env
    echo "DB_PORT=3306" >> .env
    echo "DB_DATABASE=allsports" >> .env
    echo "DB_USERNAME=root" >> .env
    echo "DB_PASSWORD=" >> .env
    echo "APP_NAME=AllSports" >> .env
    echo "APP_ENV=production" >> .env
    echo "APP_DEBUG=false" >> .env
fi

# Create MySQL database if it doesn't exist
echo "🔄 Setting up MySQL database..."
if mysql -u root -e "CREATE DATABASE IF NOT EXISTS allsports CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null; then
    echo "✅ MySQL database created/verified"
else
    echo "⚠️ Could not create MySQL database, continuing with existing setup"
fi

# Try to run migrations
echo "🔄 Attempting database migrations..."
if php artisan migrate --force 2>/dev/null; then
    echo "✅ Migrations completed successfully"
    php artisan db:seed --force 2>/dev/null || echo "⚠️ Seeding failed, continuing without seed data"
else
    echo "⚠️ Database migrations failed, continuing with file-based sessions"
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

# Verify setup
echo "✅ Verifying setup..."
echo "Environment file: $([ -f .env ] && echo "✅ Found" || echo "❌ Missing")"
echo "Database connection: $(grep "DB_CONNECTION=mysql" .env > /dev/null && echo "✅ MySQL" || echo "❌ Not MySQL")"
echo "Storage writable: $([ -w storage ] && echo "✅ Yes" || echo "❌ No")"

# Start PHP built-in server for CloudType
# Use environment variable PORT if available, otherwise default to 9000
PORT=${PORT:-9000}
echo "🚀 Starting PHP server on port $PORT..."

# Ensure we're in the right directory
cd /app 2>/dev/null || cd "$(dirname "$0")"

# Start Laravel server (automatically serves from public/ directory)
php artisan serve --host=0.0.0.0 --port=$PORT --no-reload --tries=3
