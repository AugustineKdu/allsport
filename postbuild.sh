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
echo "🧹 Clearing all caches and config..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Force remove any cached config files
echo "🔥 Force clearing cached configurations..."
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/packages.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true

# Setup SQLite database
echo "🗄️ Setting up SQLite database..."

# Ensure database directory exists with proper permissions
mkdir -p database
chmod 755 database

# Create SQLite database file with proper permissions
echo "📁 Creating SQLite database file..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# Verify database file was created
if [ -f database/database.sqlite ]; then
    echo "✅ SQLite database file created successfully"
    ls -la database/database.sqlite
else
    echo "❌ Failed to create SQLite database file"
fi

# Test basic SQLite functionality
echo "🔍 Testing SQLite functionality..."
if sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY);" 2>/dev/null; then
    echo "✅ SQLite binary working correctly"
    sqlite3 database/database.sqlite "DROP TABLE IF EXISTS test;" 2>/dev/null || true
else
    echo "⚠️ SQLite binary test failed"
fi

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

# Ensure public/build directory exists and create manifest
echo "📦 Setting up build directory and manifest..."
mkdir -p public/build

# Always create a proper manifest.json
echo "📄 Creating manifest.json..."
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

# Check if real built assets exist
if [ -d "public/build" ] && [ "$(ls -A public/build 2>/dev/null | grep -v manifest.json)" ]; then
    echo "✅ Built assets found in public/build"
    ls -la public/build/ || true
else
    echo "⚠️ Using static CDN assets instead of built files"
fi

echo "✅ Manifest.json ready: $([ -f "public/build/manifest.json" ] && echo "Found" || echo "Missing")"

# Clear view cache to ensure latest compiled assets are used
echo "🧹 Clearing view cache for asset updates..."
php artisan view:clear 2>/dev/null || true

echo "✅ Laravel post-build setup completed!"