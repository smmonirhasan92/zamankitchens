<?php
/**
 * Zaman Kitchens - Comprehensive Repair & Maintenance Tool
 * Purpose: Fix database schema mismatches and folder permission issues.
 */

// --- CONFIGURATION ---
$repair_password = "zamankitchen_fix_2026"; // Security key
require_once __DIR__ . '/includes/db.php';

// --- AUTHENTICATION ---
if (!isset($_GET['pass']) || $_GET['pass'] !== $repair_password) {
    echo "<h1>🔒 Access Denied</h1>";
    echo "<p>Usage: <code>repair_system.php?pass=$repair_password</code></p>";
    exit;
}

echo "<html><head><title>Zaman Kitchens - System Repair</title></head><body style='font-family:sans-serif; line-height:1.6; padding:20px; background:#f4f7f6;'>";
echo "<h1 style='color:#ef233c;'>🚀 Zaman Kitchens - System Repair</h1>";
echo "<p>Initializing repair process... " . date('Y-m-d H:i:s') . "</p><hr>";

try {
    // 1. Categories Table Updates
    echo "<h3>1. Checking Categories Table...</h3>";
    $pdo->exec("ALTER TABLE categories ADD COLUMN IF NOT EXISTS parent_id INT DEFAULT NULL AFTER hero_image");
    echo "✅ Checked <code>parent_id</code> (for sub-categories).<br>";

    // 2. Products Table Updates
    echo "<h3>2. Checking Products Table...</h3>";
    $product_cols = [
        "purchase_price DECIMAL(10,2) DEFAULT 0.00 AFTER price",
        "stock_qty INT DEFAULT 0 AFTER purchase_price",
        "barcode VARCHAR(100) DEFAULT NULL AFTER stock_status",
        "meta_title VARCHAR(255) DEFAULT NULL",
        "meta_description TEXT DEFAULT NULL",
        "variations JSON DEFAULT NULL",
        "specifications JSON DEFAULT NULL",
        "product_type VARCHAR(50) DEFAULT 'physical'",
        "generic_id INT DEFAULT NULL",
        "dosage_form VARCHAR(100) DEFAULT ''",
        "strength VARCHAR(100) DEFAULT ''",
        "registration_number VARCHAR(100) DEFAULT ''",
        "expiry_date DATE DEFAULT NULL",
        "batch_number VARCHAR(100) DEFAULT ''"
    ];

    foreach ($product_cols as $def) {
        $colName = explode(" ", $def)[0];
        try {
            // Check if column exists manually as IF NOT EXISTS doesn't work for all variants
            $check = $pdo->query("SHOW COLUMNS FROM products LIKE '$colName'")->fetch();
            if (!$check) {
                $pdo->exec("ALTER TABLE products ADD COLUMN $def");
                echo "✅ Added missing column: <code>$colName</code><br>";
            } else {
                echo "✔ Column exists: <code>$colName</code><br>";
            }
        } catch (Exception $e) {
            echo "❌ Error adding column '$colName': " . $e->getMessage() . "<br>";
        }
    }

    // 3. Helper Tables
    echo "<h3>3. Checking Helper Tables...</h3>";
    
    // Price Rules
    $pdo->exec("CREATE TABLE IF NOT EXISTS price_rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        min_qty INT,
        discount_type ENUM('fixed', 'percentage'),
        value DECIMAL(10,2),
        is_active BOOLEAN DEFAULT 1,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "✅ Table <code>price_rules</code> verified.<br>";

    // Leads
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        phone VARCHAR(20),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    echo "✅ Table <code>leads</code> verified.<br>";

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
    ) ENGINE=InnoDB;");
    echo "✅ Table <code>hero_slides</code> verified.<br>";

    // 4. Folder Permission Checks
    echo "<h3>4. Verifying Directory Permissions...</h3>";
    $directories = [
        'assets/uploads/ca/',
        'assets/uploads/pr/',
        'assets/uploads/gallery/'
    ];

    foreach ($directories as $dir) {
        $fullPath = __DIR__ . '/' . $dir;
        if (!is_dir($fullPath)) {
            if (mkdir($fullPath, 0777, true)) {
                echo "✅ Created directory: <code>$dir</code><br>";
            } else {
                echo "❌ Failed to create directory: <code>$dir</code><br>";
            }
        }

        if (is_writable($fullPath)) {
            echo "✅ Directory is writable: <code>$dir</code><br>";
        } else {
            // Attempt to fix
            if (chmod($fullPath, 0777)) {
                echo "✅ Permission fixed (0777) for: <code>$dir</code><br>";
            } else {
                echo "❌ Directory NOT writable (Permission Denied): <code>$dir</code><br>";
            }
        }
    }

    echo "<hr><div style='padding:20px; background:#dcfce7; border-radius:12px; color:#166534;'>";
    echo "<h2>🎉 System Repair Successfully Completed!</h2>";
    echo "<p>Database schema and folder permissions have been synchronized with the latest code requirements. You can now test product uploads and sub-category features.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='padding:20px; background:#fee2e2; border-radius:12px; color:#991b1b; margin-top:20px;'>";
    echo "<h2>❌ Critical Error during Repair:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<p style='margin-top:30px; font-size:12px; color:#94a3b8;'>&copy; 2026 Zaman Kitchens Maintenance System</p>";
echo "</body></html>";
