<?php
// Temporary PHP info page for debugging CloudType environment

echo "<h1>CloudType PHP Environment Debug</h1>";

echo "<h2>PHP Version</h2>";
echo "<p>" . phpversion() . "</p>";

echo "<h2>Loaded Extensions</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<ul>";
foreach ($extensions as $ext) {
    echo "<li>$ext</li>";
}
echo "</ul>";

echo "<h2>SQLite Check</h2>";
echo "<p>SQLite3 class exists: " . (class_exists('SQLite3') ? 'YES' : 'NO') . "</p>";
echo "<p>PDO SQLite driver: " . (in_array('sqlite', PDO::getAvailableDrivers()) ? 'YES' : 'NO') . "</p>";

echo "<h2>PDO Available Drivers</h2>";
echo "<ul>";
foreach (PDO::getAvailableDrivers() as $driver) {
    echo "<li>$driver</li>";
}
echo "</ul>";

echo "<h2>PHP Configuration</h2>";
echo "<p>extension_dir: " . ini_get('extension_dir') . "</p>";

echo "<h2>File System Check</h2>";
echo "<p>Current directory: " . getcwd() . "</p>";
echo "<p>Database directory exists: " . (is_dir('database') ? 'YES' : 'NO') . "</p>";
echo "<p>Database file exists: " . (file_exists('database/database.sqlite') ? 'YES' : 'NO') . "</p>";
if (file_exists('database/database.sqlite')) {
    echo "<p>Database file size: " . filesize('database/database.sqlite') . " bytes</p>";
    echo "<p>Database file writable: " . (is_writable('database/database.sqlite') ? 'YES' : 'NO') . "</p>";
}

// Test SQLite connection
echo "<h2>SQLite Connection Test</h2>";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "<p style='color: green;'>✅ SQLite PDO connection successful!</p>";

    // Test a simple query
    $stmt = $pdo->query("SELECT sqlite_version()");
    $version = $stmt->fetchColumn();
    echo "<p>SQLite version: $version</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ SQLite PDO connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Laravel Config Test</h2>";
try {
    // Try to load Laravel bootstrap
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    echo "<p>Laravel bootstrap loaded successfully</p>";

    // Try to check database config
    if (function_exists('config')) {
        echo "<p>DB Connection: " . config('database.default', 'Not found') . "</p>";
        echo "<p>DB Database: " . config('database.connections.sqlite.database', 'Not found') . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Laravel bootstrap error: " . $e->getMessage() . "</p>";
}
?>