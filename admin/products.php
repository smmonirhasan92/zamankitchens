<?php
$adminTitle = 'Product Management';
$adminTopbarAction = '<a href="product-edit.php" class="topbar-btn btn-primary"><i class="ph ph-plus-circle"></i> Add Product</a>';
include_once __DIR__ . '/includes/header.php';

$products = [];
try {
    $products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
} catch(Exception $e) {}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">All Products</span>
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <span style="font-size:0.75rem; color:#9ca3af; font-weight:700;"><?php echo count($products); ?> Products</span>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="6" style="text-align:center; padding:3rem; color:#9ca3af;">No products found.</td></tr>
                <?php endif; ?>
                <?php foreach($products as $i => $p): 
                    $img = !empty($p['image']) ? $p['image'] : '../assets/images/placeholder.jpg';
                ?>
                <tr>
                    <td style="color:#d1d5db; font-weight:800;"><?php echo $i+1; ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.875rem;">
                            <img src="<?php echo htmlspecialchars($img); ?>" 
                                 style="width:40px; height:40px; border-radius:8px; object-cover: cover; background:#f3f4f6;"
                                 onerror="this.src='https://placehold.co/100x100/f3f4f6/9ca3af?text=Pro'">
                            <div style="min-width:0;">
                                <div style="font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div style="font-size:0.6875rem; color:#9ca3af;">ID: #<?php echo str_pad($p['id'], 3, '0', STR_PAD_LEFT); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-size:0.75rem; font-weight:700; color:#4b5563; background:#f3f4f6; padding:0.25rem 0.625rem; border-radius:20px;"><?php echo htmlspecialchars($p['category_name'] ?: 'Uncategorized'); ?></span></td>
                    <td style="font-weight:800; color:#111827;">৳ <?php echo number_format($p['price']); ?></td>
                    <td>
                        <?php if(!empty($p['is_featured'])): ?>
                            <span class="status-badge" style="background:rgba(239,35,60,0.1); color:#ef233c;">⭐ Featured</span>
                        <?php else: ?>
                            <span class="status-badge" style="background:#f3f4f6; color:#9ca3af;">Standard</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:inline-flex; gap:0.5rem;">
                            <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-ghost" style="padding:0.4rem; width:32px; height:32px; justify-content:center;">
                                <i class="ph ph-note-pencil" style="font-size:1rem;"></i>
                            </a>
                            <button onclick="if(confirm('Delete this product?')) window.location.href='products.php?delete=<?php echo $p['id']; ?>'" class="btn btn-danger" style="padding:0.4rem; width:32px; height:32px; justify-content:center;">
                                <i class="ph ph-trash" style="font-size:1rem;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
