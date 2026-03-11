<?php
/**
 * Zaman Kitchens - Financial Reports
 * Features: Revenue, Cost, Profit Tracking
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

// Auth Guard
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$filter = $_GET['filter'] ?? 'this_month';

// Date ranges
$startDate = date('Y-m-01 00:00:00'); // Default: This Month
$endDate = date('Y-m-t 23:59:59');
$title = "This Month";

if ($filter === 'today') {
    $startDate = date('Y-m-d 00:00:00');
    $endDate = date('Y-m-d 23:59:59');
    $title = "Today";
} elseif ($filter === 'all_time') {
    $startDate = '2000-01-01 00:00:00';
    $endDate = date('Y-m-d 23:59:59');
    $title = "All Time";
}

try {
    // 1. Total Revenue
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'Cancelled' AND created_at BETWEEN ? AND ?");
    $stmt->execute([$startDate, $endDate]);
    $revenue = $stmt->fetchColumn();

    // 2. Total Cost (COGS)
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(oi.quantity * COALESCE(p.purchase_price, 0)), 0) 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        JOIN orders o ON oi.order_id = o.id 
        WHERE o.status != 'Cancelled' AND o.created_at BETWEEN ? AND ?
    ");
    $stmt->execute([$startDate, $endDate]);
    $cost = $stmt->fetchColumn();

    $profit = $revenue - $cost;
    $ordersCount = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status != 'Cancelled' AND created_at BETWEEN ? AND ?");
    $ordersCount->execute([$startDate, $endDate]);
    $ordersCount = $ordersCount->fetchColumn();

    // 3. Top Selling Products
    $topProducts = $pdo->prepare("
        SELECT p.name, SUM(oi.quantity) as total_qty, SUM(oi.quantity * oi.price) as total_sales
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status != 'Cancelled' AND o.created_at BETWEEN ? AND ?
        GROUP BY p.id
        ORDER BY total_qty DESC
        LIMIT 5
    ");
    $topProducts->execute([$startDate, $endDate]);
    $topProducts = $topProducts->fetchAll();

} catch(Exception $e) {
    $revenue = $cost = $profit = $ordersCount = 0;
    $topProducts = [];
}

$adminTitle = 'Financial Reports';
include_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Financial Reports</h1>
            <p class="text-sm text-gray-500">Business overview for <span class="font-bold text-amber-600"><?php echo $title; ?></span></p>
        </div>
        
        <!-- Filters -->
        <div class="flex bg-white rounded-2xl p-1 border border-gray-100 shadow-sm">
            <a href="reports.php?filter=today" class="px-5 py-2 rounded-xl text-sm font-bold transition <?php echo $filter === 'today' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-gray-500 hover:bg-gray-50'; ?>">Today</a>
            <a href="reports.php?filter=this_month" class="px-5 py-2 rounded-xl text-sm font-bold transition <?php echo $filter === 'this_month' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-gray-500 hover:bg-gray-50'; ?>">This Month</a>
            <a href="reports.php?filter=all_time" class="px-5 py-2 rounded-xl text-sm font-bold transition <?php echo $filter === 'all_time' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-gray-500 hover:bg-gray-50'; ?>">All Time</a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="text-gray-400 text-[10px] font-extrabold uppercase tracking-widest mb-1">Total Orders</div>
            <div class="text-3xl font-extrabold"><?php echo $ordersCount; ?></div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="text-gray-400 text-[10px] font-extrabold uppercase tracking-widest mb-1">Revenue</div>
            <div class="text-3xl font-extrabold text-gray-900">৳ <?php echo number_format($revenue); ?></div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="text-gray-400 text-[10px] font-extrabold uppercase tracking-widest mb-1">Cost (COGS)</div>
            <div class="text-3xl font-extrabold text-red-500">৳ <?php echo number_format($cost); ?></div>
        </div>
        <div class="bg-indigo-600 p-6 rounded-3xl shadow-xl shadow-indigo-100 relative overflow-hidden">
            <div class="relative z-10">
                <div class="text-indigo-200 text-[10px] font-extrabold uppercase tracking-widest mb-1">Gross Profit</div>
                <div class="text-3xl font-extrabold text-white">৳ <?php echo number_format($profit); ?></div>
            </div>
            <div class="absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12">📈</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Top Selling Products -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-lg">Top Selling Products</h3>
                <span class="text-xs text-gray-400">By Quantity</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-8 py-4 text-left font-bold uppercase tracking-widest text-[10px]">Product Name</th>
                            <th class="px-8 py-4 text-center font-bold uppercase tracking-widest text-[10px]">Qty Sold</th>
                            <th class="px-8 py-4 text-right font-bold uppercase tracking-widest text-[10px]">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($topProducts)): ?>
                        <tr><td colspan="3" class="px-8 py-10 text-center text-gray-400">No data available for this range.</td></tr>
                        <?php else: ?>
                        <?php foreach($topProducts as $tp): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-8 py-5 font-bold text-gray-800"><?php echo htmlspecialchars($tp['name']); ?></td>
                            <td class="px-8 py-5 text-center font-semibold text-indigo-600"><?php echo $tp['total_qty']; ?></td>
                            <td class="px-8 py-5 text-right font-bold text-gray-900">৳ <?php echo number_format($tp['total_sales']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profit Margin Alert/Tip -->
        <div class="space-y-6">
            <div class="bg-amber-50 rounded-3xl p-8 border border-amber-100">
                <div class="text-2xl mb-4">💡</div>
                <h4 class="font-bold text-amber-800 mb-2">POS Tip</h4>
                <p class="text-sm text-amber-700 leading-relaxed">
                    আপনার মোট বিক্রয় থেকে পণ্যের ক্রয়মূল্য বাদ দিলে <b>নিট লাভ</b> পাওয়া যায়। নিয়মিত ক্রয়মূল্য আপডেট করলে আপনার রিপোর্টিং আরও নির্ভুল হবে।
                </p>
            </div>
            
            <div class="bg-gray-900 rounded-3xl p-8 text-white relative overflow-hidden">
                <h4 class="font-bold mb-4 relative z-10">Export Data</h4>
                <p class="text-xs text-gray-400 mb-6 relative z-10">CSV বা Excel ফাইল হিসেবে ডাটা এক্সপোর্ট করার ফিচার শীঘ্রই আসবে।</p>
                <button disabled class="w-full py-3 bg-gray-800 rounded-xl text-xs font-bold opacity-50 cursor-not-allowed">COMING SOON</button>
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 rotate-12">📊</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
