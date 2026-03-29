<?php
/**
 * Zaman Kitchens - Database Maintenance & Synchronization
 * Run this script to ensure your database schema matches the latest admin requirements.
 */
require_once __DIR__ . '/includes/db.php';

// Only allow logged in admins
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access. Please login to the admin panel first.");
}

echo "<h2>Zaman Kitchens - Database Sync</h2>";
echo "Starting synchronization...<br><br>";

try {
    // 1. Categories table: Add parent_id for sub-categories
    $pdo->exec("ALTER TABLE categories ADD COLUMN IF NOT EXISTS parent_id INT DEFAULT NULL AFTER hero_image");
    echo "✅ Checked categories table (parent_id added).<br>";

    // 2. Products table: Ensure all required columns exist
    $cols = [
        "purchase_price DECIMAL(10,2) DEFAULT 0 AFTER price",
        "stock_qty INT DEFAULT 0 AFTER purchase_price",
        "barcode VARCHAR(100) DEFAULT NULL AFTER stock_status",
        "variations JSON DEFAULT NULL AFTER meta_description",
        "specifications JSON DEFAULT NULL AFTER variations",
        "product_type VARCHAR(50) DEFAULT 'physical'",
        "generic_id INT DEFAULT NULL",
        "dosage_form VARCHAR(100) DEFAULT ''",
        "strength VARCHAR(100) DEFAULT ''",
        "registration_number VARCHAR(100) DEFAULT ''",
        "expiry_date DATE DEFAULT NULL",
        "batch_number VARCHAR(100) DEFAULT ''"
    ];
    
    foreach ($cols as $colDef) {
        try {
            $colName = explode(" ", $colDef)[0];
            $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS $colDef");
            echo "✅ Checked products table ($colName added/verified).<br>";
        } catch (Exception $e) { /* Column might already exist */ }
    }

    // 3. Create helper tables if missing (Leads, etc.)
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        phone VARCHAR(20),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Checked leads table.<br>";

    $pdo->exec("CREATE TABLE IF NOT EXISTS price_rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        min_qty INT,
        discount_type ENUM('fixed', 'percentage'),
        value DECIMAL(10,2),
        is_active BOOLEAN DEFAULT 1,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
    echo "✅ Checked price_rules table.<br>";

    echo "<br><strong>🚀 Database Synchronization Complete!</strong><br>";
    echo "You can now safely use all admin features.";

} catch (Exception $e) {
    echo "<br><span style='color:red;'>❌ Error during sync: " . $e->getMessage() . "</span>";
}
