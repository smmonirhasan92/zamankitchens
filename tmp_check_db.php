<?php
require_once 'includes/db.php';
$tables = ['products', 'categories'];
foreach($tables as $table) {
    echo "--- TABLE: $table ---\n";
    $stmt = $pdo->query("SHOW CREATE TABLE $table");
    echo $stmt->fetchColumn() . "\n\n";
}
