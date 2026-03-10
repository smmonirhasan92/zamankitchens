<?php
require_once __DIR__ . '/includes/db.php';

echo "<pre>Starting Database Setup...\n";

try {
    // 1. Create Tables
    $sql = file_get_contents(__DIR__ . '/database.sql');
    $pdo->exec($sql);
    echo "Tables created/verified successfully.\n";

    // 2. Insert Dummy Products (if table empty)
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $products = [
            ['Kitchen Cabinet', 'kitchen-cabinet-v1', 'High quality premium kitchen cabinet', 45000, 'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=600', 1],
            ['Nano Diamond Sink', 'nano-sink-black', 'Premium black nano diamond kitchen sink', 12500, 'https://images.unsplash.com/photo-1584622781564-1d9876a13d00?w=600', 5],
            ['Matte Black Faucet', 'matte-black-faucet', 'Modern matte black kitchen faucet', 5500, 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=600', 2],
            ['High Power Hood', 'kitchen-hood-v1', 'Powerful suction kitchen hood', 18000, 'https://images.unsplash.com/photo-1590333247377-50a3dec42441?w=600', 3]
        ];
        
        $insert = $pdo->prepare("INSERT IGNORE INTO products (name, slug, description, price, image_url, category_id) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $insert->execute($p);
        }
        echo "Dummy products added.\n";
    }

    echo "Total categories in DB: " . $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn() . "\n";
    echo "Total products in DB: " . $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() . "\n";

    echo "Setup Complete!</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
