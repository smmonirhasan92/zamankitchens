<?php
/**
 * Zaman Kitchens - Homepage
 * BD-Style layout: Featured Row, Sink Row, Accessories Row
 */
require_once __DIR__ . '/includes/db.php';
include_once __DIR__ . '/includes/header.php';

// Fetch all categories for dropdown (already used in header)
// Fetch Featured Products
$featured = [];
try {
    $stmt = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 8");
    $featured = $stmt->fetchAll();
} catch(Exception $e) {}

// Fetch products by specific categories for "BD Style" rows
$categoryRows = [];
$rowCategories = ['sink', 'kitchen-accessories', 'kitchen-hood', 'gas-stove'];
foreach ($rowCategories as $slug) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name AS cat_name, c.hero_image AS cat_banner FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.slug = ? ORDER BY p.created_at DESC LIMIT 4");
        $stmt->execute([$slug]);
        $rows = $stmt->fetchAll();
        if (!empty($rows)) {
            $categoryRows[$slug] = [
                'name' => $rows[0]['cat_name'],
                'banner' => $rows[0]['cat_banner'],
                'products' => $rows
            ];
        }
    } catch(Exception $e) {}
}
?>

<!-- ===========================
     HERO SLIDER SECTION
=========================== -->
<section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-amber-900 text-white overflow-hidden" style="min-height: 520px;">
    <div class="absolute inset-0 opacity-20" style="background-image: url('https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=1600'); background-size: cover; background-position: center;"></div>
    <div class="relative z-10 container mx-auto px-4 py-24 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1">
            <span class="inline-block bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-4 tracking-widest uppercase">Premium Kitchen Solutions</span>
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">
                Transform Your<br>
                <span class="text-amber-400">Kitchen Space</span>
            </h1>
            <p class="text-gray-300 text-lg mb-8 max-w-xl">Bangladesh's finest collection of kitchen sinks, cabinets, hoods and accessories. Trusted by 5,000+ homes.</p>
            <div class="flex flex-wrap gap-4">
                <a href="#featured" class="bg-amber-500 hover:bg-amber-400 text-white font-bold px-8 py-4 rounded-xl transition shadow-lg shadow-amber-500/30">Shop Now</a>
                <a href="tel:01700000000" class="border border-white/30 hover:bg-white/10 text-white font-bold px-8 py-4 rounded-xl transition">📞 Call Us</a>
            </div>
            <div class="mt-8 flex gap-8 text-center">
                <div><div class="text-2xl font-extrabold text-amber-400">5K+</div><div class="text-xs text-gray-400">Happy Customers</div></div>
                <div><div class="text-2xl font-extrabold text-amber-400">200+</div><div class="text-xs text-gray-400">Products</div></div>
                <div><div class="text-2xl font-extrabold text-amber-400">11</div><div class="text-xs text-gray-400">Categories</div></div>
            </div>
        </div>
        <div class="flex-1 hidden md:block">
            <img src="<?php echo ASSETS_PATH; ?>/images/hero.png" alt="Premium Kitchen" class="rounded-2xl shadow-2xl w-full max-w-md ml-auto object-cover" style="height:380px;">
        </div>
    </div>
</section>

<!-- ===========================
     CATEGORY QUICK NAV
=========================== -->
<section class="bg-white border-b py-6 sticky top-0 z-30 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex gap-3 overflow-x-auto pb-1 scrollbar-hide snap-x">
            <?php
            try {
                $cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
                foreach ($cats as $cat):
            ?>
            <a href="category/<?php echo $cat['slug']; ?>" class="snap-start flex-shrink-0 bg-gray-50 hover:bg-amber-50 border border-gray-100 hover:border-amber-400 text-gray-700 hover:text-amber-700 text-sm font-semibold px-5 py-2.5 rounded-full transition whitespace-nowrap">
                <?php echo $cat['name']; ?>
            </a>
            <?php endforeach; } catch(Exception $e) {} ?>
        </div>
    </div>
</section>

<!-- ===========================
     FEATURED PRODUCTS ROW
=========================== -->
<?php if (!empty($featured)): ?>
<section id="featured" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">⭐ Featured Products</h2>
                <p class="text-gray-500 mt-1">Handpicked bestsellers from our collection</p>
            </div>
            <a href="#" class="text-amber-600 font-semibold hover:underline hidden md:block">View All &rarr;</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php foreach($featured as $p): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===========================
     CATEGORY-WISE ROWS
=========================== -->
<?php foreach($categoryRows as $slug => $row): ?>
<?php if (!empty($row['products'])): ?>
<section class="py-12 <?php echo $slug === 'sink' ? 'bg-white' : 'bg-gray-50'; ?>" id="cat-<?php echo $slug; ?>">
    <div class="container mx-auto px-4">
        <!-- Category Banner -->
        <?php if ($row['banner']): ?>
        <div class="relative mb-8 rounded-2xl overflow-hidden h-40" style="background: url('<?php echo $row['banner']; ?>') center/cover">
            <div class="absolute inset-0 bg-black/50 flex items-center px-8">
                <div>
                    <h2 class="text-2xl font-extrabold text-white"><?php echo $row['name']; ?></h2>
                    <a href="category/<?php echo $slug; ?>" class="text-amber-400 text-sm mt-1 inline-block">View all &rarr;</a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900"><?php echo $row['name']; ?></h2>
            </div>
            <a href="category/<?php echo $slug; ?>" class="text-amber-600 font-semibold hover:underline">View All &rarr;</a>
        </div>
        <?php endif; ?>

        <!-- Product Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php foreach($row['products'] as $p): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endforeach; ?>

<!-- Why Choose Us -->
<section class="py-16 bg-gray-900 text-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-extrabold text-center mb-12">Why <span class="text-amber-400">Zaman Kitchens?</span></h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <?php
            $features = [
                ['🚚', 'Fast Delivery', 'Dhaka same-day, nationwide 2-3 days'],
                ['🛡️', 'Warranty', 'All products carry manufacturer warranty'],
                ['💬', 'Expert Support', 'WhatsApp & phone support 10am-8pm'],
                ['💳', 'Easy Payment', 'Cash on delivery + bKash/Nagad'],
            ];
            foreach($features as $f):
            ?>
            <div class="p-6 rounded-xl bg-white/5 hover:bg-white/10 transition">
                <div class="text-4xl mb-3"><?php echo $f[0]; ?></div>
                <h3 class="font-bold text-lg mb-2"><?php echo $f[1]; ?></h3>
                <p class="text-gray-400 text-sm"><?php echo $f[2]; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
