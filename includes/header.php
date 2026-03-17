<?php
/**
 * Zaman Kitchens - Header Component
 * Features: Sticky header, Category Dropdown, Search Bar
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// Fetch categories for mega dropdown
$categories = [];
try {
    $categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC")->fetchAll();
} catch(Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' - Premium Kitchen Accessories & Sinks'; ?></title>
    <meta name="description" content="<?php echo isset($pageDesc) ? $pageDesc : "Bangladesh's best kitchen accessories, sinks, hoods, and cabinets. Shop online with fast delivery."; ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .group:hover .group-hover\:scale-105 { transform: scale(1.05); }
        /* WhatsApp button pulse */
        @keyframes pulse-ring { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(1.3); opacity: 0; } }
        .wa-pulse::before { content: ''; position: absolute; inset: -4px; border-radius: 50%; background: #25d366; animation: pulse-ring 1.5s infinite; z-index: -1; }
        
        /* Top bar scroll for mobile */
        @keyframes scroll-text { 0% { transform: translateX(10%); } 100% { transform: translateX(-100%); } }
        @media (max-width: 640px) {
            .animate-scroll {
                display: inline-flex;
                animation: scroll-text 15s linear infinite;
                padding-left: 100%;
            }
        }
    </style>

    <?php echo $extraHead ?? ''; ?>
</head>
<body class="bg-white text-gray-900 antialiased">

<!-- ===== TOP BAR (Gazi Style) ===== -->
<div class="bg-slate-900 text-white text-[10px] md:text-xs py-2 border-b border-white/5">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-2">
        <!-- Contact Info -->
        <div class="flex items-center gap-4 opacity-80 uppercase tracking-widest font-bold">
            <a href="tel:<?php echo SITE_PHONE_RAW; ?>" class="hover:text-red-500 transition flex items-center gap-1.5">
                <i class="ph-bold ph-phone"></i> <?php echo SITE_PHONE; ?>
            </a>
            <span class="hidden md:block opacity-20">|</span>
            <div class="hidden md:flex items-center gap-1.5">
                <i class="ph-bold ph-clock"></i> Sat-Thurs | 10am-6pm
            </div>
        </div>

        <!-- Utility Links -->
        <div class="flex items-center gap-4 uppercase tracking-widest font-bold">
            <div class="flex items-center gap-3">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo SITE_URL; ?>/profile.php" class="hover:text-red-500 transition">My Account</a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="hover:text-red-500 transition">Login</a>
                    <span class="opacity-20">|</span>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="hover:text-red-500 transition">Registration</a>
                <?php endif; ?>
            </div>
            <span class="opacity-20">|</span>
            <a href="<?php echo SITE_URL; ?>/wishlist.php" class="hover:text-red-500 transition flex items-center gap-1.5">
                WISHLIST (<span id="wishlist-count-top">0</span>)
            </a>
            <span class="opacity-20 hidden md:block">|</span>
            <button onclick="toggleCart()" class="hover:text-red-500 transition flex items-center gap-1.5 uppercase">
                My Cart (<span id="cart-count-top">0</span>)
            </button>
        </div>
    </div>
</div>


<!-- ===== MAIN HEADER ===== -->
<header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex items-center h-20 gap-4">

            <!-- Mobile Menu Toggle -->
            <button onclick="document.getElementById('mobileMenu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg hover:bg-gray-50 border border-gray-100">
                <i class="ph-bold ph-list text-xl"></i>
            </button>

            <!-- Logo -->
            <a href="<?php echo SITE_URL; ?>" class="flex-shrink-0 flex items-center">
                <img src="<?php echo SITE_URL; ?>/assets/logo.png" alt="Zaman Kitchens" class="h-12 md:h-16 w-auto object-contain" onerror="this.onerror=null; this.src='https://placehold.co/200x80/d80032/ffffff?text=Zaman+Kitchens';">
            </a>

            <!-- Search Bar (Gazi Style - More prominent) -->
            <form action="<?php echo SITE_URL; ?>/search" method="GET" class="flex-1 max-w-2xl px-4 hidden md:block">
                <div class="relative group">
                    <input type="text" name="q" placeholder="Search for products (Gas Stove, Kitchen Hood...)"
                        class="w-full pl-6 pr-14 py-3 text-sm bg-gray-50 border-2 border-slate-100 rounded-full focus:outline-none focus:border-red-600 focus:bg-white transition-all shadow-sm group-hover:shadow-md">
                    <button type="submit" class="absolute right-1 top-1 bottom-1 px-5 h-auto bg-red-600 hover:bg-red-700 text-white rounded-full transition-all flex items-center justify-center">
                        <i class="ph-bold ph-magnifying-glass text-lg"></i>
                    </button>
                </div>
            </form>

            <!-- Quick Actions (Gazi Style Icons + Labels) -->
            <div class="flex items-center gap-5 ml-auto">
                <!-- Compare -->
                <a href="<?php echo SITE_URL; ?>/compare.php" class="flex flex-col items-center group relative">
                    <div class="relative mb-1">
                        <img src="image/compare_icon.png" alt="Compare" class="h-6 w-auto group-hover:scale-110 transition-transform">
                        <span id="compare-count" class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white">0</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter group-hover:text-red-600 transition-colors">Compare</span>
                </a>

                <!-- Wishlist -->
                <a href="<?php echo SITE_URL; ?>/wishlist.php" class="flex flex-col items-center group relative border-l border-slate-100 pl-4">
                    <div class="relative mb-1">
                        <img src="image/wish_list_icon.png" alt="Wishlist" class="h-6 w-auto group-hover:scale-110 transition-transform">
                        <span id="wishlist-count-header" class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white">0</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter group-hover:text-red-600 transition-colors">Wishlist</span>
                </a>

                <!-- My Cart -->
                <a href="<?php echo SITE_URL; ?>/cart.php" class="flex flex-col items-center group relative border-l border-slate-100 pl-4">
                    <div class="relative mb-1">
                        <img src="image/cart_icon.png" alt="Cart" class="h-6 w-auto group-hover:scale-110 transition-transform">
                        <span id="cart-count-header" class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white">0</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter group-hover:text-red-600 transition-colors">My Cart</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Menu (Gazi Style Secondary Header) -->
    <div class="bg-gray-50 border-t border-gray-100 hidden md:block">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-center gap-8 py-3 text-xs font-black uppercase tracking-[0.15em] text-slate-700">
                <a href="<?php echo SITE_URL; ?>" class="hover:text-red-600 transition">Home</a>
                <?php foreach(array_slice($categories, 0, 7) as $cat): ?>
                <a href="<?php echo SITE_URL; ?>/category/<?php echo $cat['slug']; ?>" class="hover:text-red-600 transition"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endforeach; ?>
                <a href="<?php echo SITE_URL; ?>/video" class="hover:text-red-600 transition">Videos</a>
                <a href="<?php echo SITE_URL; ?>/blog" class="hover:text-red-600 transition">Blogs</a>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <div id="mobileMenu" class="hidden md:hidden fixed inset-0 z-[60] bg-white">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <img src="<?php echo SITE_URL; ?>/assets/logo.png" alt="Zaman Kitchens" class="h-10 w-auto object-contain">
            <button onclick="document.getElementById('mobileMenu').classList.add('hidden')" class="p-2">
                <i class="ph-bold ph-x text-2xl"></i>
            </button>
        </div>
        <div class="p-6 flex flex-col gap-6 font-bold uppercase tracking-widest text-sm text-slate-700">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <?php foreach($categories as $cat): ?>
            <a href="<?php echo SITE_URL; ?>/category/<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
            <?php endforeach; ?>
            <hr class="border-gray-100">
            <a href="<?php echo SITE_URL; ?>/login.php">Login / Register</a>
        </div>
    </div>
</header>

<script>
    // Keep badge counts updated
    function updateHeaderCounts() {
        const cart = JSON.parse(localStorage.getItem('zk_cart')) || [];
        const wishlist = JSON.parse(localStorage.getItem('zk_wishlist')) || [];
        const cartCount = cart.reduce((a, b) => a + b.qty, 0);
        
        document.getElementById('cart-count-top').innerText = cartCount;
        document.getElementById('cart-count-badge').innerText = cartCount;
        document.getElementById('wishlist-count-top').innerText = wishlist.length;
    }
    document.addEventListener('DOMContentLoaded', updateHeaderCounts);
    window.addEventListener('storage', updateHeaderCounts);
    // Custom event for internal updates
    window.addEventListener('cartUpdated', updateHeaderCounts);
</script>
