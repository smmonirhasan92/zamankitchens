<?php
require_once __DIR__ . '/includes/db.php';
echo "🔍 Testing Database Connection...\n";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Success! Database is accessible from the web server.\n";
} catch (Exception $e) {
    echo "❌ Failed! Error: " . $e->getMessage() . "\n";
}
