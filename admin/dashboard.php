<?php
/**
 * Zaman Kitchens - Admin Dashboard
 * Features: KPIs, Latest Orders, AJAX new-order notification with sound
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

// Auth Guard
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Dashboard Stats
try {
    $totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
    $totalSales    = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $recentOrders  = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10")->fetchAll();
    $lastOrderId   = $pdo->query("SELECT COALESCE(MAX(id),0) FROM orders")->fetchColumn();
} catch(Exception $e) {
    $totalOrders = $pendingOrders = $totalSales = $totalProducts = 0;
    $recentOrders = [];
    $lastOrderId = 0;
}
?>
<?php 
$adminTitle = 'Dashboard';
include_once __DIR__ . '/includes/header.php'; 
?>

<!-- Notification Alert Bar (hidden by default) -->
<div id="newOrderAlert" class="bg-green-500 text-white px-6 py-3 text-center font-bold hidden cursor-pointer" onclick="window.location.href='orders.php'">
    🔔 New order received! Click to view.
</div>

<div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-extrabold mb-8">Dashboard Overview</h1>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <?php
        $kpis = [
            ['Total Orders', $totalOrders, '📦', 'blue', 'orders.php'],
            ['Pending Orders', $pendingOrders, '⏳', 'yellow', 'orders.php?status=Pending'],
            ['Total Sales', '৳ ' . number_format($totalSales), '💰', 'green', 'orders.php'],
            ['Total Products', $totalProducts, '🏪', 'purple', 'products.php'],
        ];
        foreach ($kpis as [$label, $value, $icon, $color, $link]):
        $colors = ['blue'=>'bg-blue-50 border-blue-100 text-blue-700', 'yellow'=>'bg-yellow-50 border-yellow-100 text-yellow-700', 'green'=>'bg-green-50 border-green-100 text-green-700', 'purple'=>'bg-purple-50 border-purple-100 text-purple-700'];
        ?>
        <a href="<?php echo $link; ?>" class="<?php echo $colors[$color]; ?> border rounded-2xl p-5 hover:shadow-md transition">
            <div class="text-3xl mb-2"><?php echo $icon; ?></div>
            <div class="text-2xl font-extrabold"><?php echo $value; ?></div>
            <div class="text-xs font-semibold mt-1 opacity-75"><?php echo $label; ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="font-extrabold text-lg">Recent Orders</h2>
            <a href="orders.php" class="text-amber-600 text-sm font-semibold hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Order ID</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Customer</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Phone</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Time</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">No orders yet</td></tr>
                    <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <?php
                    $statusColor = match($order['status']) {
                        'Pending' => 'bg-yellow-100 text-yellow-700',
                        'Processing' => 'bg-blue-100 text-blue-700',
                        'Delivered' => 'bg-green-100 text-green-700',
                        'Cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600'
                    };
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($order['phone']); ?></td>
                        <td class="px-6 py-4 font-bold text-amber-600">৳ <?php echo number_format($order['total_amount']); ?></td>
                        <td class="px-6 py-4">
                            <span class="<?php echo $statusColor; ?> text-xs font-bold px-2 py-1 rounded-full"><?php echo $order['status']; ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-400"><?php echo date('d M, g:ia', strtotime($order['created_at'])); ?></td>
                        <td class="px-6 py-4">
                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="text-amber-600 font-semibold hover:underline">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- AJAX New Order Polling + Sound Alert + Browser Notification -->
<script>
let lastKnownOrderId = <?php echo (int)$lastOrderId; ?>;

// Notification Sound (Web Audio API - no file needed)
function playAlertSound() {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.frequency.setValueAtTime(880, ctx.currentTime);
    osc.frequency.setValueAtTime(660, ctx.currentTime + 0.1);
    osc.frequency.setValueAtTime(880, ctx.currentTime + 0.2);
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
    osc.start(ctx.currentTime);
    osc.stop(ctx.currentTime + 0.4);
}

// Browser Notification
function showBrowserNotification(msg) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('🔔 New Order - Zaman Kitchens', { body: msg, icon: '/assets/images/hero.png' });
    }
}

// Poll for new orders every 30 seconds
function pollNewOrders() {
    fetch('api/check_orders.php?last_id=' + lastKnownOrderId)
        .then(r => r.json())
        .then(data => {
            if (data.new_count > 0) {
                lastKnownOrderId = data.latest_id;
                playAlertSound();
                showBrowserNotification(data.new_count + ' new order(s) arrived!');
                document.getElementById('newOrderAlert').classList.remove('hidden');
                document.getElementById('newOrderAlert').textContent = '🔔 ' + data.new_count + ' new order(s) received! Click to view.';
            }
        })
        .catch(() => {});
}

// Request notification permission on load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Start polling every 30 seconds
setInterval(pollNewOrders, 30000);
</script>

</body>
</html>
