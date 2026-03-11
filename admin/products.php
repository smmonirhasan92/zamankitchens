require_once __DIR__ . '/../includes/db.php';

$adminTitle = 'Manage Products';
include_once __DIR__ . '/includes/header.php';

$message = "";
$error = "";

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $message = "Product deleted successfully!";
    } catch(Exception $e) { $error = "Error: " . $e->getMessage(); }
}

// Fetch Products with category names
try {
    $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll();
} catch(Exception $e) { $products = []; }

// Fetch Categories for dropdown
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
?>

<?php if($message): ?> <div class="max-w-7xl mx-auto px-6 mt-6"><div class="bg-green-50 text-green-700 p-4 rounded-2xl font-bold border border-green-100">✅ <?php echo $message; ?></div></div> <?php endif; ?>
<?php if($error): ?> <div class="max-w-7xl mx-auto px-6 mt-6"><div class="bg-red-50 text-red-700 p-4 rounded-2xl font-bold border border-red-100">❌ <?php echo $error; ?></div></div> <?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-extrabold">Products Management</h1>
        <a href="product-edit.php" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-lg shadow-amber-200">Add New Product</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Image</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Product Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Category</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Price</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Stock</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if(empty($products)): ?>
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No products found. Add your first product!</td></tr>
                    <?php endif; ?>
                    <?php foreach ($products as $p): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <img src="../<?php echo $p['main_image']; ?>" class="w-12 h-12 rounded-lg object-cover bg-gray-100 border border-gray-100">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800"><?php echo htmlspecialchars($p['name']); ?></div>
                            <div class="text-[10px] text-gray-400 font-mono"><?php echo $p['slug']; ?></div>
                        </td>
                        <td class="px-6 py-4 px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full inline-block mt-4"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></td>
                        <td class="px-6 py-4 font-bold text-amber-600">৳ <?php echo number_format($p['price']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $p['stock_status'] == 'In Stock' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <?php echo $p['stock_status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition font-semibold">Edit</a>
                                <a href="products.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete product?')" class="text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-lg transition font-semibold">Delete</a>
                            </div>
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
