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

<div class="max-w-7xl mx-auto px-8 py-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Financial Insights</h1>
            <p class="text-slate-500 font-medium">Business overview for <span class="text-amber-600 font-bold"><?php echo $title; ?></span></p>
        </div>
        
        <!-- Premium Filter Tab -->
        <div class="flex bg-white rounded-2xl p-1.5 border border-slate-100 shadow-sm overflow-hidden">
            <a href="reports.php?filter=today" class="px-6 py-2 rounded-xl text-xs font-black transition-all <?php echo $filter === 'today' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50'; ?>">TODAY</a>
            <a href="reports.php?filter=this_month" class="px-6 py-2 rounded-xl text-xs font-black transition-all <?php echo $filter === 'this_month' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50'; ?>">THIS MONTH</a>
            <a href="reports.php?filter=all_time" class="px-6 py-2 rounded-xl text-xs font-black transition-all <?php echo $filter === 'all_time' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50'; ?>">ALL TIME</a>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-7 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Orders</div>
            <div class="text-3xl font-black text-slate-900"><?php echo $ordersCount; ?></div>
        </div>
        <div class="bg-white p-7 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Gross Revenue</div>
            <div class="text-3xl font-black text-slate-900">৳ <?php echo number_format($revenue); ?></div>
        </div>
        <div class="bg-white p-7 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2">Cost (COGS)</div>
            <div class="text-3xl font-black text-rose-500">৳ <?php echo number_format($cost); ?></div>
        </div>
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 p-7 rounded-[2.5rem] shadow-xl shadow-indigo-100 relative overflow-hidden">
            <div class="relative z-10">
                <div class="text-indigo-200 text-[10px] font-black uppercase tracking-widest mb-2">Net Profit</div>
                <div class="text-4xl font-black text-white italic">৳ <?php echo number_format($profit); ?></div>
            </div>
            <div class="absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12">📈</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Top Products Redesign -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-8 py-7 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-black text-xl text-slate-900">Best Performing Products</h3>
                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-full uppercase">By Volume</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-left font-bold text-slate-400 uppercase tracking-widest text-[10px]">Product Name</th>
                            <th class="px-8 py-4 text-center font-bold text-slate-400 uppercase tracking-widest text-[10px]">Qty Sold</th>
                            <th class="px-8 py-4 text-right font-bold text-slate-400 uppercase tracking-widest text-[10px]">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($topProducts)): ?>
                        <tr><td colspan="3" class="px-8 py-16 text-center text-slate-400 font-medium italic">Insufficient data for report.</td></tr>
                        <?php else: ?>
                        <?php foreach($topProducts as $tp): ?>
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-800 group-hover:text-amber-600 transition-all"><?php echo htmlspecialchars($tp['name']); ?></div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">Product Category</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex items-center justify-center w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-base"><?php echo $tp['total_qty']; ?></div>
                            </td>
                            <td class="px-8 py-6 text-right font-black text-xl text-slate-900 leading-none group-hover:scale-105 transition-transform origin-right">৳ <?php echo number_format($tp['total_sales']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar Tips & CTA -->
        <div class="space-y-8">
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden border border-slate-800">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-xl shadow-amber-500/20">💡</div>
                    <h4 class="font-black text-xl mb-4">Accounting Tip</h4>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        আপনার নিট মুনাফা আরও নির্ভুল করতে প্রতিটি পণ্যের **Purchase Price** নিয়মিত আপডেট করুন। এটি আপনাকে সঠিক ব্যবসায়িক সিদ্ধান্ত নিতে সাহায্য করবে।
                    </p>
                    <a href="products.php" class="inline-block text-amber-500 font-black text-xs uppercase tracking-widest border-b-2 border-amber-500/20 hover:border-amber-500 transition-all pb-1">Update Inventory &rarr;</a>
                </div>
                <div class="absolute -right-6 -bottom-6 text-9xl opacity-[0.03] rotate-12">💎</div>
            </div>
            
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm group">
                <h4 class="font-black text-slate-900 mb-4">Advanced Analytics</h4>
                <p class="text-xs text-slate-400 font-medium leading-relaxed mb-8">Detailed graphs, CSV exports and multi-store reporting are currently in development.</p>
                <button disabled class="w-full py-4 bg-slate-50 border border-slate-100 rounded-2xl text-[10px] font-black text-slate-400 opacity-60 tracking-[0.2em] transition-all">COMING SOON</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
