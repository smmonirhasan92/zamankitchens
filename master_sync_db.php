<?php
/**
 * Zaman Kitchens - Master Database Sync Script
 * This script ensures the database schema matches the latest application requirements.
 */
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain');
echo "🚀 Starting Master Database Synchronization...\n\n";

try {
    // 1. Update Categories Table for Sub-categories
    echo "Checking 'categories' table...\n";
    $stmt = $pdo->query("DESCRIBE categories");
    $existingCols = array_column($stmt->fetchAll(), 'Field');

    if (!in_array('parent_id', $existingCols)) {
        echo "- Adding 'parent_id' to categories...\n";
        $pdo->exec("ALTER TABLE categories ADD COLUMN parent_id INT DEFAULT NULL");
        $pdo->exec("ALTER TABLE categories ADD CONSTRAINT fk_category_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL");
    }
    if (!in_array('hero_image', $existingCols)) {
        echo "- Adding 'hero_image' to categories...\n";
        $pdo->exec("ALTER TABLE categories ADD COLUMN hero_image VARCHAR(255) DEFAULT NULL");
    }

    // 2. Update Products Table for Inventory, Barcode, SEO and Metadata
    echo "\nChecking 'products' table...\n";
    $stmt = $pdo->query("DESCRIBE products");
    $existingCols = array_column($stmt->fetchAll(), 'Field');

    $colsToAdd = [
        'stock_qty' => "INT DEFAULT 0",
        'barcode' => "VARCHAR(100) DEFAULT NULL",
        'purchase_price' => "DECIMAL(10,2) DEFAULT 0.00",
        'meta_title' => "VARCHAR(255) DEFAULT NULL",
        'meta_description' => "TEXT DEFAULT NULL",
        'variations' => "JSON DEFAULT NULL",
        'specifications' => "JSON DEFAULT NULL",
        'is_featured' => "BOOLEAN DEFAULT FALSE",
        'product_type' => "VARCHAR(50) DEFAULT 'physical'",
        'generic_id' => "INT DEFAULT NULL",
        'dosage_form' => "VARCHAR(100) DEFAULT NULL",
        'strength' => "VARCHAR(100) DEFAULT NULL",
        'registration_number' => "VARCHAR(100) DEFAULT NULL",
        'expiry_date' => "DATE DEFAULT NULL",
        'batch_number' => "VARCHAR(100) DEFAULT NULL",
        'main_image' => "VARCHAR(255) DEFAULT NULL" // Ensuring main_image exists for consistency
    ];

    foreach ($colsToAdd as $col => $def) {
        if (!in_array($col, $existingCols)) {
            echo "- Adding '$col' to products...\n";
            $pdo->exec("ALTER TABLE products ADD COLUMN $col $def");
        }
    }

    // 3. Ensure other tables exist
    echo "\nEnsuring helper tables exist...\n";
    
    // Hero Slides
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) DEFAULT NULL,
        subtitle TEXT DEFAULT NULL,
        image_path VARCHAR(255) NOT NULL,
        button_text VARCHAR(50) DEFAULT 'Shop Now',
        button_link VARCHAR(255) DEFAULT '#',
        order_index INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Leads / Inquiries (if missing)
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(20),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    
    // Price Rules (from product-edit.php)
    $pdo->exec("CREATE TABLE IF NOT EXISTS price_rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        min_qty INT NOT NULL,
        discount_type ENUM('fixed', 'percentage') NOT NULL,
        value DECIMAL(10,2) NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    echo "\n✅ Database Synchronization Complete!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
