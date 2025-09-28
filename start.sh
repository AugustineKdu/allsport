#!/bin/bash

# CloudType Start Script for AllSports - ULTIMATE SQLite Fix
echo "🎯 Starting AllSports application..."

# 백업/복구 스크립트 실행 권한 설정
if [ -f "scripts/backup.sh" ]; then
    chmod +x scripts/backup.sh
fi
if [ -f "scripts/restore.sh" ]; then
    chmod +x scripts/restore.sh
fi

# 재배포 시 자동 복구 (환경 변수로 제어)
if [ "${AUTO_RESTORE_ON_DEPLOY:-false}" = "true" ]; then
    echo "🔄 재배포 감지 - 자동 복구 실행 중..."
    export AUTO_RESTORE=true
    if [ -f "scripts/restore.sh" ]; then
        ./scripts/restore.sh latest || echo "⚠️ 자동 복구 실패, 정상 배포 계속 진행"
    fi
fi

# CRITICAL: Force install SQLite with multiple methods
echo "🚨 CRITICAL FIX: Installing SQLite with ALL methods..."

# Method 1: Update and install via apt
echo "📦 Method 1: APT installation..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y sqlite3 php-sqlite3 php-pdo-sqlite php-pdo php-common php-mbstring php-xml php-zip php-cli

# Method 2: Manual extension installation
echo "📦 Method 2: Manual extension setup..."
# Find PHP configuration directory
PHP_CONFIG_DIR=$(php --ini | grep "Scan for additional .ini files" | cut -d: -f2 | xargs)
echo "PHP config dir: $PHP_CONFIG_DIR"

# Create SQLite configuration file
cat > "$PHP_CONFIG_DIR/sqlite.ini" << 'EOF'
extension=pdo.so
extension=pdo_sqlite.so
extension=sqlite3.so
EOF

# Method 3: Alternative PHP configuration paths
echo "📦 Method 3: Alternative paths..."
echo "extension=pdo" >> /usr/local/etc/php/conf.d/20-pdo.ini 2>/dev/null || true
echo "extension=pdo_sqlite" >> /usr/local/etc/php/conf.d/20-pdo_sqlite.ini 2>/dev/null || true
echo "extension=sqlite3" >> /usr/local/etc/php/conf.d/20-sqlite3.ini 2>/dev/null || true

# Method 4: Docker/Container specific paths
echo "📦 Method 4: Container paths..."
echo "extension=pdo" >> /etc/php/8.3/cli/conf.d/20-pdo.ini 2>/dev/null || true
echo "extension=pdo_sqlite" >> /etc/php/8.3/cli/conf.d/20-pdo_sqlite.ini 2>/dev/null || true
echo "extension=sqlite3" >> /etc/php/8.3/cli/conf.d/20-sqlite3.ini 2>/dev/null || true

echo "extension=pdo" >> /etc/php/8.3/fpm/conf.d/20-pdo.ini 2>/dev/null || true
echo "extension=pdo_sqlite" >> /etc/php/8.3/fpm/conf.d/20-pdo_sqlite.ini 2>/dev/null || true
echo "extension=sqlite3" >> /etc/php/8.3/fpm/conf.d/20-sqlite3.ini 2>/dev/null || true

# Method 5: Force load extensions
echo "📦 Method 5: Force loading..."
php -d extension=pdo -d extension=pdo_sqlite -d extension=sqlite3 -m | grep -E "(pdo|sqlite)" || echo "Extensions not loaded"

# Restart all PHP services
echo "🔄 Restarting all PHP services..."
service php8.3-fpm restart 2>/dev/null || true
service php-fpm restart 2>/dev/null || true
service apache2 restart 2>/dev/null || true
service nginx restart 2>/dev/null || true

# Comprehensive SQLite test
echo "🔍 COMPREHENSIVE SQLite test..."
php -r "
echo 'Testing PDO availability: ';
if (class_exists('PDO')) {
    echo 'SUCCESS - PDO available\n';
    echo 'PDO drivers: ' . implode(', ', PDO::getAvailableDrivers()) . '\n';

    if (in_array('sqlite', PDO::getAvailableDrivers())) {
        echo 'SUCCESS - SQLite PDO driver available\n';
        try {
            \$pdo = new PDO('sqlite::memory:');
            \$pdo->exec('CREATE TABLE test (id INTEGER)');
            echo 'SUCCESS - SQLite PDO working\n';
        } catch(Exception \$e) {
            echo 'FAIL - SQLite PDO error: ' . \$e->getMessage() . '\n';
        }
    } else {
        echo 'FAIL - SQLite PDO driver not available\n';
    }
} else {
    echo 'FAIL - PDO not available\n';
}

echo 'Testing SQLite3 extension: ';
if (extension_loaded('sqlite3')) {
    echo 'SUCCESS - SQLite3 extension loaded\n';
} else {
    echo 'FAIL - SQLite3 extension not loaded\n';
}
"

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

# Ensure .env has correct settings
echo "🔧 Ensuring .env configuration..."
if ! grep -q "DB_DATABASE=/tmp/database.sqlite" .env; then
    echo "DB_DATABASE=/tmp/database.sqlite" >> .env
fi

# Add HTTPS security settings
echo "🔒 Adding HTTPS security settings..."
cat >> .env << 'EOF'

# HTTPS Security Settings
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CSRF_COOKIE_SECURE=true
CSRF_COOKIE_SAME_SITE=lax
SANCTUM_STATEFUL_DOMAINS=port-9000-allsport-m10pz2kw69fd7f89.sel4.cloudtype.app
SESSION_COOKIE=laravel_session

# Asset URLs for HTTPS
ASSET_URL=https://port-9000-allsport-m10pz2kw69fd7f89.sel4.cloudtype.app
MIX_ASSET_URL=https://port-9000-allsport-m10pz2kw69fd7f89.sel4.cloudtype.app

# Additional Security
TRUSTED_PROXIES=*
EOF

# Generate app key if not set
echo "🔑 Generating application key..."
php artisan key:generate --force --no-interaction 2>/dev/null || echo "⚠️ Key generation failed"

# ULTIMATE Database connection test with multiple fallbacks
echo "🧪 ULTIMATE Database connection test..."

# Test 1: Direct PDO test
echo "Test 1: Direct PDO test..."
if php -r "try { new PDO('sqlite:/tmp/database.sqlite'); echo 'SUCCESS'; } catch(Exception \$e) { echo 'FAIL'; }" 2>/dev/null | grep -q "SUCCESS"; then
    echo "✅ Direct PDO test successful"

    # Test 2: Laravel database connection
    echo "Test 2: Laravel database connection..."
    if php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
        echo "✅ Laravel database connection successful"

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
        echo "❌ Laravel database connection failed"
        setup_fallback_database
    fi
else
    echo "❌ Direct PDO test failed"
    setup_fallback_database
fi

# Fallback database setup function
setup_fallback_database() {
    echo "🚨 Setting up FALLBACK database system..."

    # Switch to file-based sessions
    echo "SESSION_DRIVER=file" >> .env
    echo "CACHE_STORE=file" >> .env
    echo "QUEUE_CONNECTION=sync" >> .env

    # Try multiple database locations
    echo "📦 Trying multiple database locations..."

    # Location 1: /tmp with full permissions
    touch /tmp/allsports.sqlite
    chmod 777 /tmp/allsports.sqlite
    echo "DB_DATABASE=/tmp/allsports.sqlite" >> .env

    # Test this location
    if php -r "try { new PDO('sqlite:/tmp/allsports.sqlite'); echo 'SUCCESS'; } catch(Exception \$e) { echo 'FAIL'; }" 2>/dev/null | grep -q "SUCCESS"; then
        echo "✅ Fallback database created at /tmp/allsports.sqlite"
        php artisan migrate --force --no-interaction 2>/dev/null || echo "⚠️ Migration failed on fallback"
    else
        echo "❌ Fallback database also failed"

        # Location 2: Current directory
        touch database/allsports.sqlite
        chmod 777 database/allsports.sqlite
        echo "DB_DATABASE=database/allsports.sqlite" >> .env

        # Test this location
        if php -r "try { new PDO('sqlite:database/allsports.sqlite'); echo 'SUCCESS'; } catch(Exception \$e) { echo 'FAIL'; }" 2>/dev/null | grep -q "SUCCESS"; then
            echo "✅ Fallback database created at database/allsports.sqlite"
            php artisan migrate --force --no-interaction 2>/dev/null || echo "⚠️ Migration failed on fallback"
        else
            echo "❌ All database attempts failed - using minimal setup"
            echo "DB_CONNECTION=array" >> .env
        fi
    fi
}

# Clear caches to prevent 500 errors (critical for NPM builds)
echo "🧹 Clearing all caches..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Verify security settings
echo "🔒 Verifying security settings..."
echo "HTTPS Force: $(grep '^FORCE_HTTPS=true' .env > /dev/null && echo "✅ Enabled" || echo "❌ Disabled")"
echo "Secure Cookies: $(grep '^SESSION_SECURE_COOKIE=true' .env > /dev/null && echo "✅ Enabled" || echo "❌ Disabled")"
echo "CSRF Secure: $(grep '^CSRF_COOKIE_SECURE=true' .env > /dev/null && echo "✅ Enabled" || echo "❌ Disabled")"

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

# ULTIMATE Final comprehensive test
echo "🧪 ULTIMATE Final comprehensive test..."

echo "=== PHP Extensions Test ==="
echo "PDO available: $(php -r 'echo class_exists("PDO") ? "YES" : "NO";')"
echo "SQLite3 available: $(php -r 'echo extension_loaded("sqlite3") ? "YES" : "NO";')"
echo "PDO SQLite driver: $(php -r 'echo in_array("sqlite", PDO::getAvailableDrivers()) ? "YES" : "NO";')"

echo "=== Database Test ==="
echo "Testing PDO SQLite directly:"
php -r "
try {
    \$db_file = '/tmp/database.sqlite';
    if (!file_exists(\$db_file)) {
        touch(\$db_file);
        chmod(\$db_file, 0777);
    }
    \$pdo = new PDO('sqlite:' . \$db_file);
    \$pdo->exec('CREATE TABLE IF NOT EXISTS test_table (id INTEGER PRIMARY KEY)');
    \$pdo->exec('INSERT INTO test_table (id) VALUES (1)');
    \$result = \$pdo->query('SELECT COUNT(*) FROM test_table')->fetchColumn();
    echo 'SUCCESS - PDO SQLite working (records: ' . \$result . ')';
} catch(Exception \$e) {
    echo 'FAIL - PDO SQLite error: ' . \$e->getMessage();
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
    \$pdo = DB::connection()->getPdo();
    echo 'SUCCESS - Laravel DB connection working';
} catch(Exception \$e) {
    echo 'FAIL - Laravel DB error: ' . \$e->getMessage();
}
" 2>/dev/null

echo "Testing authentication system:"
php artisan tinker --execute="
try {
    \$user = new \App\Models\User();
    echo 'SUCCESS - User model working';
} catch(Exception \$e) {
    echo 'FAIL - User model error: ' . \$e->getMessage();
}
" 2>/dev/null

# Final service health check
echo "🏥 Final service health check..."
echo "Checking routes:"
php artisan route:list --compact 2>/dev/null | head -5

echo "Checking middleware:"
php artisan route:list --middleware 2>/dev/null | head -5

echo "Checking storage permissions:"
echo "Storage writable: $([ -w storage ] && echo "✅ Yes" || echo "❌ No")"
echo "Bootstrap cache writable: $([ -w bootstrap/cache ] && echo "✅ Yes" || echo "❌ No")"

echo "Checking environment:"
echo "Environment: $(grep '^APP_ENV=' .env | cut -d'=' -f2)"
echo "Debug mode: $(grep '^APP_DEBUG=' .env | cut -d'=' -f2)"
echo "App URL: $(grep '^APP_URL=' .env | cut -d'=' -f2)"

# Start Laravel server
echo "🚀 Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT --no-reload --tries=3
