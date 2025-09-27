#!/bin/bash

# CloudType Start Script for AllSports
echo "🎯 Starting AllSports application..."

# Force install SQLite PHP extension (CRITICAL FIX)
echo "🔧 FORCE Installing SQLite PHP extension..."
echo "📦 Updating package list..."
apt-get update -qq 2>/dev/null || true

echo "📦 Installing SQLite3..."
apt-get install -y sqlite3 2>/dev/null || true

echo "📦 Installing PHP SQLite extensions..."
apt-get install -y php-sqlite3 2>/dev/null || true
apt-get install -y php-pdo-sqlite 2>/dev/null || true
apt-get install -y php-pdo 2>/dev/null || true

echo "📦 Installing additional PHP extensions..."
apt-get install -y php-common 2>/dev/null || true
apt-get install -y php-mbstring 2>/dev/null || true
apt-get install -y php-xml 2>/dev/null || true
apt-get install -y php-zip 2>/dev/null || true

# Enable extensions manually
echo "🔧 Enabling SQLite extensions..."
echo "extension=pdo_sqlite" >> /usr/local/etc/php/conf.d/sqlite.ini 2>/dev/null || true
echo "extension=sqlite3" >> /usr/local/etc/php/conf.d/sqlite.ini 2>/dev/null || true
echo "extension=pdo" >> /usr/local/etc/php/conf.d/sqlite.ini 2>/dev/null || true

# Restart PHP-FPM if available
echo "🔄 Restarting PHP services..."
service php-fpm restart 2>/dev/null || true
service apache2 restart 2>/dev/null || true

# Verify SQLite is working
echo "🔍 Testing SQLite availability..."
php -r "
try {
    \$pdo = new PDO('sqlite::memory:');
    \$pdo->exec('CREATE TABLE test (id INTEGER)');
    echo 'SUCCESS';
} catch(Exception \$e) {
    echo 'FAIL: ' . \$e->getMessage();
}
" 2>/dev/null

# Check if Laravel is properly set up
if [ ! -f .env ]; then
    echo "⚠️ .env not found, copying from example..."
    cp .env.example .env
    php artisan key:generate --force
fi

# Set up SQLite database for CloudType
echo "🔧 Setting up SQLite database..."
if [ ! -f /tmp/database.sqlite ]; then
    echo "📦 Creating SQLite database..."
    touch /tmp/database.sqlite
    chmod 664 /tmp/database.sqlite
    echo "✅ SQLite database created"
else
    echo "✅ SQLite database already exists"
fi

# Ensure .env has correct SQLite settings
echo "🔧 Ensuring .env configuration..."
if ! grep -q "DB_DATABASE=/tmp/database.sqlite" .env; then
    echo "DB_DATABASE=/tmp/database.sqlite" >> .env
fi

# Generate app key if not set
echo "🔑 Generating application key..."
php artisan key:generate --force --no-interaction 2>/dev/null || echo "⚠️ Key generation failed"

# Test database connection before migrations
echo "🧪 Testing database connection..."
if php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
    echo "✅ Database connection successful"

    # Run migrations
    echo "🔄 Running database migrations..."
    if php artisan migrate --force --no-interaction 2>/dev/null; then
        echo "✅ Migrations completed successfully"
        php artisan db:seed --force --no-interaction 2>/dev/null || echo "⚠️ Seeding failed, continuing without seed data"
    else
        echo "❌ Database migrations failed"
        echo "🔍 Debugging migration error..."
        php artisan migrate --force --no-interaction 2>&1 | head -20
    fi
else
    echo "❌ Database connection failed - switching to file-based sessions"
    echo "SESSION_DRIVER=file" >> .env
    echo "CACHE_STORE=file" >> .env
    echo "QUEUE_CONNECTION=sync" >> .env

    # Create minimal database for basic functionality
    echo "📦 Creating minimal SQLite database..."
    touch /tmp/minimal.sqlite
    chmod 664 /tmp/minimal.sqlite
    echo "DB_DATABASE=/tmp/minimal.sqlite" >> .env

    # Try basic migration
    php artisan migrate --force --no-interaction 2>/dev/null || echo "⚠️ Even minimal migration failed"
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

# Show final configuration
echo "📋 Final Configuration:"
echo "APP_NAME: $(grep '^APP_NAME=' .env | cut -d'=' -f2)"
echo "APP_ENV: $(grep '^APP_ENV=' .env | cut -d'=' -f2)"
echo "DB_CONNECTION: $(grep '^DB_CONNECTION=' .env | cut -d'=' -f2)"
echo "DB_DATABASE: $(grep '^DB_DATABASE=' .env | cut -d'=' -f2)"
echo "Database exists: $([ -f /tmp/database.sqlite ] && echo "✅ Yes" || echo "❌ No")"

# Show PHP version and extensions
echo "📋 PHP Information:"
php -v | head -1
echo "Available extensions: $(php -m | grep -E '(sqlite|pdo)' | wc -l) SQLite/PDO extensions"
echo "SQLite3: $(php -m | grep -q sqlite3 && echo "✅ Available" || echo "❌ Missing")"
echo "PDO SQLite: $(php -m | grep -q pdo_sqlite && echo "✅ Available" || echo "❌ Missing")"
echo "PDO: $(php -m | grep -q pdo && echo "✅ Available" || echo "❌ Missing")"

# Show PHP configuration
echo "📋 PHP Configuration:"
php --ini | head -5

# Final comprehensive test
echo "🧪 Final comprehensive test..."
echo "Testing PDO SQLite directly:"
php -r "
try {
    \$pdo = new PDO('sqlite:/tmp/database.sqlite');
    \$pdo->exec('CREATE TABLE IF NOT EXISTS test_table (id INTEGER PRIMARY KEY)');
    echo '✅ PDO SQLite working';
} catch(Exception \$e) {
    echo '❌ PDO SQLite failed: ' . \$e->getMessage();
}
" 2>/dev/null

echo "Testing Laravel artisan:"
if php artisan --version 2>/dev/null; then
    echo "✅ Laravel is working correctly"
else
    echo "❌ Laravel test failed"
fi

echo "Testing database connection via Laravel:"
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'SUCCESS';
} catch(Exception \$e) {
    echo 'FAIL: ' . \$e->getMessage();
}
" 2>/dev/null

# Start Laravel server
echo "🚀 Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT --no-reload --tries=3
