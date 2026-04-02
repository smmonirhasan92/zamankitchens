<?php
require_once __DIR__ . '/../includes/db.php';

// Handle Add/Edit/Delete Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_name'])) {
    $name = trim($_POST['cat_name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $cat_id = $_POST['cat_id'] ?? null;
    $parent_id = (!empty($_POST['parent_id'])) ? $_POST['parent_id'] : null;
    $hero_image = $_POST['existing_image'] ?? null;

    if (!empty($_FILES['cat_image']['name'])) {
        $upload_dir = __DIR__ . '/../assets/uploads/ca/';
        if (!is_dir($upload_dir)) {
            if(!mkdir($upload_dir, 0777, true)) {
                $error = "❌ Directory 'assets/uploads/ca/' missing and could not be created.";
            }
        }
        
        if (!is_writable($upload_dir)) {
            $error = "❌ Folder 'assets/uploads/ca/' is not writable. Please fix permissions.";
        } else {
            $file_ext = pathinfo($_FILES['cat_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '-' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['cat_image']['tmp_name'], $upload_dir . $file_name)) {
                $hero_image = 'assets/uploads/ca/' . $file_name;
            } else {
                $error = "❌ Failed to move uploaded file. Check server tmp limits.";
            }
        }
    }

    if (!empty($name)) {
        try {
            if (!empty($cat_id)) {
                $pdo->prepare("UPDATE categories SET name=?, slug=?, hero_image=?, parent_id=? WHERE id=?")->execute([$name, $slug, $hero_image, $parent_id, $cat_id]);
                $msg = "success_update";
            } else {
                $pdo->prepare("INSERT INTO categories (name, slug, hero_image, parent_id) VALUES (?, ?, ?, ?)")->execute([$name, $slug, $hero_image, $parent_id]);
                $msg = "success_add";
            }
            if (!empty($error)) {
                header("Location: categories.php?error=upload_fail");
            } else {
                header("Location: categories.php?msg=" . $msg);
            }
            exit();
        } catch(Exception $e) {
            $errCode = "db_error";
            if (str_contains($e->getMessage(), '1062')) {
                $errCode = "duplicate";
            }
            header("Location: categories.php?error=" . $errCode);
            exit();
        }
    }
    header("Location: categories.php"); exit();
}

if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    try {
        // 1. Dissociate products (set to NULL)
        $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?")->execute([$del_id]);
        
        // 2. Dissociate sub-categories (make them main categories)
        $pdo->prepare("UPDATE categories SET parent_id = NULL WHERE parent_id = ?")->execute([$del_id]);

        // 3. Now delete the category
        $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$del_id]);
        header("Location: categories.php?msg=success_delete");
    } catch (PDOException $e) {
        header("Location: categories.php?error=db_error");
    }
    exit();
}

$adminTitle = 'Categories';
include_once __DIR__ . '/includes/header.php';

$categories = [];
$msgCode = $_GET['msg'] ?? '';
$errorCode = $_GET['error'] ?? '';

// Message Mapping
$messages = [
    'success_add' => 'বিভাগটি সফলভাবে যুক্ত হয়েছে। (Category added successfully!)',
    'success_update' => 'বিভাগটি সফলভাবে আপডেট হয়েছে। (Category updated successfully!)',
    'success_delete' => 'বিভাগটি সফলভাবে মুছে ফেলা হয়েছে। (Category deleted successfully!)'
];

$errors = [
    'duplicate' => 'এই নামের একটি বিভাগ ইতিমধ্যে বিদ্যমান। অন্য একটি নাম চেষ্টা করুন। (This category name already exists. Please try another.)',
    'upload_fail' => 'ছবি আপলোড করতে সমস্যা হয়েছে। (Image upload failed.)',
    'db_error' => 'ডাটাবেস ত্রুটি হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন। (Database error occurred. Please try again.)'
];

$msg = $messages[$msgCode] ?? '';
$error = $errors[$errorCode] ?? '';

try {
    $categories = $pdo->query("SELECT c.*, p.name as parent_name, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c LEFT JOIN categories p ON c.parent_id = p.id ORDER BY COALESCE(p.name, c.name), c.parent_id IS NOT NULL, c.name ASC")->fetchAll();
} catch(Exception $e) {}
?>

<div class="max-w-7xl mx-auto">
    <?php if($msg): ?> <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-6 font-bold border border-emerald-100 flex items-center gap-3"><i class="ph ph-check-circle text-xl"></i> <?php echo $msg; ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-6 font-bold border border-rose-100 flex items-center gap-3"><i class="ph ph-warning-circle text-xl"></i> <?php echo $error; ?></div> <?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem; align-items: start;">
    <!-- Add/Edit Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title"><?php echo isset($_GET['edit']) ? 'Edit Category' : 'Create New Category'; ?></span>
        </div>
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php 
                $editCat = null;
                if(isset($_GET['edit'])) {
                    foreach($categories as $c) if($c['id'] == $_GET['edit']) $editCat = $c;
                }
                ?>
                <input type="hidden" name="cat_id" value="<?php echo $editCat['id'] ?? ''; ?>">
                <input type="hidden" name="existing_image" value="<?php echo $editCat['hero_image'] ?? ''; ?>">
                
                <div style="margin-bottom:1.25rem;">
                    <label class="admin-label">Category Name</label>
                    <input type="text" name="cat_name" required class="admin-input" placeholder="e.g. Kitchen Sinks" value="<?php echo htmlspecialchars($editCat['name'] ?? ''); ?>">
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="admin-label">Parent Category (Optional)</label>
                    <select name="parent_id" class="admin-input">
                        <option value="">-- Main Category (No Parent) --</option>
                        <?php foreach($categories as $c): 
                            if ($c['parent_id'] !== null) continue; // Only show main categories as potential parents
                            if (isset($editCat) && $c['id'] == $editCat['id']) continue; // Cannot be own parent
                        ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo (isset($editCat) && $editCat['parent_id'] == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="margin-bottom:1.5rem;">
                    <label class="admin-label">Category Icon / Circle Image</label>
                    <div style="display:flex; align-items:center; gap:1.5rem; background:#f8fafc; padding:1rem; border-radius:16px; border:1px solid #e2e8f0;">
                        <img id="cat-preview" src="<?php echo !empty($editCat['hero_image']) ? '../' . $editCat['hero_image'] : 'https://placehold.co/100x100/f1f5f9/94a3b8?text=Img'; ?>" 
                             style="width:70px; height:70px; border-radius:50%; object-fit:cover; border:2px solid #ef233c; padding:2px; background:white;">
                        <div style="flex:1;">
                            <label class="cursor-pointer">
                                <span style="display:inline-block; background:#ef233c; color:white; font-size:0.75rem; font-weight:800; padding:0.5rem 1rem; border-radius:8px; box-shadow:0 4px 10px rgba(239,35,60,0.2);">Change Image</span>
                                <input type="file" name="cat_image" accept="image/*" class="hidden" onchange="document.getElementById('cat-preview').src = window.URL.createObjectURL(this.files[0])">
                            </label>
                            <p style="font-size:0.65rem; color:#9ca3af; margin-top:0.5rem; font-weight:600;">Recommended: Square image for perfect circle</p>
                        </div>
                    </div>
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
                        <th>Type / Parent</th>
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
                        <td>
                            <div style="display:flex; align-items:center; gap:0.75rem; <?php echo $c['parent_id'] ? 'padding-left:1.5rem;' : ''; ?>">
                                <?php 
                                $catImg = !empty($c['hero_image']) ? '../' . $c['hero_image'] : null;
                                ?>
                                <?php if ($catImg): ?>
                                    <img src="<?php echo htmlspecialchars($catImg); ?>" 
                                         style="width:<?php echo $c['parent_id'] ? '36px' : '48px'; ?>; height:<?php echo $c['parent_id'] ? '36px' : '48px'; ?>; border-radius:50%; object-fit: cover; background:#f8fafc; border:1px solid #f1f5f9;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php endif; ?>
                                <div class="thumbnail-fallback" style="display: <?php echo $catImg ? 'none' : 'flex'; ?>; width:<?php echo $c['parent_id'] ? '36px' : '48px'; ?>; height:<?php echo $c['parent_id'] ? '36px' : '48px'; ?>; border-radius:50%; background:linear-gradient(135deg, #f8fafc, #f1f5f9); border:1px solid #e2e8f0; align-items:center; justify-center; flex-shrink:0;">
                                    <i class="ph ph-folder text-slate-400 text-lg" style="margin:auto;"></i>
                                </div>
                                <div style="min-width:0;">
                                    <div style="font-weight:800; color:#111827; font-size:<?php echo $c['parent_id'] ? '0.8125rem' : '0.9375rem'; ?>;"><?php echo htmlspecialchars($c['name']); ?></div>
                                    <div style="font-family:monospace; font-size:0.625rem; color:#9ca3af; margin-top:1px;">/<?php echo $c['slug']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($c['parent_id']): ?>
                                <span style="font-size:0.6875rem; font-weight:800; color:#6b7280; background:#f3f4f6; padding:0.2rem 0.6rem; border-radius:6px; border:1px solid #e5e7eb;">
                                    <i class="ph ph-arrow-bend-down-right"></i> <?php echo htmlspecialchars($c['parent_name']); ?>
                                </span>
                            <?php else: ?>
                                <span style="font-size:0.6875rem; font-weight:800; color:#ef233c; background:rgba(239,35,60,0.05); padding:0.2rem 0.6rem; border-radius:6px; border:1px solid rgba(239,35,60,0.1);">MAIN</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span style="font-weight:700; color:#ef233c; font-size:0.875rem;"><?php echo $c['product_count']; ?></span>
                                <span style="font-size:0.625rem; color:#9ca3af; font-weight:700; text-transform:uppercase;">Items</span>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:0.4rem;">
                                <a href="../category/<?php echo $c['slug']; ?>" target="_blank" class="btn btn-ghost" style="padding:0.35rem 0.65rem;" title="View on Site">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <a href="categories.php?edit=<?php echo $c['id']; ?>" class="btn btn-ghost" style="padding:0.35rem 0.75rem; font-size:0.75rem; font-weight:700;">Edit</a>
                                <a href="categories.php?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete this category?')" class="btn btn-danger" style="padding:0.35rem 0.75rem; font-size:0.75rem; font-weight:700;">Delete</a>
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
