<?php
$adminTitle = 'Categories';
include_once __DIR__ . '/includes/header.php';

// Handle Add/Edit/Delete Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_name'])) {
    $name = trim($_POST['cat_name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    if (!empty($name)) {
        if (!empty($_POST['cat_id'])) {
            $pdo->prepare("UPDATE categories SET name=?, slug=? WHERE id=?")->execute([$name, $slug, $_POST['cat_id']]);
        } else {
            $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
        }
    }
    header("Location: categories.php"); exit();
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$_GET['delete']]);
    header("Location: categories.php"); exit();
}

$categories = [];
try {
    $categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.name ASC")->fetchAll();
} catch(Exception $e) {}
?>

<div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem; align-items: start;">
    <!-- Add/Edit Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title"><?php echo isset($_GET['edit']) ? 'Edit Category' : 'Create New Category'; ?></span>
        </div>
        <div class="admin-card-body">
            <form method="POST">
                <?php 
                $editCat = null;
                if(isset($_GET['edit'])) {
                    foreach($categories as $c) if($c['id'] == $_GET['edit']) $editCat = $c;
                }
                ?>
                <input type="hidden" name="cat_id" value="<?php echo $editCat['id'] ?? ''; ?>">
                <div style="margin-bottom:1.5rem;">
                    <label class="admin-label">Category Name</label>
                    <input type="text" name="cat_name" required class="admin-input" placeholder="e.g. Kitchen Sinks" value="<?php echo htmlspecialchars($editCat['name'] ?? ''); ?>">
                </div>
                <div style="display:flex; gap:0.75rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;"><?php echo $editCat ? 'Update Category' : 'Add Category'; ?></button>
                    <?php if($editCat): ?>
                        <a href="categories.php" class="btn btn-ghost">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories List -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">Manage Categories</span>
            <span style="font-size:0.75rem; color:#9ca3af; font-weight:700;"><?php echo count($categories); ?> Categories</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:3rem; color:#9ca3af;">No categories yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach($categories as $i => $c): ?>
                    <tr>
                        <td style="color:#d1d5db; font-weight:700;"><?php echo $i+1; ?></td>
                        <td style="font-weight:800; color:#111827;"><?php echo htmlspecialchars($c['name']); ?></td>
                        <td style="font-family:monospace; font-size:0.75rem; color:#6b7280;"><?php echo $c['slug']; ?></td>
                        <td><span style="font-weight:700; color:#ef233c;"><?php echo $c['product_count']; ?></span></td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:0.5rem;">
                                <a href="categories.php?edit=<?php echo $c['id']; ?>" class="btn btn-ghost" style="padding:0.35rem 0.75rem; font-size:0.75rem;">Edit</a>
                                <button onclick="if(confirm('Delete this category?')) window.location.href='categories.php?delete=<?php echo $c['id']; ?>'" class="btn btn-danger" style="padding:0.35rem 0.75rem; font-size:0.75rem;">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
