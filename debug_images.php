<?php
include 'includes/db.php';

echo "--- Hero Slides ---\n";
try {
    $stmt = $pdo->query("SELECT * FROM hero_slides");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']}, Title: {$row['title']}, Path: {$row['image_path']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- Categories ---\n";
try {
    $stmt = $pdo->query("SELECT id, name, hero_image FROM categories LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']}, Name: {$row['name']}, Image: {$row['hero_image']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
