<?php
/**
 * Zaman Kitchens - Admin Orders Management
 */
session_start();
require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit(); }

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['Pending', 'Processing', 'Delivered', 'Cancelled'];
    if (in_array($_POST['status'], $allowed)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$_POST['status'], $_POST['order_id']]);
    }
    header("Location: orders.php");
    exit();
}

// Filter by status
$statusFilter = $_GET['status'] ?? '';
$allowed = ['Pending', 'Processing', 'Delivered', 'Cancelled'];
$sql = "SELECT * FROM orders";
$params = [];
if ($statusFilter && in_array($statusFilter, $allowed)) {
    $sql .= " WHERE status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY created_at DESC";
$orders = $pdo->prepare($sql);
$orders->execute($params);
$orders = $orders->fetchAll();
?>
<?php 
$adminTitle = 'Orders Management';
include_once __DIR__ . '/includes/header.php'; 
?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold">All Orders (<?php echo count($orders); ?>)</h1>
        <!-- Filter Buttons -->
        <div class="flex gap-2 flex-wrap">
            <a href="orders.php" class="<?php echo !$statusFilter ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border'; ?> px-4 py-2 rounded-xl text-sm font-semibold transition hover:bg-amber-600 hover:text-white">All</a>
            <?php foreach(['Pending','Processing','Delivered','Cancelled'] as $s): ?>
            <a href="orders.php?status=<?php echo $s; ?>" class="<?php echo $statusFilter === $s ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border'; ?> px-4 py-2 rounded-xl text-sm font-semibold transition hover:bg-amber-600 hover:text-white"><?php echo $s; ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">#ID</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Customer</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Phone</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Address</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">No orders found.</td></tr>
                    <?php endif; ?>
                    <?php foreach($orders as $order): ?>
                    <?php $statusColor = match($order['status']) {
                        'Pending' => 'bg-yellow-100 text-yellow-700',
                        'Processing' => 'bg-blue-100 text-blue-700',
                        'Delivered' => 'bg-green-100 text-green-700',
                        'Cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600'
                    }; ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-amber-700">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td class="px-6 py-4">
                            <a href="tel:<?php echo $order['phone']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($order['phone']); ?></a>
                        </td>
                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($order['address']); ?></td>
                        <td class="px-6 py-4 font-bold text-amber-600">৳ <?php echo number_format($order['total_amount']); ?></td>
                        <td class="px-6 py-4">
                            <span class="<?php echo $statusColor; ?> text-xs font-bold px-2 py-1 rounded-full"><?php echo $order['status']; ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 whitespace-nowrap"><?php echo date('d M, g:ia', strtotime($order['created_at'])); ?></td>
                        <td class="px-6 py-4">
                            <form method="POST" class="flex gap-1">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="text-xs border border-gray-200 rounded-lg px-2 py-1 focus:outline-none focus:border-amber-400">
                                    <?php foreach(['Pending','Processing','Delivered','Cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3 py-1 rounded-lg transition">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
