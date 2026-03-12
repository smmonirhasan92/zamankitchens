<?php
/**
 * Zaman Kitchens - Manage Categories
 * Features: List, Add, Edit, Delete categories with Image Upload
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

// Auth Guard
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $message = "Category deleted successfully!";
    } catch(Exception $e) { $error = "Error deleting category: " . $e->getMessage(); }
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    
    // Auto-generate slug if empty
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    $image_path = $_POST['existing_image'] ?? '';

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/categories/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = $slug . '-' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'assets/uploads/categories/' . $file_name;
        }
    }

    if ($name) {
        try {
            if ($id) {
                // Update
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $image_path, $id]);
                $message = "Category updated!";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image) VALUES (?, ?, ?)");
                $stmt->execute([$name, $slug, $image_path]);
                $message = "Category added!";
            }
        } catch(Exception $e) { $error = "Database Error: " . $e->getMessage(); }
    }
}

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Fetch single category for editing
$editCat = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCat = $stmt->fetch();
}
?>
<?php 
$adminTitle = 'Manage Categories';
include_once __DIR__ . '/includes/header.php'; 
?>

<div class="max-w-7xl mx-auto px-6 py-10 grid md:grid-cols-3 gap-8">
    
    <!-- Form Section -->
    <div class="md:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h2 class="text-xl font-extrabold mb-6"><?php echo $editCat ? 'Edit Category' : 'Add New Category'; ?></h2>
            
            <?php if($message): ?> <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 text-sm font-medium border border-green-100">✅ <?php echo $message; ?></div> <?php endif; ?>
            <?php if($error): ?> <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-4 text-sm font-medium border border-red-100">❌ <?php echo $error; ?></div> <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="id" value="<?php echo $editCat['id'] ?? ''; ?>">
                <input type="hidden" name="existing_image" value="<?php echo $editCat['image'] ?? ''; ?>">
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Category Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($editCat['name'] ?? ''); ?>"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-400 focus:bg-white transition text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Slug (Optional)</label>
                    <input type="text" name="slug" value="<?php echo htmlspecialchars($editCat['slug'] ?? ''); ?>"
                        placeholder="e.g. kitchen-cabinet"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-400 focus:bg-white transition text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Category Image/Icon</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                    <?php if(!empty($editCat['image'])): ?>
                        <img src="../<?php echo $editCat['image']; ?>" class="mt-2 w-16 h-16 rounded-lg object-cover border border-gray-100">
                    <?php endif; ?>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-amber-200">
                        <?php echo $editCat ? 'Update Category' : 'Save Category'; ?>
                    </button>
                    <?php if($editCat): ?>
                        <a href="categories.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-extrabold text-lg">Category List (<?php echo count($categories); ?>)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Image</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Slug</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <img src="../<?php echo !empty($cat['image']) ? $cat['image'] : 'https://placehold.co/400x400/f5f5f5/aaa?text='.$cat['name']; ?>" 
                                     class="w-10 h-10 rounded-full object-cover bg-gray-100 border border-gray-100 shadow-sm"
                                     onerror="this.src='https://placehold.co/100x100/f5f5f5/aaa?text=Error'">
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($cat['slug']); ?></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition font-semibold">Edit</a>
                                    <a href="categories.php?delete=<?php echo $cat['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this category?')"
                                       class="text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-lg transition font-semibold">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
