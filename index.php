<?php
/**
 * Zaman Kitchens - Homepage
 * BD-Style layout: Featured Row, Sink Row, Accessories Row
 */
require_once __DIR__ . '/includes/db.php';
include_once __DIR__ . '/includes/header.php';

// Fetch Hero Slides
$slides = [];
try {
    $slides = $pdo->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll();
} catch(Exception $e) {}

// Fetch 8-12 Categories for Grid
$gridCats = [];
try {
    $gridCats = $pdo->query("SELECT * FROM categories ORDER BY id ASC LIMIT 11")->fetchAll();
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
     HERO SLIDER SECTION (Dynamic)
=========================== -->
<section class="relative bg-gray-900 overflow-hidden">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            <?php if (empty($slides)): ?>
            <!-- Fallback Slide -->
            <div class="swiper-slide relative flex items-center min-h-[520px] bg-gradient-to-br from-gray-900 via-gray-800 to-amber-900 text-white">
                <div class="absolute inset-0 opacity-20" style="background-image: url('https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=1600'); background-size: cover; background-position: center;"></div>
                <div class="relative z-10 container mx-auto px-4 py-24 flex flex-col md:flex-row items-center gap-12">
                    <div class="flex-1">
                        <span class="inline-block bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-4 tracking-widest uppercase">Premium Kitchen Solutions</span>
                        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">Transform Your <span class="text-amber-400">Kitchen Space</span></h1>
                        <p class="text-gray-300 text-lg mb-8 max-w-xl">Bangladesh's finest collection of kitchen sinks, cabinets, hoods and accessories.</p>
                        <a href="#featured" class="bg-amber-500 hover:bg-amber-400 text-white font-bold px-8 py-4 rounded-xl transition shadow-lg inline-block">Shop Now</a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <?php foreach($slides as $slide): ?>
            <div class="swiper-slide relative flex items-center min-h-[520px] bg-gray-900 text-white">
                <!-- Background Image -->
                <div class="absolute inset-0">
                    <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" alt="" class="w-full h-full object-cover opacity-40">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/40 to-transparent"></div>
                </div>
                <!-- Content -->
                <div class="relative z-10 container mx-auto px-4 py-24">
                    <div class="max-w-2xl">
                        <h2 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4"><?php echo htmlspecialchars($slide['title']); ?></h2>
                        <p class="text-gray-300 text-lg mb-8"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                        <a href="<?php echo htmlspecialchars($slide['button_link']); ?>" class="bg-amber-500 hover:bg-amber-400 text-white font-bold px-8 py-4 rounded-xl transition shadow-lg inline-block">
                            <?php echo htmlspecialchars($slide['button_text']); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- Pagination/Navigation -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next !text-white/50 hover:!text-white"></div>
        <div class="swiper-button-prev !text-white/50 hover:!text-white"></div>
    </div>
</section>

<!-- ===========================
     SHOP BY CATEGORY GRID
=========================== -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Shop by Category</h2>
            <p class="text-gray-500">Explore our wide range of premium products</p>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php foreach($gridCats as $cat): ?>
            <a href="category/<?php echo $cat['slug']; ?>" class="group flex flex-col items-center text-center">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full overflow-hidden bg-gray-50 border-2 border-gray-100 group-hover:border-amber-500 transition-all duration-300 mb-4 shadow-sm group-hover:shadow-md">
                    <img src="<?php echo htmlspecialchars($cat['image'] ?? 'https://placehold.co/400x400/f5f5f5/aaa?text='.$cat['name']); ?>" 
                         alt="<?php echo htmlspecialchars($cat['name']); ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <h3 class="text-sm md:text-base font-bold text-gray-800 group-hover:text-amber-600 transition tracking-tight">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Initialize Swiper -->
<script>
    const swiper = new Swiper('.heroSwiper', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        effect: 'fade',
        fadeEffect: { crossFade: true }
    });
</script>

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
