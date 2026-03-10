<?php
require_once __DIR__ . '/includes/db.php';

echo "<pre>Starting Database Setup...\n";

try {
    // 1. Create Tables
    $sql = file_get_contents(__DIR__ . '/database.sql');
    $pdo->exec($sql);
    echo "Tables created/verified successfully.\n";

    // 2. Verify Categories
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $count = $stmt->fetchColumn();
    echo "Total categories in DB: " . $count . "\n";

    echo "Setup Complete!</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
