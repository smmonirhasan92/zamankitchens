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
    $totalProfit   = $pdo->query("SELECT COALESCE(SUM(oi.quantity * (oi.price - COALESCE(p.purchase_price, 0))), 0) FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE o.status != 'Cancelled'")->fetchColumn();
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
<div id="newOrderAlert" class="bg-indigo-600 text-white px-8 py-4 text-center font-bold hidden cursor-pointer sticky top-[72px] z-40 animate-pulse shadow-xl shadow-indigo-200">
    🚀 New order received! Tap to view details.
</div>

<div class="max-w-7xl mx-auto px-8 py-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Dashboard Overview</h1>
            <p class="text-slate-500 font-medium">Welcome back, <span class="text-amber-600">Admin</span>. Here's what's happening today.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Export
            </button>
            <a href="product-edit.php" class="px-5 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-bold hover:bg-amber-700 transition shadow-lg shadow-amber-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                New Product
            </a>
        </div>
    </div>

    <!-- Premium KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <?php
        $kpis = [
            ['Total Orders', $totalOrders, '📦', 'bg-blue-600', 'orders.php'],
            ['Pending Status', $pendingOrders, '⏳', 'bg-amber-500', 'orders.php?status=Pending'],
            ['Total Sales', '৳ ' . number_format($totalSales), '💰', 'bg-emerald-600', 'orders.php'],
            ['Net Profit', '৳ ' . number_format($totalProfit), '📈', 'bg-indigo-600', 'reports.php'],
        ];
        foreach ($kpis as [$label, $value, $icon, $color, $link]): ?>
        <a href="<?php echo $link; ?>" class="group bg-white p-7 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-7xl opacity-[0.03] group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500"><?php echo $icon; ?></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 <?php echo $color; ?> rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-<?php echo str_replace('bg-','',$color); ?>/20 text-white"><?php echo $icon; ?></div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo $label; ?></div>
            </div>
            <div class="text-3xl font-black text-slate-900 group-hover:text-amber-600 transition-colors"><?php echo $value; ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Recent Orders & Stats Grid -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-8 py-7 border-b border-slate-50">
                <h2 class="font-black text-xl text-slate-900">Recent Orders</h2>
                <a href="orders.php" class="px-4 py-1.5 bg-slate-50 text-slate-500 text-xs font-bold rounded-full hover:bg-amber-50 hover:text-amber-600 transition">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-left font-bold text-slate-400 uppercase tracking-widest text-[10px]">Order</th>
                            <th class="px-8 py-4 text-left font-bold text-slate-400 uppercase tracking-widest text-[10px]">Customer</th>
                            <th class="px-8 py-4 text-left font-bold text-slate-400 uppercase tracking-widest text-[10px]">Amount</th>
                            <th class="px-8 py-4 text-left font-bold text-slate-400 uppercase tracking-widest text-[10px]">Status</th>
                            <th class="px-8 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="5" class="px-8 py-16 text-center">
                            <div class="text-4xl mb-4 opacity-20">📭</div>
                            <div class="text-slate-400 font-medium">No orders recorded yet.</div>
                        </td></tr>
                        <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                        <?php
                        $statusColor = match($order['status']) {
                            'Pending' => 'bg-amber-100 text-amber-700',
                            'Processing' => 'bg-blue-100 text-blue-700',
                            'Delivered' => 'bg-emerald-100 text-emerald-700',
                            'Cancelled' => 'bg-rose-100 text-rose-700',
                            default => 'bg-slate-100 text-slate-600'
                        };
                        ?>
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="px-8 py-5">
                                <div class="font-black text-slate-900 group-hover:text-amber-600 transition-colors">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></div>
                                <div class="text-[10px] text-slate-400 font-bold"><?php echo date('d M, g:ia', strtotime($order['created_at'])); ?></div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-slate-800"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                <div class="text-[10px] text-slate-400 font-bold"><?php echo htmlspecialchars($order['phone']); ?></div>
                            </td>
                            <td class="px-8 py-5 font-black text-indigo-600">৳ <?php echo number_format($order['total_amount']); ?></td>
                            <td class="px-8 py-5">
                                <span class="<?php echo $statusColor; ?> text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-wider"><?php echo $order['status']; ?></span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="orders.php?id=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center hover:bg-amber-600 hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Extra Stats Sidebar -->
        <div class="space-y-8">
            <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="font-black text-xl mb-2">Pro Plan Active</h3>
                    <p class="text-indigo-100 text-sm mb-6 leading-relaxed">You have full access to variations, SEO meta data, and financial reporting.</p>
                    <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl w-fit">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></div>
                        <span class="text-xs font-bold uppercase tracking-widest">System Online</span>
                    </div>
                </div>
                <div class="absolute -right-8 -bottom-8 text-9xl opacity-10 rotate-12">🌟</div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h3 class="font-black text-slate-900 mb-6">Inventory Health</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-bold text-slate-500">Products Listed</div>
                        <div class="text-lg font-black text-slate-900"><?php echo $totalProducts; ?></div>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full" style="width: 75%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-relaxed font-bold uppercase tracking-widest">Stock is monitored automatically based on sales data.</p>
                </div>
            </div>
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
