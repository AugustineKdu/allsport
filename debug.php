<?php
/**
 * Quick debug script for CloudType deployment
 * Access via: /debug.php
 */

echo "<h1>AllSports Debug Info</h1>";

echo "<h2>PHP Info</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Laravel Path: " . __DIR__ . "<br>";

echo "<h2>Environment Check</h2>";
$envFile = __DIR__ . '/.env';
echo ".env exists: " . (file_exists($envFile) ? "✅ Yes" : "❌ No") . "<br>";

if (file_exists($envFile)) {
    echo ".env readable: " . (is_readable($envFile) ? "✅ Yes" : "❌ No") . "<br>";
}

echo "<h2>Database Check</h2>";
$dbFile = __DIR__ . '/database/database.sqlite';
echo "SQLite file exists: " . (file_exists($dbFile) ? "✅ Yes" : "❌ No") . "<br>";

if (file_exists($dbFile)) {
    echo "SQLite file writable: " . (is_writable($dbFile) ? "✅ Yes" : "❌ No") . "<br>";
    echo "SQLite file size: " . filesize($dbFile) . " bytes<br>";
}

echo "<h2>Directory Permissions</h2>";
$directories = ['storage', 'storage/logs', 'storage/framework', 'bootstrap/cache'];
foreach ($directories as $dir) {
    $path = __DIR__ . '/' . $dir;
    echo "$dir: " . (is_writable($path) ? "✅ Writable" : "❌ Not writable") . "<br>";
}

echo "<h2>Laravel Bootstrap</h2>";
try {
    require_once __DIR__ . '/bootstrap/app.php';
    echo "Laravel bootstrap: ✅ Success<br>";

    // Try to get Laravel app
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "Laravel app creation: ✅ Success<br>";

} catch (Exception $e) {
    echo "Laravel bootstrap error: ❌ " . $e->getMessage() . "<br>";
}

echo "<h2>Composer Autoload</h2>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "Composer autoload: ✅ Success<br>";
} catch (Exception $e) {
    echo "Composer autoload error: ❌ " . $e->getMessage() . "<br>";
}

echo "<h2>Recent Error Logs</h2>";
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    echo "Laravel log exists: ✅ Yes<br>";
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -2000); // Last 2000 characters
    echo "<pre>" . htmlspecialchars($recentLogs) . "</pre>";
} else {
    echo "Laravel log: ❌ Not found<br>";
}
?>