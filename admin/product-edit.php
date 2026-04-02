<?php
/**
 * Zaman Kitchens - Pro Product Management (Add/Edit)
 * Features: Variations, Meta Data, Gallery, Specs
 */
require_once __DIR__ . '/../includes/db.php';


$id = $_GET['id'] ?? null;
$product = null;
$message = "";
$error = "";


// Fetch Product for editing
if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    // Decode JSON fields
    if ($product) {
        $product['variations'] = json_decode($product['variations'] ?? '[]', true) ?: [];
        $product['specifications'] = json_decode($product['specifications'] ?? '[]', true) ?: [];
        $product['gallery_images'] = json_decode($product['gallery_images'] ?? '[]', true) ?: [];
        // Compatibility fix for column name
        $product['image'] = $product['main_image'] ?? ($product['image'] ?? '');
    }

    // Fetch Price Rules
    $priceRules = $pdo->prepare("SELECT * FROM price_rules WHERE product_id = ? ORDER BY min_qty ASC");
    $priceRules->execute([$id]);
    $priceRules = $priceRules->fetchAll();
}

// (Generics query removed)

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $category_id = $_POST['category_id'] ?? null;
    $price = $_POST['price'] ?? 0;
    $purchase_price = $_POST['purchase_price'] ?? 0;
    $stock_qty = (int)($_POST['stock_qty'] ?? 0);
    $stock_status = $_POST['stock_status'] ?? 'In Stock';
    $description = $_POST['description'] ?? '';
    $meta_title = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $barcode = trim($_POST['barcode'] ?? '');
    if (empty($barcode)) $barcode = null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // (Pharma fields removed — not applicable for kitchen appliances)
    
    // Process Variations
    $var_names = $_POST['var_name'] ?? [];
    $var_values = $_POST['var_value'] ?? [];
    $variations = [];
    for($i=0; $i < count($var_names); $i++) {
        if(!empty($var_names[$i])) {
            $variations[] = ['name' => $var_names[$i], 'value' => $var_values[$i]];
        }
    }
    
    // Process Specs
    $spec_labels = $_POST['spec_label'] ?? [];
    $spec_values = $_POST['spec_value'] ?? [];
    $specifications = [];
    for($i=0; $i < count($spec_labels); $i++) {
        if(!empty($spec_labels[$i])) {
            $specifications[] = ['label' => $spec_labels[$i], 'value' => $spec_values[$i]];
        }
    }

    $main_image = $_POST['existing_image'] ?? '';
    
    // Main Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/pr/';
        if (!is_dir($upload_dir)) {
            if(!mkdir($upload_dir, 0777, true)) {
                $error = "❌ Directory 'assets/uploads/pr/' missing and could not be created.";
            }
        }
        
        if (!is_writable($upload_dir)) {
            $error = "❌ Folder 'assets/uploads/pr/' is not writable. Please fix permissions.";
        } else {
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '-' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name)) {
                $main_image = 'assets/uploads/pr/' . $file_name;
            } else {
                $error = "❌ Failed to move uploaded file. Check server tmp limits.";
            }
        }
    }

    if ($name) {
        try {
            if ($id) {
                $sql = "UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, purchase_price = ?, stock_qty = ?, stock_status = ?, main_image = ?, meta_title = ?, meta_description = ?, variations = ?, specifications = ?, is_featured = ?, barcode = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$category_id, $name, $slug, $description, $price, $purchase_price, $stock_qty, $stock_status, $main_image, $meta_title, $meta_description, json_encode($variations), json_encode($specifications), $is_featured, $barcode, $id]);
                $message = "Product updated successfully!";
            } else {
                $sql = "INSERT INTO products (category_id, name, slug, description, price, purchase_price, stock_qty, stock_status, main_image, meta_title, meta_description, variations, specifications, is_featured, barcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$category_id, $name, $slug, $description, $price, $purchase_price, $stock_qty, $stock_status, $main_image, $meta_title, $meta_description, json_encode($variations), json_encode($specifications), $is_featured, $barcode]);
                $id = $pdo->lastInsertId();
                $message = "Product added successfully!";
            }

            // Save Price Rules
            $pdo->prepare("DELETE FROM price_rules WHERE product_id = ?")->execute([$id]);
            $min_qtys = $_POST['rule_min_qty'] ?? [];
            $rule_types = $_POST['rule_type'] ?? [];
            $rule_values = $_POST['rule_value'] ?? [];
            
            for($i=0; $i < count($min_qtys); $i++) {
                if(!empty($min_qtys[$i]) && $rule_values[$i] > 0) {
                    $insertRule = $pdo->prepare("INSERT INTO price_rules (product_id, min_qty, discount_type, value, is_active) VALUES (?, ?, ?, ?, 1)");
                    $insertRule->execute([$id, $min_qtys[$i], $rule_types[$i], $rule_values[$i]]);
                }
            }

            // Refresh product data
            header("Location: product-edit.php?id=$id&msg=".urlencode($message));
            exit();
        } catch(Exception $e) { $error = "Error: " . $e->getMessage(); }
    }
}

$categories = $pdo->query("SELECT id, name, parent_id FROM categories ORDER BY name ASC")->fetchAll();
$msg = $_GET['msg'] ?? '';

$adminTitle = 'Edit Product';
include_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900"><?php echo $product ? 'Edit Product' : 'Add New Product'; ?></h1>
            <p class="text-sm text-slate-500">Professional management of your kitchen appliances and accessories.</p>
        </div>
        <a href="products.php" class="btn btn-ghost">
            <i class="ph ph-arrow-left"></i>
            Back to Products
        </a>
    </div>

    <?php if($msg): ?> <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-6 font-bold border border-emerald-100 flex items-center gap-3"><i class="ph ph-check-circle text-xl"></i> <?php echo htmlspecialchars($msg); ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-6 font-bold border border-rose-100 flex items-center gap-3"><i class="ph ph-warning-circle text-xl"></i> <?php echo $error; ?></div> <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-3 gap-8">
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title flex items-center gap-2">
                        <i class="ph ph-info text-emerald-500 text-lg"></i>
                        Basic Information
                    </span>
                </div>
                <div class="admin-card-body space-y-5">
                    <div>
                        <label class="admin-label">Product Title</label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                            class="admin-input text-lg font-bold" placeholder="e.g. Premium Stainless Sink">
                    </div>
                    <div>
                        <label class="admin-label">Description</label>
                        <textarea name="description" rows="8" class="admin-input" placeholder="Give a detailed description..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>


            <!-- Advanced Options Toggle -->
            <div onclick="document.getElementById('advanced-options').classList.toggle('hidden')" class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center cursor-pointer hover:bg-slate-50 hover:border-emerald-300 transition group">
                <h3 class="text-sm font-black text-slate-600 group-hover:text-emerald-600 flex items-center justify-center gap-2">
                    <i class="ph ph-gear-six text-lg"></i>
                    Show Advanced Options (Variations, Specs, Wholesale)
                </h3>
            </div>

            <!-- Advanced Options Container -->
            <div id="advanced-options" class="hidden space-y-6">
                <!-- Variations & Specs -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <span class="admin-card-title flex items-center gap-2">
                            <i class="ph ph-stack text-indigo-500 text-lg"></i>
                            Variations & Specifications
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Variations (Size, Color, etc.)</h4>
                            <button type="button" onclick="addVariation()" class="text-indigo-600 font-bold text-xs flex items-center gap-1"><i class="ph ph-plus-circle"></i> ADD VARIATION</button>
                        </div>
                        <div id="variation-container" class="space-y-3 mb-8">
                            <?php 
                            $vars = (!empty($product['variations'])) ? $product['variations'] : [];
                            foreach($vars as $v): ?>
                            <div class="flex gap-3 variation-row">
                                <input type="text" name="var_name[]" value="<?php echo htmlspecialchars($v['name']); ?>" placeholder="e.g. Size" class="admin-input flex-1">
                                <input type="text" name="var_value[]" value="<?php echo htmlspecialchars($v['value']); ?>" placeholder="e.g. Large" class="admin-input flex-1">
                                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 p-2 hover:text-rose-500 transition">✕</button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="flex items-center justify-between mb-4 mt-8">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Technical Specifications</h4>
                            <button type="button" onclick="addSpec()" class="text-blue-600 font-bold text-xs flex items-center gap-1"><i class="ph ph-plus-circle"></i> ADD SPEC</button>
                        </div>
                        <div id="spec-container" class="space-y-3">
                            <?php 
                            $specs = (!empty($product['specifications'])) ? $product['specifications'] : [];
                            foreach($specs as $s): ?>
                            <div class="flex gap-3 spec-row">
                                <input type="text" name="spec_label[]" value="<?php echo htmlspecialchars($s['label']); ?>" placeholder="e.g. Material" class="admin-input flex-1">
                                <input type="text" name="spec_value[]" value="<?php echo htmlspecialchars($s['value']); ?>" placeholder="e.g. Chrome" class="admin-input flex-1">
                                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 p-2 hover:text-rose-500 transition">✕</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Wholesale Price Rules -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <span class="admin-card-title flex items-center gap-2">
                            <i class="ph ph-tag text-rose-500 text-lg"></i>
                            Wholesale & Tiered Pricing
                        </span>
                        <button type="button" onclick="addPriceRule()" class="text-rose-600 font-bold text-xs flex items-center gap-1"><i class="ph ph-plus-circle"></i> ADD RULE</button>
                    </div>
                    <div class="admin-card-body">
                        <div id="price-rule-container" class="space-y-4">
                            <?php 
                            $rules = $priceRules ?? [];
                            foreach($rules as $r): ?>
                            <div class="grid grid-cols-7 gap-3 rule-row items-end">
                                <div class="col-span-2">
                                    <label class="admin-label" style="font-size:10px;">Min Qty</label>
                                    <input type="number" name="rule_min_qty[]" value="<?php echo htmlspecialchars($r['min_qty']); ?>" placeholder="10" class="admin-input">
                                </div>
                                <div class="col-span-2">
                                    <label class="admin-label" style="font-size:10px;">Type</label>
                                    <select name="rule_type[]" class="admin-input">
                                        <option value="fixed" <?php echo $r['discount_type'] == 'fixed' ? 'selected' : ''; ?>>Fixed Price</option>
                                        <option value="percentage" <?php echo $r['discount_type'] == 'percentage' ? 'selected' : ''; ?>>% Discount</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="admin-label" style="font-size:10px;">Value (৳/%)</label>
                                    <input type="number" step="0.01" name="rule_value[]" value="<?php echo htmlspecialchars($r['value']); ?>" placeholder="150" class="admin-input">
                                </div>
                                <div class="col-span-1 text-right">
                                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-slate-400 p-2 hover:text-rose-500 transition">✕</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-4 italic">* If "Fixed Price", it becomes the unit price. If "% Discount", it's deducted from the sale price.</p>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title flex items-center gap-2">
                        <i class="ph ph-globe-hemisphere-east text-sky-500 text-lg"></i>
                        SEO & Meta Data
                    </span>
                </div>
                <div class="admin-card-body space-y-5">
                    <div>
                        <label class="admin-label">Meta Title</label>
                        <input type="text" name="meta_title" value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>" placeholder="SEO Optimized Title" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Meta Description</label>
                        <textarea name="meta_description" rows="3" placeholder="SEO Description..." class="admin-input"><?php echo htmlspecialchars($product['meta_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="space-y-6">
            <!-- Publishing & Visibility -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">Publishing</span>
                </div>
                <div class="admin-card-body">
                    <button type="submit" class="w-full btn btn-primary py-4 text-sm justify-center mb-6">
                        <i class="ph ph-check-circle"></i>
                        <?php echo $product ? 'Save Changes' : 'Publish Product'; ?>
                    </button>
                    
                    <div class="space-y-5">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <label class="text-xs font-black text-slate-600 cursor-pointer" for="is_featured">⭐ Feature on Homepage</label>
                            <input type="checkbox" id="is_featured" name="is_featured" class="w-5 h-5 rounded accent-emerald-500" <?php echo ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                        </div>

                        <div>
                            <label class="admin-label flex items-center justify-between">
                                Barcode (EAN/UPC)
                                <span class="text-[10px] text-slate-400 font-normal italic">Unique required</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="barcode" value="<?php echo htmlspecialchars($product['barcode'] ?? ''); ?>" 
                                       class="admin-input font-mono font-bold tracking-widest pl-10" placeholder="e.g. 1234567890123">
                                <i class="ph ph-barcode absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                            </div>
                        </div>



                        <div>
                            <?php 
                            // Prepare category hierarchy
                            $mainCats = array_filter($categories, fn($c) => empty($c['parent_id']));
                            $subCats = array_filter($categories, fn($c) => !empty($c['parent_id']));
                            
                            $current_main_id = null;
                            if (!empty($product['category_id'])) {
                                foreach($categories as $c) {
                                    if($c['id'] == $product['category_id']) {
                                        $current_main_id = $c['parent_id'] ?: $c['id'];
                                        break;
                                    }
                                }
                            }
                            ?>
                            <label class="admin-label">Product Category</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Main Category Selection -->
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Main Category</label>
                                    <select id="main_category_select" onchange="updateSubCategories()" class="admin-input font-bold bg-slate-50 border-slate-200">
                                        <option value="">Select Main Category</option>
                                        <?php foreach($mainCats as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo ($current_main_id == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Sub-category Selection -->
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Sub-category (Optional)</label>
                                    <select id="sub_category_select" onchange="syncFinalCategory()" class="admin-input font-bold">
                                        <option value="">No Sub-category</option>
                                        <?php foreach($subCats as $sub): ?>
                                            <option value="<?php echo $sub['id']; ?>" data-parent="<?php echo $sub['parent_id']; ?>" 
                                                    <?php echo (($product['category_id'] ?? null) == $sub['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sub['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- This is the actual value that will be submitted to PHP -->
                            <input type="hidden" name="category_id" id="final_category_id" value="<?php echo $product['category_id'] ?? ''; ?>">
                        </div>

                        <script>
                        function updateSubCategories() {
                            const mainId = document.getElementById('main_category_select').value;
                            const subSelect = document.getElementById('sub_category_select');
                            
                            // Reset sub-category select
                            subSelect.value = "";
                            
                            // Filter sub-categories based on data-parent attribute
                            let hasSub = false;
                            Array.from(subSelect.options).forEach(opt => {
                                if (opt.value === "") {
                                    opt.hidden = false;
                                } else if (opt.dataset.parent == mainId) {
                                    opt.hidden = false;
                                    hasSub = true;
                                } else {
                                    opt.hidden = true;
                                }
                            });
                            
                            syncFinalCategory();
                        }

                        function syncFinalCategory() {
                            const mainId = document.getElementById('main_category_select').value;
                            const subId = document.getElementById('sub_category_select').value;
                            const finalInput = document.getElementById('final_category_id');
                            
                            // If sub-category is selected, use it, otherwise use main category
                            finalInput.value = subId || mainId;
                        }

                        // Initialize sub-categories on page load
                        window.addEventListener('DOMContentLoaded', () => {
                            if (document.getElementById('main_category_select').value) {
                                const mainId = document.getElementById('main_category_select').value;
                                const subSelect = document.getElementById('sub_category_select');
                                const currentSubId = "<?php echo $product['category_id'] ?? ''; ?>";
                                
                                Array.from(subSelect.options).forEach(opt => {
                                    if (opt.value !== "" && opt.dataset.parent != mainId) {
                                        opt.hidden = true;
                                    }
                                });
                            }
                        });
                        </script>
                        <div>
                            <label class="admin-label">Sale Price (৳)</label>
                            <input type="number" name="price" value="<?php echo $product['price'] ?? 0; ?>" class="admin-input font-black text-emerald-600 bg-emerald-50/30">
                        </div>
                        <div>
                            <label class="admin-label">Purchase Cost (৳)</label>
                            <input type="number" step="0.01" name="purchase_price" value="<?php echo $product['purchase_price'] ?? 0; ?>" class="admin-input font-bold text-slate-500 bg-slate-50/50">
                        </div>
                        <div>
                            <label class="admin-label">Stock Quantity</label>
                            <input type="number" name="stock_qty" value="<?php echo $product['stock_qty'] ?? 0; ?>" class="admin-input font-bold bg-slate-50">
                        </div>
                        <div>
                            <label class="admin-label">Availability</label>
                            <select name="stock_status" class="admin-input font-bold">
                                <option value="In Stock" <?php echo ($product['stock_status'] ?? '') == 'In Stock' ? 'selected' : ''; ?>>In Stock</option>
                                <option value="Out of Stock" <?php echo ($product['stock_status'] ?? '') == 'Out of Stock' ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Image -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">Featured Image</span>
                </div>
                <div class="admin-card-body">
                    <div class="border-2 border-dashed border-slate-100 rounded-2xl p-4 text-center">
                        <?php 
                        $previewImg = !empty($product['image'] ?? null) ? '../' . $product['image'] : null;
                        ?>
                        <?php if ($previewImg): ?>
                            <img id="image-preview" src="<?php echo $previewImg; ?>" class="w-full h-48 object-contain rounded-xl mb-4 bg-slate-50"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <?php endif; ?>
                        
                        <div id="image-fallback" style="display: <?php echo $previewImg ? 'none' : 'flex'; ?>; width:100%; height:12rem; border-radius:12px; background:linear-gradient(135deg, #fff1f2, #ffe4e6); border:1px solid #fecdd3; align-items:center; justify-content:center; flex-direction:column; gap:0.5rem; margin-bottom:1rem;">
                            <i class="ph ph-image-square text-rose-400 text-4xl" style="margin:auto 0 0;"></i>
                            <span style="font-size:0.625rem; font-weight:800; color:#fb7185; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 auto;">No Product Image</span>
                        </div>
                        
                        <label class="cursor-pointer block">
                            <span class="btn btn-ghost text-[10px] py-2">Select New Image</span>
                            <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </label>
                        <input type="hidden" name="existing_image" value="<?php echo $product['image'] ?? ''; ?>">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            const fallback = document.getElementById('image-fallback');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            if (fallback) fallback.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function addVariation() {
    const container = document.getElementById('variation-container');
    const div = document.createElement('div');
    div.className = 'flex gap-3 variation-row';
    div.innerHTML = `
        <input type="text" name="var_name[]" placeholder="e.g. Size" class="admin-input flex-1">
        <input type="text" name="var_value[]" placeholder="e.g. Large" class="admin-input flex-1">
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 p-2 hover:text-rose-500 transition">✕</button>
    `;
    container.appendChild(div);
}
function addSpec() {
    const container = document.getElementById('spec-container');
    const div = document.createElement('div');
    div.className = 'flex gap-3 spec-row';
    div.innerHTML = `
        <input type="text" name="spec_label[]" placeholder="e.g. Material" class="admin-input flex-1">
        <input type="text" name="spec_value[]" placeholder="e.g. Chrome" class="admin-input flex-1">
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 p-2 hover:text-rose-500 transition">✕</button>
    `;
    container.appendChild(div);
}
function addPriceRule() {
    const container = document.getElementById('price-rule-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-7 gap-3 rule-row items-end';
    div.innerHTML = `
        <div class="col-span-2">
            <label class="admin-label" style="font-size:10px;">Min Qty</label>
            <input type="number" name="rule_min_qty[]" placeholder="10" class="admin-input">
        </div>
        <div class="col-span-2">
            <label class="admin-label" style="font-size:10px;">Type</label>
            <select name="rule_type[]" class="admin-input">
                <option value="fixed">Fixed Price</option>
                <option value="percentage">% Discount</option>
            </select>
        </div>
        <div class="col-span-2">
            <label class="admin-label" style="font-size:10px;">Value</label>
            <input type="number" step="0.01" name="rule_value[]" placeholder="150" class="admin-input">
        </div>
        <div class="col-span-1 text-right">
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 p-2 hover:text-rose-500 transition">✕</button>
        </div>
    `;
    container.appendChild(div);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
