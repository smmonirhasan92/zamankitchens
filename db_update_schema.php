<?php
require 'includes/db.php';

try {
    // Add missing columns if they don't exist
    $pdo->exec("ALTER TABLE products ADD COLUMN meta_title VARCHAR(255) NULL");
    echo "Added meta_title<br>";
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN meta_description TEXT NULL");
    echo "Added meta_description<br>";
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN variations JSON NULL");
    echo "Added variations<br>";
} catch (Exception $e) {}

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN specifications JSON NULL");
    echo "Added specifications<br>";
} catch (Exception $e) {}

echo "Database Update Complete!";
?>
