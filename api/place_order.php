<?php
/**
 * AJAX - Place Order API
 * Fixed: Server-side price validation + Stock deduction
 */
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$items   = json_decode($_POST['items'] ?? '[]', true);

if (empty($name) || empty($phone) || empty($address) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields and add items to your bag.']);
    exit();
}

// Phone Validation (Bangladesh)
if (!preg_match('/^01[3-9]\d{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid Bangladeshi mobile number (01XXXXXXXXX).']);
    exit();
}

try {
    $pdo->beginTransaction();

    $totalAmount = 0;
    $validatedItems = [];

    // ✅ Server-side validation: fetch price from DB, don't trust client
    foreach ($items as $item) {
        $itemId = (int)($item['id'] ?? 0);
        $itemQty = max(1, (int)($item['qty'] ?? 1));

        if (!$itemId) continue;

        $stmt = $pdo->prepare("SELECT id, name, price, stock_qty, stock_status FROM products WHERE id = ?");
        $stmt->execute([$itemId]);
        $product = $stmt->fetch();

        if (!$product) continue;

        $unitPrice = (float)$product['price'];

        // Check wholesale price rule
        $ruleStmt = $pdo->prepare("SELECT * FROM price_rules WHERE product_id = ? AND is_active = 1 AND min_qty <= ? ORDER BY min_qty DESC LIMIT 1");
        $ruleStmt->execute([$itemId, $itemQty]);
        $rule = $ruleStmt->fetch();
        if ($rule) {
            if ($rule['discount_type'] === 'fixed') {
                $unitPrice = (float)$rule['value'];
            } elseif ($rule['discount_type'] === 'percentage') {
                $unitPrice = $unitPrice - ($unitPrice * ((float)$rule['value'] / 100));
            }
        }

        $lineTotal = $unitPrice * $itemQty;
        $totalAmount += $lineTotal;

        $validatedItems[] = [
            'id'           => $product['id'],
            'name'         => $product['name'],
            'qty'          => $itemQty,
            'price'        => $unitPrice,
            'stock_qty'    => (int)$product['stock_qty'],
            'stock_status' => $product['stock_status'],
        ];
    }

    if (empty($validatedItems)) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'No valid products found in your cart.']);
        exit();
    }

    // Insert Order
    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, address, total_amount, status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->execute([$name, $phone, $address, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // Insert Order Items + Deduct Stock
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
    foreach ($validatedItems as $item) {
        $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['qty'], $item['price']]);

        // ✅ Deduct stock quantity
        $newQty = max(0, $item['stock_qty'] - $item['qty']);
        $newStatus = ($newQty <= 0) ? 'Out of Stock' : $item['stock_status'];
        $pdo->prepare("UPDATE products SET stock_qty = ?, stock_status = ? WHERE id = ?")
            ->execute([$newQty, $newStatus, $item['id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId, 'total' => $totalAmount]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Order system error. Please call us directly.']);
}
