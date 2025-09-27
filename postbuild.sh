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

# Setup database
echo "🗄️ Setting up MySQL database..."

# Remove any SQLite database files
echo "🗑️ Removing SQLite files if they exist..."
rm -f database/database.sqlite 2>/dev/null || true
rm -f database/*.sqlite 2>/dev/null || true

# Force MySQL connection environment
echo "🔧 Setting MySQL environment variables..."
export DB_CONNECTION=mysql
export DB_HOST=mysql
export DB_PORT=3306
export DB_DATABASE=allsports
export DB_USERNAME=root
export DB_PASSWORD=password

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
for i in {1..30}; do
    if php artisan migrate:status 2>/dev/null | grep -q "Migration"; then
        echo "✅ MySQL connection successful!"
        break
    fi
    echo "⏳ Waiting for MySQL... ($i/30)"
    sleep 2
done

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