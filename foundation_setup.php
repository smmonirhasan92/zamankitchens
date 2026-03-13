<?php
/**
 * OCE Professional Data Foundation Setup
 */
require_once __DIR__ . '/legacy/includes/db.php';

echo "Starting Professional Data Foundation Setup...\n";

try {
    // 1. Create leads table
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        message TEXT DEFAULT NULL,
        status ENUM('New', 'Contacted', 'Closed') DEFAULT 'New',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Leads table created.\n";

    // 2. Ensure product purchase_price exists
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS purchase_price DECIMAL(10,2) DEFAULT 0.00");
    echo "✅ Products table updated with purchase_price.\n";

    // 3. Ensure price_rules table exists (for Wholesale)
    $pdo->exec("CREATE TABLE IF NOT EXISTS price_rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        min_qty INT NOT NULL,
        discount_type ENUM('fixed', 'percentage') DEFAULT 'fixed',
        value DECIMAL(10,2) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Price rules table ready.\n";

    // 4. Populate mandatory categories if empty
    $catCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($catCount == 0) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)");
        $cats = [
            ['Kitchen Cabinet', 'kitchen-cabinet'],
            ['Kitchen Hood', 'kitchen-hood'],
            ['Gas Stove', 'gas-stove'],
            ['Premium Sink', 'sink']
        ];
        foreach ($cats as $cat) $stmt->execute($cat);
        echo "✅ Categories populated.\n";
    }

    echo "\nSetup Complete!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
