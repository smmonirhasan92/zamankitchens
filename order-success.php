<?php
/**
 * Zaman Kitchens - Order Success Page
 * Works for both: direct checkout (GET id) & Ajax cart checkout (session)
 */
require_once __DIR__ . '/includes/db.php';
session_start();

$order = null;
$orderItems = [];

// Try to get order by ID from URL (direct checkout.php flow)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $order = $stmt->fetch();

        if ($order) {
            $itemStmt = $pdo->prepare("SELECT oi.*, COALESCE(oi.product_name, p.name) as item_name FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
            $itemStmt->execute([$order['id']]);
            $orderItems = $itemStmt->fetchAll();
        }
    } catch(Exception $e) {}
}

// If no valid order found, just redirect home
if (!$order) {
    header("Location: " . SITE_URL);
    exit();
}

$pageTitle = "Order Confirmed! #" . str_pad($order['id'], 5, '0', STR_PAD_LEFT);
include_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16">
    <div class="max-w-xl w-full mx-auto px-4 text-center">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-10">

            <!-- Success Icon -->
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">Order Confirmed! 🎉</h1>
            <p class="text-slate-500 mb-8">
                ধন্যবাদ <strong class="text-slate-900"><?php echo htmlspecialchars($order['customer_name']); ?></strong>!
                আপনার অর্ডার পেয়েছি।
            </p>

            <!-- Order Details -->
            <div class="bg-slate-50 rounded-2xl p-5 text-left space-y-3 mb-6 border border-slate-100">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Order ID</span>
                    <span class="font-black text-slate-900">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                </div>
                <?php if (!empty($orderItems)): ?>
                <div class="border-t border-slate-100 pt-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Items Ordered</span>
                    <?php foreach($orderItems as $item): ?>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600"><?php echo htmlspecialchars($item['item_name'] ?? 'Product'); ?> × <?php echo $item['quantity']; ?></span>
                        <span class="font-bold text-slate-900">৳ <?php echo number_format($item['price'] * $item['quantity']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="flex justify-between text-sm border-t border-slate-100 pt-3">
                    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Total Amount</span>
                    <span class="font-black text-red-600 text-lg">৳ <?php echo number_format($order['total_amount']); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Payment</span>
                    <span class="bg-yellow-100 text-yellow-700 font-black text-xs px-3 py-1 rounded-full">💳 Cash on Delivery</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Status</span>
                    <span class="bg-blue-50 text-blue-700 font-black text-xs px-3 py-1 rounded-full">⏳ Pending Confirmation</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Deliver To</span>
                    <span class="font-medium text-slate-700 text-right max-w-[200px] text-xs leading-relaxed"><?php echo htmlspecialchars($order['address']); ?></span>
                </div>
            </div>

            <p class="text-sm text-slate-500 mb-8 leading-relaxed">
                আমাদের টিম <strong class="text-slate-900"><?php echo htmlspecialchars($order['phone']); ?></strong> নম্বরে
                <strong>২ ঘণ্টার মধ্যে</strong> কল করে ডেলিভারি নিশ্চিত করবে।
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col gap-3">
                <a href="https://wa.me/<?php echo SITE_WHATSAPP; ?>?text=Hi%2C+my+Order+ID+is+%23<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?>+I+want+to+track+my+order." target="_blank"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-4 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-green-200 text-sm uppercase tracking-widest">
                    💬 Track via WhatsApp
                </a>
                <a href="<?php echo SITE_URL; ?>"
                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-black py-4 rounded-2xl transition text-sm uppercase tracking-widest">
                    ← Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
