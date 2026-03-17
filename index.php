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

// Fetch top 4-5 categories that have products to show as rows
$categoryRows = [];
try {
    // Get categories that have products
    $catsWithProducts = $pdo->query("SELECT c.* FROM categories c WHERE (SELECT COUNT(*) FROM products WHERE category_id = c.id) > 0 LIMIT 6")->fetchAll();
    
    foreach ($catsWithProducts as $cat) {
        $stmt = $pdo->prepare("SELECT p.*, c.name AS cat_name, c.hero_image AS cat_banner FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.id = ? ORDER BY p.created_at DESC LIMIT 4");
        $stmt->execute([$cat['id']]);
        $rows = $stmt->fetchAll();
        if (!empty($rows)) {
            $categoryRows[$cat['slug']] = [
                'name' => $cat['name'],
                'banner' => $cat['hero_image'],
                'products' => $rows
            ];
        }
    }
} catch(Exception $e) {}
?>

<!-- ===========================
     HERO SLIDER SECTION (Gazi Style)
=========================== -->
<style>
    .heroSwiper { width: 100%; height: auto; }
    .hero-slide { position: relative; width: 100%; aspect-ratio: 1920/600; overflow: hidden; }
    @media (max-width: 768px) { .hero-slide { aspect-ratio: 16/9; } }
    .hero-slide img { width: 100%; h-full; object-fit: cover; }
    .swiper-button-next, .swiper-button-prev { color: #d80032 !important; background: white; width: 40px; height: 40px; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .swiper-button-next:after, .swiper-button-prev:after { font-size: 18px !important; font-weight: bold; }
    .swiper-pagination-bullet-active { background: #d80032 !important; }
</style>

<section class="relative bg-gray-50">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            <?php if (!empty($slides)): ?>
                <?php foreach($slides as $slide): ?>
                <div class="swiper-slide">
                    <div class="hero-slide">
                        <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" alt="<?php echo htmlspecialchars($slide['title'] ?? ''); ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Slide -->
                <div class="swiper-slide">
                    <div class="hero-slide">
                        <img src="image/slider.png" alt="Zaman Kitchens Slider">
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- ===========================
     Gazi-style Feature Row
=========================== -->
<section class="py-8 bg-white border-b border-slate-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="flex items-center gap-4 group">
                <img src="image/02eQFmMczupMD8eID5AjVTQjpW6uCYOSHor0V6rg.jpg" alt="COD" class="h-10 w-auto group-hover:scale-110 transition-transform">
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase leading-none mb-1">ক্যাশ অন ডেলিভারিতে</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ক্রয়ের সুবিধা</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="flex items-center gap-4 group">
                <img src="image/04AZ86yvpyaFt2jfR5XseGIMi3QVU1LOafCFrkTH.png" alt="Free Delivery" class="h-10 w-auto group-hover:scale-110 transition-transform">
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase leading-none mb-1">ফ্রি হোম ডেলিভারি</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">সমগ্র বাংলাদেশে</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="flex items-center gap-4 group">
                <img src="https://www.zamankitchens.com/assets/logo.png" alt="Zaman Kitchens" class="h-10 w-auto group-hover:scale-110 transition-transform">
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase leading-none mb-1">EMI সুবিধা</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">২৮টি ব্যাংকের সাথে</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="flex items-center gap-4 group">
                <img src="image/call_icon.png" alt="Call" class="h-10 w-auto group-hover:scale-110 transition-transform">
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase leading-none mb-1">কল করুন</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">01766688840</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===========================
     Gazi-style Circular Categories
=========================== -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center text-center mb-8">
            <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tighter mb-2">
                Explore Categories
            </h2>
            <div class="h-1 w-12 bg-red-600 rounded-full mb-4"></div>
            <a href="categories.php" class="text-xs font-black text-red-600 hover:text-red-700 uppercase tracking-widest flex items-center gap-1">
                View All <i class="ph-bold ph-caret-right"></i>
            </a>
        </div>

        <div class="flex items-center justify-center gap-6 md:gap-10 overflow-x-auto pb-6 scrollbar-hide">
            <?php foreach($gridCats as $cat): 
                $catImg = !empty($cat['hero_image']) ? $cat['hero_image'] : 'https://placehold.co/150x150/f1f5f9/94a3b8?text=' . urlencode($cat['name']);
            ?>
            <a href="category/<?php echo $cat['slug']; ?>" class="flex-shrink-0 group text-center w-24 md:w-32">
                <div class="w-20 h-20 md:w-28 md:h-28 rounded-full bg-white border border-slate-100 shadow-sm group-hover:shadow-md group-hover:border-red-500 transition-all duration-300 flex items-center justify-center overflow-hidden mb-3 mx-auto">
                    <img src="<?php echo htmlspecialchars($catImg); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                         class="w-4/5 h-4/5 object-contain group-hover:scale-110 transition-transform duration-500">
                </div>
                <span class="block text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-700 group-hover:text-red-600 transition-colors leading-tight">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===========================
     CATEGORIZED PRODUCT ROWS (Gazi Style)
=========================== -->
<?php foreach($categoryRows as $slug => $row): ?>
<section class="py-12 <?php echo $slug === 'sink' ? 'bg-gray-50' : 'bg-white'; ?>">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center text-center mb-8">
            <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tighter mb-2">
                <?php echo htmlspecialchars($row['name']); ?>
            </h2>
            <div class="h-1 w-16 bg-red-600 rounded-full mb-4"></div>
            <a href="category/<?php echo $slug; ?>" class="text-xs font-black text-slate-400 hover:text-red-600 uppercase tracking-widest flex items-center gap-1 transition-colors">
                Explore More <i class="ph-bold ph-caret-right"></i>
            </a>
        </div>

        <div id="grid-<?php echo $slug; ?>" class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
            <?php foreach($row['products'] as $p): ?>
            <div class="product-item">
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endforeach; ?>

<!-- ===========================
     Gazi-style Campaign Banners
=========================== -->
<section class="py-8 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group">
                <img src="image/beMuLKFPhoWqTU4T0eM9Tz5jplszOBA01y8OinWO.png" class="w-full h-auto group-hover:scale-105 transition-transform duration-700">
            </div>
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group">
                <img src="image/hEG14FkfLraHd7zzM8sUgEcTTWgjNwaDeBq63ENr.png" class="w-full h-auto group-hover:scale-105 transition-transform duration-700">
            </div>
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group">
                <img src="image/lkQZqSkZKmUHgIW7UTqhDcxZEMGVgBIPvphNpejU.png" class="w-full h-auto group-hover:scale-105 transition-transform duration-700">
            </div>
            <div class="rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group">
                <img src="image/niwd6fySuspYlJWj4bylyLkEl6ft4P1ddbPQgavu.png" class="w-full h-auto group-hover:scale-105 transition-transform duration-700">
            </div>
        </div>
    </div>
</section>

<!-- ===========================
     QUICK FILTER SECTION (Gazi Style)
=========================== -->
<section class="py-16 bg-slate-900 relative overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-0 left-0 w-64 h-64 bg-red-600 rounded-full blur-[100px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-red-600 rounded-full blur-[100px] translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-4 tracking-tighter uppercase">All Products</h2>
            <div class="h-1 w-20 bg-red-600 mx-auto rounded-full"></div>
        </div>

        <div id="product-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
            <?php 
            $allProducts = $pdo->query("SELECT p.*, c.slug AS cat_slug FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 8")->fetchAll();
            foreach($allProducts as $p): 
            ?>
            <div class="product-item">
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="search.php" class="inline-flex items-center gap-3 bg-red-600 hover:bg-red-700 text-white font-black px-10 py-4 rounded-full transition-all shadow-xl hover:-translate-y-1">
                SEE ALL PRODUCTS
                <i class="ph-bold ph-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<script>
    // Initialize Swiper
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
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">⭐ Featured Products</h2>
            <p class="text-gray-500 mt-2">Handpicked bestsellers from our collection</p>
            <div class="h-1 w-16 bg-red-600 mx-auto rounded-full mt-4"></div>
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
                    <a href="category/<?php echo $slug; ?>" class="text-white bg-red-600/20 hover:bg-red-600 px-3 py-1 rounded-lg text-xs mt-2 inline-block transition">View all &rarr;</a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center text-center mb-8">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2"><?php echo $row['name']; ?></h2>
            <div class="h-1 w-16 bg-red-600 rounded-full mb-4"></div>
            <a href="category/<?php echo $slug; ?>" class="text-red-600 font-semibold hover:underline">View All &rarr;</a>
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

<!-- Quick Inquiry Form: COMPACT & SLIM -->
<section class="py-12 bg-white" id="inquiry">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-slate-900 rounded-[2rem] overflow-hidden flex flex-col md:flex-row shadow-2xl">
            <div class="md:w-2/5 p-8 bg-gradient-to-br from-slate-900 to-slate-800 text-white border-r border-white/5">
                <h2 class="text-2xl font-black mb-4"><span class="text-red-500">Custom</span> Design?</h2>
                <p class="text-slate-400 text-xs mb-6 leading-relaxed">Our experts will find the perfect fit for your kitchen.</p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm font-bold">
                        <span class="text-red-600">📞</span> +880 1700-000000
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold">
                        <span class="text-red-600">📍</span> Uttara, Dhaka
                    </div>
                </div>
            </div>
            <div class="md:w-3/5 p-8">
                <form action="api/submit_lead.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="name" required placeholder="Name" class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:border-red-500 transition text-sm">
                    <input type="text" name="phone" required placeholder="Phone" class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:border-red-500 transition text-sm">
                    <input type="text" name="message" placeholder="Requirements" class="md:col-span-2 w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:border-red-500 transition text-sm">
                    <button type="submit" class="md:col-span-2 bg-red-600 hover:bg-red-700 text-white font-black py-3 rounded-xl transition shadow-lg shadow-red-600/20 text-sm">
                        Send Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us — Monochrome Red -->
<section class="py-24 relative overflow-hidden" style="background: linear-gradient(145deg, #2b2d42 0%, #1a1b2e 60%, #0f0f1a 100%);">
    <div class="absolute inset-0 hero-grid-bg"></div>
    <div class="floating-blob1 absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full opacity-10"
         style="background: radial-gradient(circle, #ef233c 0%, transparent 70%);"></div>
    <div class="floating-blob2 absolute -bottom-40 -left-20 w-[500px] h-[500px] rounded-full opacity-10"
         style="background: radial-gradient(circle, #d80032 0%, transparent 70%);"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <span class="inline-block text-[10px] font-black uppercase tracking-[0.35em] px-6 py-2 rounded-full mb-6"
                  style="border: 1px solid rgba(239,35,60,0.4); color: #ef233c; background: rgba(239,35,60,0.1);">Why Us</span>
            <h2 class="text-4xl md:text-5xl font-black tracking-tight" style="color: #edf2f4;">
                Why <span style="background: linear-gradient(135deg, #ef233c, #d80032); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Zaman Kitchens?</span>
            </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            $features = [
                ['🚚', 'Fast Delivery', 'Dhaka same-day, nationwide 2-3 days', 'rgba(239,35,60,0.1)', '#ef233c'],
                ['🛡️', 'Full Warranty', 'All products carry manufacturer warranty', 'rgba(216,0,50,0.1)', '#d80032'],
                ['💬', 'Expert Support', 'WhatsApp & phone support 10am–8pm', 'rgba(141,153,174,0.1)', '#8d99ae'],
                ['💳', 'Easy Payment', 'Cash on delivery + bKash/Nagad', 'rgba(237,242,244,0.05)', '#edf2f4'],
            ];
            foreach($features as $f):
            ?>
            <div class="group p-8 rounded-3xl transition-all duration-300 hover:-translate-y-2 cursor-default"
                 style="background: <?php echo $f[3]; ?>; border: 1px solid rgba(255,255,255,0.06);">
                <div class="text-5xl mb-5"><?php echo $f[0]; ?></div>
                <h3 class="font-black text-lg mb-2" style="color: #edf2f4;"><?php echo $f[1]; ?></h3>
                <p class="text-sm leading-relaxed" style="color: #8d99ae;"><?php echo $f[2]; ?></p>
                <div class="mt-5 w-8 h-1 rounded-full transition-all duration-300 group-hover:w-16"
                     style="background: <?php echo $f[4]; ?>;"></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===========================
     COMPARE MODAL (Glassmorphism)
=========================== -->
<div id="compare-modal" class="fixed inset-0 z-[115] hidden items-center justify-center p-4 md:p-10">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCompareModal()"></div>
    <div class="relative bg-slate-50 w-full max-w-6xl h-full max-h-[90vh] rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col border border-white/20">
        <div class="p-6 md:p-8 bg-white border-b border-slate-100 flex items-center justify-between shrink-0">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3"><span class="text-red-600">⇄</span> Product Comparison</h2>
            <button onclick="closeCompareModal()" class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center hover:bg-slate-200 text-slate-600 transition font-bold">✕</button>
        </div>
        <div class="flex-1 overflow-x-auto p-6 md:p-8">
            <div id="compare-grid" class="flex gap-6 w-max min-w-full">
                <!-- Comparison columns injected here -->
            </div>
        </div>
    </div>
</div>

<!-- ===========================
     QUICK VIEW MODAL (Glassmorphism)
=========================== -->
<div id="quick-view-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 md:p-10">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeQuickView()"></div>
    <div id="qv-content" class="relative bg-white/90 backdrop-blur-xl w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row transition-all duration-500 transform scale-90 opacity-0 border border-white/20">
        <!-- Content will be injected via JS -->
    </div>
</div>

<!-- ===========================
     SIDE CART (Sliding Drawer)
=========================== -->
<div id="side-cart" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl z-[110] transform translate-x-full transition-transform duration-500 ease-in-out border-l border-slate-100 flex flex-col">
    <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-900 text-white">
        <h2 class="text-xl font-black italic tracking-tight">SHOPPING BAG</h2>
        <button onclick="toggleCart()" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-white/10 transition">✕</button>
    </div>
    <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-6">
        <!-- Cart items injected here -->
        <div class="text-center py-12 text-slate-400">
            <div class="text-4xl mb-4">🛍️</div>
            <p class="font-bold">Your bag is empty</p>
            <p class="text-xs mt-2">Add some kitchen magic to get started!</p>
        </div>
    </div>
    <div class="p-6 bg-slate-50 border-t border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <span class="text-slate-500 font-bold uppercase tracking-widest text-xs">Total</span>
            <span id="cart-total" class="text-2xl font-black text-slate-900">৳ 0</span>
        </div>
        <button onclick="toggleCart(); openCheckout();" class="w-full bg-red-600 hover:bg-red-700 text-white text-center font-black py-4 rounded-2xl transition shadow-xl shadow-red-600/20 block">
            Checkout Now
        </button>
    </div>
</div>

<script>
    // Global State
    let cart = JSON.parse(localStorage.getItem('zk_cart')) || [];
    let wishlist = JSON.parse(localStorage.getItem('zk_wishlist')) || [];
    let compareList = JSON.parse(localStorage.getItem('zk_compare')) || [];

    // Compare Logic
    function toggleCompare(product) {
        event.stopPropagation();
        const index = compareList.findIndex(item => item.id == product.id);
        if (index > -1) {
            compareList.splice(index, 1);
        } else {
            if (compareList.length >= 3) {
                alert('You can only compare up to 3 products at a time.');
                return;
            }
            compareList.push(product);
        }
        localStorage.setItem('zk_compare', JSON.stringify(compareList));
        renderCompareBar();
    }

    function renderCompareBar() {
        let bar = document.getElementById('compare-bar');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'compare-bar';
            bar.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[90] bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-6 transform transition-all duration-300 translate-y-24';
            bar.innerHTML = `
                <div class="flex items-center gap-2 font-bold text-sm">
                    <span><span id="compare-count" class="text-red-500">0</span> Items to Compare</span>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openCompareModal()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl text-sm font-black transition">Compare Now</button>
                    <button onclick="clearCompare()" class="text-slate-400 hover:text-white transition">✕</button>
                </div>
            `;
            document.body.appendChild(bar);
        }

        const countEl = document.getElementById('compare-count');
        if (countEl) countEl.innerText = compareList.length;

        if (compareList.length > 0) {
            bar.classList.remove('translate-y-24');
        } else {
            bar.classList.add('translate-y-24');
        }
    }

    function clearCompare() {
        compareList = [];
        localStorage.setItem('zk_compare', JSON.stringify(compareList));
        renderCompareBar();
    }

    function openCompareModal() {
        if (compareList.length < 2) {
            alert('Please select at least 2 products to compare.');
            return;
        }
        const modal = document.getElementById('compare-modal');
        const grid = document.getElementById('compare-grid');
        
        let html = '';
        compareList.forEach(p => {
            html += `
                <div class="bg-white p-6 rounded-3xl border border-slate-100 flex-1 relative min-w-[280px]">
                    <button onclick="toggleCompare({id: ${p.id}}); openCompareModal();" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 flex items-center justify-center transition">✕</button>
                    <img src="${p.image}" class="w-full h-48 object-cover rounded-2xl mb-6 bg-slate-50">
                    <h3 class="font-black text-slate-900 text-lg mb-2 leading-tight">${p.name}</h3>
                    <div class="text-2xl font-black text-red-600 mb-6">৳ ${parseInt(p.price).toLocaleString()}</div>
                    
                    <div class="space-y-4 text-sm">
                        <div class="border-t border-slate-100 pt-4">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Brand</span>
                            <span class="font-bold text-slate-700">Zaman Premium</span>
                        </div>
                        <div class="border-t border-slate-100 pt-4">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Description</span>
                            <span class="text-slate-600 line-clamp-3 leading-relaxed">${p.description}</span>
                        </div>
                        <div class="border-t border-slate-100 pt-4">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Stock Status</span>
                            <span class="font-bold text-emerald-600">In Stock</span>
                        </div>
                    </div>
                    
                    <button onclick="closeCompareModal(); buyNow(${JSON.stringify(p).replace(/"/g, '&quot;')})" class="w-full mt-8 bg-slate-900 hover:bg-slate-800 text-white font-black py-3.5 rounded-xl transition shadow-xl hover:-translate-y-0.5">Buy Now</button>
                </div>
            `;
        });
        
        grid.innerHTML = html;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCompareModal() {
        let modal = document.getElementById('compare-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Wishlist Logic
    function renderWishlistCount() {
        const countEl = document.getElementById('wishlist-count');
        if (countEl) {
            if (wishlist.length > 0) {
                countEl.innerHTML = wishlist.length;
                countEl.classList.remove('hidden');
            } else {
                countEl.classList.add('hidden');
            }
        }
    }

    function toggleWishlist(product) {
        const index = wishlist.findIndex(item => item.id == product.id);
        let action = 'added';
        if (index > -1) {
            wishlist.splice(index, 1);
            action = 'removed';
        } else {
            wishlist.push(product);
        }
        localStorage.setItem('zk_wishlist', JSON.stringify(wishlist));
        renderWishlistCount();

        // Animate Button if exists
        if (typeof window.event !== 'undefined' && window.event) {
            let btn = window.event.target;
            if (btn && btn.closest) btn = btn.closest(`.wishlist-btn-${product.id}`) || btn.closest('button');
            if (btn && btn.classList.contains(`wishlist-btn-${product.id}`)) {
                if (action === 'added') {
                    btn.classList.add('bg-rose-50', 'text-rose-500', 'border-rose-100');
                    btn.classList.remove('bg-white/80', 'text-slate-400', 'border-transparent');
                } else {
                    btn.classList.remove('bg-rose-50', 'text-rose-500', 'border-rose-100');
                    btn.classList.add('bg-white/80', 'text-slate-400', 'border-transparent');
                }
            }
        }
        
        // If on wishlist page, re-render
        if (typeof renderWishlistPage === 'function') {
            renderWishlistPage();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderWishlistCount();
    });

    function toggleCart() {
        const sideCart = document.getElementById('side-cart');
        sideCart.classList.toggle('translate-x-full');
        renderCart();
    }

    function addToCart(product, skipAnimation = false) {
        const exists = cart.find(item => item.id == product.id);
        if (exists) {
            exists.qty++;
        } else {
            cart.push({...product, qty: 1});
        }
        localStorage.setItem('zk_cart', JSON.stringify(cart));
        
        if (!skipAnimation && typeof window.event !== 'undefined' && window.event) {
            let btn = window.event.target;
            if (btn && btn.closest) {
                btn = btn.closest('button');
            }
            if (btn) {
                const originalText = btn.innerHTML;
                btn.classList.add('bg-green-500');
                btn.innerHTML = '✓';
                setTimeout(() => {
                    btn.classList.remove('bg-green-500');
                    btn.innerHTML = originalText;
                }, 1000);
            }
        }

        renderCart();
        if (!skipAnimation && window.innerWidth > 768) {
            const sideCart = document.getElementById('side-cart');
            if (sideCart && sideCart.classList.contains('translate-x-full')) {
                toggleCart();
            }
        }
    }

    function buyNow(product) {
        // Add item to cart SILENTLY (no animation, no sidecart)
        addToCart(product, true);
        
        // Hide side cart if open
        const sideCart = document.getElementById('side-cart');
        if (sideCart && !sideCart.classList.contains('translate-x-full')) {
            sideCart.classList.add('translate-x-full');
        }
        
        // Open the Checkout Modal Immediately
        setTimeout(() => {
            openCheckout();
        }, 50);
    }

    function renderCart() {
        const countEl = document.getElementById('cart-count');
        const container = document.getElementById('cart-items');
        const totalEl = document.getElementById('cart-total');

        if (countEl) countEl.innerHTML = cart.reduce((a, b) => a + b.qty, 0);

        if (!container || !totalEl) return;

        if (cart.length === 0) {
            container.innerHTML = `<div class="text-center py-12 text-slate-400">
                <div class="text-4xl mb-4">🛍️</div>
                <p class="font-bold">Your bag is empty</p>
                <p class="text-xs mt-2">Add some kitchen magic to get started!</p>
            </div>`;
            totalEl.innerHTML = '৳ 0';
            return;
        }

        let total = 0;
        container.innerHTML = cart.map((item, index) => {
            total += item.price * item.qty;
            return `
            <div class="flex gap-4 items-center group">
                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100">
                    <img src="${item.image}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <h4 class="font-black text-slate-900 text-sm leading-tight">${item.name}</h4>
                    <p class="text-amber-600 font-bold text-xs mt-1">৳ ${item.price.toLocaleString()}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <button onclick="updateQty(${index}, -1)" class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold hover:bg-amber-100 transition">-</button>
                        <span class="text-xs font-black">${item.qty}</span>
                        <button onclick="updateQty(${index}, 1)" class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold hover:bg-amber-100 transition">+</button>
                    </div>
                </div>
                <button onclick="removeFromCart(${index})" class="text-slate-300 hover:text-red-500 transition">✕</button>
            </div>`;
        }).join('');
        totalEl.innerHTML = '৳ ' + total.toLocaleString();
    }

    function updateQty(index, delta) {
        cart[index].qty += delta;
        if (cart[index].qty < 1) return removeFromCart(index);
        localStorage.setItem('zk_cart', JSON.stringify(cart));
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        localStorage.setItem('zk_cart', JSON.stringify(cart));
        renderCart();
    }

    // Quick View Logic
    function openQuickView(p) {
        const modal = document.getElementById('quick-view-modal');
        const content = document.getElementById('qv-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        content.innerHTML = `
            <button onclick="closeQuickView()" class="absolute top-6 right-6 z-20 w-12 h-12 rounded-full bg-white/50 backdrop-blur-md flex items-center justify-center hover:bg-white transition text-xl">✕</button>
            <div class="md:w-1/2 h-[400px] md:h-auto overflow-hidden">
                <img src="${p.image}" class="w-full h-full object-cover">
            </div>
            <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                <span class="text-amber-600 text-[10px] font-black uppercase tracking-widest mb-4">Limited Edition</span>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 mb-6 leading-tight">${p.name}</h2>
                <div class="text-4xl font-black text-amber-600 mb-8 italic">৳ ${parseInt(p.price).toLocaleString()}</div>
                <p class="text-slate-500 mb-10 text-lg leading-relaxed">${p.description}</p>
                <div class="flex gap-3">
                    <button onclick='addToCart(${JSON.stringify(p)})' class="flex-1 bg-slate-100 hover:bg-amber-100 text-slate-800 font-bold py-4 rounded-2xl transition shadow-sm flex items-center justify-center gap-2 group/btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover/btn:text-amber-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Add to Bag</span>
                    </button>
                    
                    <button onclick='buyNow(${JSON.stringify(p).replace(/"/g, '&quot;')}); closeQuickView();' class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-black uppercase tracking-widest text-sm py-4 rounded-2xl transition shadow-xl shadow-slate-200 text-center">
                        Buy Now
                    </button>
                    
                    <button onclick='toggleWishlist(${JSON.stringify(p).replace(/"/g, '&quot;')})' class="w-14 h-14 rounded-2xl border border-slate-200 bg-white flex items-center justify-center text-xl hover:bg-rose-50 hover:border-rose-100 hover:text-rose-500 transition flex-shrink-0 wishlist-btn-${p.id}">
                        ${wishlist.findIndex(item => item.id == p.id) > -1 ? '❤️' : '🤍'}
                    </button>
                </div>
            </div>
        `;

        setTimeout(() => {
            content.classList.remove('scale-90', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeQuickView() {
        const content = document.getElementById('qv-content');
        content.classList.add('scale-90', 'opacity-0');
        setTimeout(() => {
            document.getElementById('quick-view-modal').classList.add('hidden');
            document.getElementById('quick-view-modal').classList.remove('flex');
        }, 500);
    }
</script>

<!-- ===========================
     CHECKOUT MODAL (Zoom-in Animation)
=========================== -->
<div id="checkout-modal" class="fixed inset-0 z-[120] hidden items-center justify-center p-3">
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" onclick="closeCheckout()"></div>
    <div id="checkout-content" class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden transition-all duration-500 transform scale-75 opacity-0 border border-slate-100">
        
        <!-- Top Right Close Button -->
        <button onclick="closeCheckout()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/10 flex items-center justify-center hover:bg-black/20 transition z-10 text-slate-800 font-bold">✕</button>

        <div class="bg-red-600 p-6 text-white text-center relative border-b border-red-700/20">
            <h2 class="text-2xl font-black italic tracking-tight">Place Order</h2>
            <p class="text-red-100/80 text-[10px] font-bold uppercase tracking-widest mt-1">Cash on Delivery</p>
        </div>
        
        <form id="order-form" onsubmit="submitOrder(event)" class="p-6 space-y-4">
            <div id="order-summary-mini" class="bg-slate-50 p-3 rounded-xl border border-slate-100 mb-2 max-h-32 overflow-y-auto hidden">
                <!-- Summary injected here -->
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 opacity-30 group-focus-within:opacity-100 transition text-sm">👤</span>
                    <input type="text" name="name" required placeholder="Full Name" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-red-500 outline-none transition text-sm font-medium text-slate-800">
                </div>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 opacity-30 group-focus-within:opacity-100 transition text-sm">📞</span>
                    <input type="tel" name="phone" required placeholder="Mobile Number" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-red-500 outline-none transition text-sm font-medium text-slate-800">
                </div>
            </div>
            
            <div class="relative group">
                <span class="absolute left-4 top-3.5 opacity-30 group-focus-within:opacity-100 transition text-sm">📍</span>
                <textarea name="address" required rows="2" placeholder="Full Delivery Address" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-red-500 outline-none transition text-sm font-medium text-slate-800 resize-none"></textarea>
            </div>

            <button type="submit" id="order-btn" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-xl transition shadow-xl shadow-slate-200 mt-2 flex justify-center items-center gap-2">
                Confirm Order <span class="text-red-400">৳ <span id="checkout-total">0</span></span>
            </button>
            <p class="text-[10px] text-slate-400 text-center font-bold uppercase tracking-widest mt-2">Pay cash after receiving your items</p>
        </form>

        <!-- Success Message (Hidden) -->
        <div id="success-screen" class="hidden absolute inset-0 bg-white flex flex-col items-center justify-center text-center p-12">
            <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-5xl mb-8 animate-bounce">✓</div>
            <h2 class="text-3xl font-black text-slate-900 mb-4">ORDER PLACED!</h2>
            <p class="text-slate-500 mb-8 leading-relaxed">Thank you for shopping with Zaman Kitchens. Our team will call you shortly for confirmation.</p>
            <button onclick="window.location.reload()" class="bg-slate-900 text-white font-black px-10 py-4 rounded-2xl hover:bg-slate-800 transition">Continue Shopping</button>
        </div>
    </div>
</div>

<script>
    function openCheckout() {
        if (cart.length === 0) return alert('Your bag is empty!');
        
        const modal = document.getElementById('checkout-modal');
        const content = document.getElementById('checkout-content');
        const summary = document.getElementById('order-summary-mini');
        const totalDisplay = document.getElementById('checkout-total');
        
        let total = cart.reduce((a, b) => a + (b.price * b.qty), 0);
        totalDisplay.innerText = total.toLocaleString();
        
        summary.innerHTML = cart.map(item => `
            <div class="flex justify-between items-center text-xs mb-1 last:mb-0">
                <span class="font-bold text-slate-600">${item.qty}x ${item.name}</span>
                <span class="text-slate-400">৳ ${ (item.price * item.qty).toLocaleString() }</span>
            </div>
        `).join('');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-75', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeCheckout() {
        const content = document.getElementById('checkout-content');
        content.classList.add('scale-75', 'opacity-0');
        setTimeout(() => {
            document.getElementById('checkout-modal').classList.add('hidden');
        }, 500);
    }

    async function submitOrder(e) {
        e.preventDefault();
        const btn = document.getElementById('order-btn');
        const form = document.getElementById('order-form');
        const formData = new FormData(form);
        formData.append('items', JSON.stringify(cart));

        btn.disabled = true;
        btn.innerHTML = '<span class="animate-pulse">Processing...</span>';

        try {
            const response = await fetch('api/place_order.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                localStorage.removeItem('zk_cart');
                document.getElementById('success-screen').classList.remove('hidden');
                document.getElementById('success-screen').classList.add('flex');
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = 'Confirm Order';
            }
        } catch (error) {
            alert('Something went wrong. Please try again or call us.');
            btn.disabled = false;
        }
    }
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
