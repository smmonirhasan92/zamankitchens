<?php
/**
 * Zaman Kitchens - Inventory Management
 * Quick stock tracking and bulk updates.
 */
$adminTitle = 'Inventory Control';
include_once __DIR__ . '/includes/header.php';

$products = [];
try {
    $products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.stock_status DESC, p.name ASC")->fetchAll();
} catch(Exception $e) {}

// Stats
$total = count($products);
$inStock = 0;
$outOfStock = 0;
foreach($products as $p) {
    if($p['stock_qty'] > 0) $inStock++;
    else $outOfStock++;
}
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Inventory Management</h1>
            <p class="text-sm text-slate-500">Monitor and update product availability in real-time.</p>
        </div>
        <div class="flex gap-3">
            <a href="barcode-scanner.php" class="btn btn-primary">
                <i class="ph ph-barcode"></i>
                Launch Scanner
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="kpi-card">
            <div class="kpi-icon bg-slate-100 text-slate-600"><i class="ph ph-package"></i></div>
            <div class="kpi-value"><?php echo $total; ?></div>
            <div class="kpi-label">Total SKUs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon bg-emerald-100 text-emerald-600"><i class="ph ph-check-circle"></i></div>
            <div class="kpi-value"><?php echo $inStock; ?></div>
            <div class="kpi-label">In Stock</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon bg-rose-100 text-rose-600"><i class="ph ph-warning-circle"></i></div>
            <div class="kpi-value"><?php echo $outOfStock; ?></div>
            <div class="kpi-label">Out of Stock</div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">Stock Availability List</span>
            <div class="flex gap-4">
                <input type="text" id="inventory-search" onkeyup="filterInventory()" placeholder="Search products..." class="text-xs border border-slate-200 rounded-lg px-3 py-1.5 outline-none focus:border-red-500">
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table" id="inventory-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Sale Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th style="text-align:right;">Quick Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr class="inventory-row">
                        <td>
                            <div style="font-weight:700; color:#111827;"><?php echo htmlspecialchars($p['name']); ?></div>
                            <div style="font-size:10px; color:#9ca3af; font-family:monospace;">BARCODE: <?php echo $p['barcode'] ?: 'NONE'; ?></div>
                        </td>
                        <td><span style="font-size:11px; font-weight:600; color:#6b7280;"><?php echo htmlspecialchars($p['category_name'] ?: 'General'); ?></span></td>
                        <td style="font-weight:700;">৳ <?php echo number_format($p['price']); ?></td>
                        <td><span style="font-weight:700; color:#4b5563;"><?php echo $p['stock_qty']; ?></span></td>
                        <td>
                            <select onchange="updateStock(this, <?php echo $p['id']; ?>)" 
                                    class="text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-lg border-none outline-none cursor-pointer <?php echo ($p['stock_status'] == 'In Stock') ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?>">
                                <option value="In Stock" <?php echo ($p['stock_status'] == 'In Stock') ? 'selected' : ''; ?>>In Stock</option>
                                <option value="Out of Stock" <?php echo ($p['stock_status'] == 'Out of Stock') ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </td>
                        <td style="text-align:right;">
                            <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="text-slate-400 hover:text-red-500 transition">
                                <i class="ph ph-pencil-simple" style="font-size:1.25rem;"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterInventory() {
    const input = document.getElementById('inventory-search');
    const filter = input.value.toUpperCase();
    const rows = document.querySelectorAll('.inventory-row');
    
    rows.forEach(row => {
        const text = row.innerText.toUpperCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function updateStock(select, id) {
    const status = select.value;
    select.disabled = true;
    
    // Use the same API as scanner for consistency
    fetch('api-update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, price: 0, stock_status: status }) // price 0 means keep current
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            select.className = `text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-lg border-none outline-none cursor-pointer ${status === 'In Stock' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`;
        }
        select.disabled = false;
    })
    .catch(() => { select.disabled = false; alert('Failed to update.'); });
}
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
