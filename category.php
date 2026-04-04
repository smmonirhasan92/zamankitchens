<?php
/**
 * Zaman Kitchens - Category Page
 * Features: Category hero banner, product grid with pagination
 */
require_once __DIR__ . '/includes/db.php';

$slug = $_GET['slug'] ?? basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$category = null;
$products = [];

if ($slug) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $category = $stmt->fetch();

        if ($category) {
            // Find sub-categories if any
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ?");
            $stmt->execute([$category['id']]);
            $sub_categories = $stmt->fetchAll();
            
            // Collect all category IDs to fetch products from (this category + sub-categories)
            $cat_ids = [$category['id']];
            foreach ($sub_categories as $sub) {
                $cat_ids[] = $sub['id'];
            }
            
            // Build the query with placeholders
            $placeholders = implode(',', array_fill(0, count($cat_ids), '?'));
            $sql = "SELECT * FROM products WHERE category_id IN ($placeholders) ORDER BY is_featured DESC, created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($cat_ids);
            $products = $stmt->fetchAll();
            
            // Fetch parent if current is a sub-category
            $parent_category = null;
            if ($category['parent_id']) {
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
                $stmt->execute([$category['parent_id']]);
                $parent_category = $stmt->fetch();
            }
        }
    } catch(Exception $e) {}
}

if (!$category) {
    header("Location: " . SITE_URL);
    exit();
}

$pageTitle = htmlspecialchars($category['name']);
include_once __DIR__ . '/includes/header.php';
?>

<!-- Category Hero Banner -->
<div class="relative overflow-hidden" style="min-height: 220px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
    <?php if (!empty($category['hero_image'])): ?>
    <img src="<?php echo htmlspecialchars($category['hero_image']); ?>" class="absolute inset-0 w-full h-full object-cover opacity-30" alt="">
    <?php endif; ?>
    <div class="relative z-10 container mx-auto px-4 py-16 flex flex-col items-center justify-center text-center">
        <nav class="text-white/60 text-[10px] font-black uppercase tracking-widest mb-6 flex items-center gap-2 justify-center">
            <a href="<?php echo SITE_URL; ?>" class="hover:text-red-500 transition">Home</a>
            <i class="ph ph-caret-right text-[8px] opacity-40"></i>
            <?php if ($parent_category): ?>
                <a href="<?php echo SITE_URL; ?>/category/<?php echo $parent_category['slug']; ?>" class="hover:text-red-500 transition"><?php echo htmlspecialchars($parent_category['name']); ?></a>
                <i class="ph ph-caret-right text-[8px] opacity-40"></i>
            <?php endif; ?>
            <span class="text-white"><?php echo htmlspecialchars($category['name']); ?></span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-4 uppercase tracking-tighter"><?php echo htmlspecialchars($category['name']); ?></h1>
        <p class="text-sky-100/60 font-medium tracking-wide"><?php echo count($products); ?> Products available in this category</p>
        
        <?php if (!empty($sub_categories)): ?>
        <div class="flex flex-wrap justify-center gap-3 mt-8">
            <?php foreach ($sub_categories as $sub): ?>
                <a href="<?php echo SITE_URL; ?>/category/<?php echo $sub['slug']; ?>" 
                   class="bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-full transition-all">
                    <?php echo htmlspecialchars($sub['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Products Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">

        <?php if (empty($products)): ?>
        <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
            <div class="text-6xl mb-4">📦</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">No Products Yet</h2>
            <p class="text-gray-500">We're stocking up this category. Check back soon!</p>
            <a href="<?php echo SITE_URL; ?>" class="mt-6 inline-block bg-red-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-red-700 transition">Back to Home</a>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach($products as $p): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
