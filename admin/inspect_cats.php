<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "--- RAW DATA FROM DATABASE ---\n";
    foreach ($cats as $c) {
        echo "ID: " . $c['id'] . " | Name: " . $c['name'] . " | Hex: " . bin2hex($c['name']) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
