<?php
/**
 * UTF8MB4 Database Migration Script
 * Run once: http://localhost/zamankitchen/run_utf8mb4.php
 * DELETE this file after running!
 */
require_once __DIR__ . '/includes/db.php';

// Security: only run from localhost
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'cli';
if (!in_array($clientIP, $allowedIPs) && $clientIP !== 'cli') {
    http_response_code(403);
    die('Access denied. Run only from localhost.');
}

$results = [];
$errors = [];

function runSQL($pdo, $sql, $label, &$results, &$errors) {
    try {
        $pdo->exec($sql);
        $results[] = "✅ " . $label;
    } catch (Exception $e) {
        $errors[] = "⚠️ " . $label . ": " . $e->getMessage();
    }
}

// Step 1: Set connection encoding
runSQL($pdo, "SET NAMES utf8mb4", "Set NAMES utf8mb4", $results, $errors);
runSQL($pdo, "SET CHARACTER SET utf8mb4", "Set CHARACTER SET utf8mb4", $results, $errors);

// Step 2: Convert all tables
$tables = ['admins', 'categories', 'products', 'orders', 'order_items', 'hero_slides', 'leads', 'users', 'user_addresses', 'price_rules'];
foreach ($tables as $table) {
    // Check if table exists first
    $check = $pdo->query("SHOW TABLES LIKE '$table'")->fetch();
    if ($check) {
        runSQL($pdo, "ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci", 
               "Convert $table to utf8mb4_unicode_ci", $results, $errors);
    } else {
        $results[] = "⏭️ Skipped $table (table does not exist)";
    }
}

// Step 3: Add image_path column to hero_slides if missing
try {
    $cols = $pdo->query("SHOW COLUMNS FROM hero_slides LIKE 'image_path'")->fetch();
    if (!$cols) {
        runSQL($pdo, "ALTER TABLE `hero_slides` ADD COLUMN `image_path` varchar(255) DEFAULT NULL", 
               "Add image_path column to hero_slides", $results, $errors);
        runSQL($pdo, "UPDATE `hero_slides` SET `image_path` = `image` WHERE `image_path` IS NULL", 
               "Sync image → image_path", $results, $errors);
    } else {
        $results[] = "✅ hero_slides.image_path already exists";
    }
} catch(Exception $e) {
    $errors[] = "⚠️ hero_slides check failed: " . $e->getMessage();
}

// Step 4: Add is_active column to hero_slides if missing
try {
    $cols = $pdo->query("SHOW COLUMNS FROM hero_slides LIKE 'is_active'")->fetch();
    if (!$cols) {
        runSQL($pdo, "ALTER TABLE `hero_slides` ADD COLUMN `is_active` tinyint(1) DEFAULT 1", 
               "Add is_active column to hero_slides", $results, $errors);
    } else {
        $results[] = "✅ hero_slides.is_active already exists";
    }
} catch(Exception $e) {
    $errors[] = "⚠️ hero_slides is_active check: " . $e->getMessage();
}

// Step 5: Add old_price to products if missing
try {
    $cols = $pdo->query("SHOW COLUMNS FROM products LIKE 'old_price'")->fetch();
    if (!$cols) {
        runSQL($pdo, "ALTER TABLE `products` ADD COLUMN `old_price` decimal(10,2) DEFAULT NULL", 
               "Add old_price column to products", $results, $errors);
    } else {
        $results[] = "✅ products.old_price already exists";
    }
} catch(Exception $e) {
    $errors[] = "⚠️ products old_price check: " . $e->getMessage();
}

// Step 6: Verify current charsets
$verifyQuery = "SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME";
$tableInfo = $pdo->query($verifyQuery)->fetchAll();

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UTF8MB4 Migration — Zaman Kitchens</title>
    <style>
        body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
        h1 { color: #ef4444; } h2 { color: #94a3b8; font-size: 0.9rem; }
        .ok { color: #34d399; } .err { color: #f87171; } .skip { color: #94a3b8; }
        table { border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.5rem 1rem; border: 1px solid #1e293b; font-size: 0.8rem; }
        th { background: #1e293b; color: #94a3b8; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; }
        .utf8mb4 { background: #064e3b; color: #34d399; }
        .latin1 { background: #7f1d1d; color: #fca5a5; }
        .warn-box { background: #7f1d1d; border: 1px solid #ef4444; padding: 1rem; border-radius: 8px; margin-top: 1rem; }
        .delete-warn { background: #1c1917; border: 2px dashed #ef4444; padding: 1.5rem; border-radius: 8px; margin-top: 2rem; color: #fca5a5; }
    </style>
</head>
<body>
<h1>🔧 UTF8MB4 Migration — Zaman Kitchens</h1>

<h2>Results:</h2>
<?php foreach($results as $r): ?>
<p class="<?php echo strpos($r, '✅') !== false ? 'ok' : (strpos($r, '⏭️') !== false ? 'skip' : ''); ?>"><?php echo htmlspecialchars($r); ?></p>
<?php endforeach; ?>

<?php if (!empty($errors)): ?>
<div class="warn-box">
<h2>⚠️ Warnings / Errors:</h2>
<?php foreach($errors as $e): ?>
<p class="err"><?php echo htmlspecialchars($e); ?></p>
<?php endforeach; ?>
</div>
<?php endif; ?>

<h2 style="margin-top:2rem;">Current Table Charsets:</h2>
<table>
    <tr><th>Table</th><th>Collation</th></tr>
    <?php foreach($tableInfo as $row): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['TABLE_NAME']); ?></td>
        <td>
            <span class="badge <?php echo strpos($row['TABLE_COLLATION'], 'utf8mb4') !== false ? 'utf8mb4' : 'latin1'; ?>">
                <?php echo htmlspecialchars($row['TABLE_COLLATION']); ?>
            </span>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="delete-warn">
    ⚠️ <strong>IMPORTANT:</strong> Migration complete! Please <strong>delete this file</strong> immediately:<br>
    <code>d:\zamankitchen\run_utf8mb4.php</code><br><br>
    Also run this same SQL on your <strong>live cPanel</strong> via phpMyAdmin using:<br>
    <code>d:\zamankitchen\database_utf8mb4_fix.sql</code>
</div>
</body>
</html>
