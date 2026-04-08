<?php
/**
 * Zaman Kitchens - Header Component
 * Features: Sticky header, Category Dropdown, Search Bar
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// Fetch categories and organize hierarchically
$all_categories = [];
$main_categories = [];
try {
    $all_categories = $pdo->query("SELECT id, name, slug, parent_id, hero_image FROM categories ORDER BY name ASC")->fetchAll();
    
    // Group into Parent -> Children
    foreach ($all_categories as $cat) {
        if (empty($cat['parent_id'])) {
            $main_categories[$cat['id']] = $cat;
            $main_categories[$cat['id']]['children'] = [];
        }
    }
    foreach ($all_categories as $cat) {
        if (!empty($cat['parent_id']) && isset($main_categories[$cat['parent_id']])) {
            $main_categories[$cat['parent_id']]['children'][] = $cat;
        }
    }
} catch(Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo SITE_URL; ?>/">
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        h1, h2, h3, h4, .font-black { letter-spacing: -0.02em; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .group:hover .group-hover\:scale-105 { transform: scale(1.05); }

        /* Dropdown Menu Styles */
        .nav-item { position: relative; }
        .dropdown-menu { 
            position: absolute; top: 100%; left: 0; min-width: 220px; 
            background: white; border: 1px solid #f1f5f9; border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); opacity: 0; 
            visibility: hidden; transform: translateY(10px); transition: all 0.3s ease; 
            z-index: 100; padding: 0.5rem 0;
        }
        .nav-item:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-link { 
            display: block; padding: 0.75rem 1.25rem; font-size: 11px; 
            font-weight: 800; color: #475569; text-transform: uppercase; 
            letter-spacing: 0.05em; transition: all 0.2s;
        }
        .dropdown-link:hover { background: #f8fafc; color: #ef233c; padding-left: 1.5rem; }

        /* Mega Menu Styles */
        .mega-menu {
            position: absolute; top: 100%; left: 50%; width: 900px; max-width: 90vw;
            background: white; border: 1px solid #f1f5f9; border-radius: 1.5rem; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); opacity: 0; 
            visibility: hidden; transform: translate(-50%, 10px); transition: all 0.3s ease; 
            z-index: 100; padding: 2.5rem; cursor: auto;
        }
        .nav-item:hover .mega-menu { opacity: 1; visibility: visible; transform: translate(-50%, 0); }

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
            <button onclick="document.getElementById('mobileMenu').classList.remove('hidden')" class="md:hidden p-2 rounded-lg hover:bg-gray-50 border border-gray-100">
                <i class="ph-bold ph-list text-xl"></i>
            </button>

            <!-- Logo -->
            <a href="<?php echo SITE_URL; ?>" class="flex-shrink-0 flex items-center gap-2 group whitespace-nowrap">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-red-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <span class="text-white font-extrabold text-[10px] md:text-sm">ZK</span>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="font-extrabold text-slate-900 text-xs md:text-lg uppercase tracking-tighter">Zaman</span>
                    <span class="font-bold text-red-600 text-[8px] md:text-[10px] uppercase tracking-[0.2em] -mt-0.5">Kitchens</span>
                </div>
            </a>

            <!-- Mobile Search Toggle -->
            <button onclick="toggleMobileSearch()" class="md:hidden p-2 rounded-lg hover:bg-gray-50 border border-gray-100 ml-auto">
                <i class="ph-bold ph-magnifying-glass text-xl"></i>
            </button>

            <!-- Search Bar (Gazi Style - Slimmer & Modern) -->
            <form action="<?php echo SITE_URL; ?>/search" method="GET" class="flex-1 max-w-xl px-4 hidden md:block">
                <div class="relative group">
                    <input type="text" name="q" placeholder="Search for products (Gas Stove, Sinks...)"
                        class="w-full pl-5 pr-12 py-2.5 text-sm bg-gray-50 border border-slate-200 rounded-xl focus:outline-none focus:border-red-600 focus:bg-white transition-all shadow-sm group-hover:shadow-md">
                    <button type="submit" class="absolute right-1 top-1 bottom-1 w-10 h-auto bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all flex items-center justify-center">
                        <i class="ph-bold ph-magnifying-glass text-lg"></i>
                    </button>
                </div>
            </form>

            <!-- Quick Actions (Gazi Style Icons + Labels) -->
            <div class="flex items-center gap-4 md:gap-5 <?php echo isset($hideQuickActionsOnMobile) && $hideQuickActionsOnMobile ? 'hidden md:flex' : 'flex'; ?>">
                <!-- Compare -->
                <button onclick="openCompareModal()" class="flex flex-col items-center group relative hidden sm:flex">
                    <div class="relative mb-1">
                        <img src="<?php echo SITE_URL; ?>/image/compare_icon.png" alt="Compare" class="h-5 md:h-6 w-auto group-hover:scale-110 transition-transform">
                        <span id="compare-count" class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white">0</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter group-hover:text-red-600 transition-colors">Compare</span>
                </button>

                <!-- Wishlist -->
                <a href="<?php echo SITE_URL; ?>/wishlist.php" class="flex flex-col items-center group relative border-l border-slate-100 pl-4">
                    <div class="relative mb-1">
                        <img src="<?php echo SITE_URL; ?>/image/wish_list_icon.png" alt="Wishlist" class="h-5 md:h-6 w-auto group-hover:scale-110 transition-transform">
                        <span id="wishlist-count-header" class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white">0</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter group-hover:text-red-600 transition-colors">Wishlist</span>
                </a>

                <!-- My Cart -->
                <a href="<?php echo SITE_URL; ?>/cart.php" class="flex flex-col items-center group relative border-l border-slate-100 pl-4">
                    <div class="relative mb-1">
                        <img src="<?php echo SITE_URL; ?>/image/cart_icon.png" alt="Cart" class="h-5 md:h-6 w-auto group-hover:scale-110 transition-transform">
                        <span id="cart-count-header" class="absolute -top-2 -right-2 bg-red-600 text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-white">0</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter group-hover:text-red-600 transition-colors">My Cart</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Collapsible Mobile Search Bar -->
    <div id="mobileSearch" class="hidden md:hidden bg-white border-t border-gray-100 p-4 sticky top-20 z-40 shadow-lg">
        <form action="<?php echo SITE_URL; ?>/search" method="GET" class="relative">
            <input type="text" id="mob-search-input" name="q" placeholder="Search for products..."
                class="w-full pl-5 pr-12 py-3 text-sm bg-gray-50 border border-slate-200 rounded-xl focus:outline-none focus:border-red-600 focus:bg-white transition-all shadow-inner">
            <button type="submit" class="absolute right-1 top-1 bottom-1 w-10 h-auto bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all flex items-center justify-center">
                <i class="ph-bold ph-magnifying-glass text-lg"></i>
            </button>
        </form>
    </div>

    <script>
        function toggleMobileSearch() {
            const searchBar = document.getElementById('mobileSearch');
            const input = document.getElementById('mob-search-input');
            const isHidden = searchBar.classList.contains('hidden');
            
            searchBar.classList.toggle('hidden');
            if (isHidden) {
                setTimeout(() => input.focus(), 100);
            }
        }
    </script>

    <div class="bg-gray-50 border-t border-gray-100 hidden md:block">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-center gap-6 py-3 text-[10px] font-black uppercase tracking-[0.1em] text-slate-700">
                <a href="<?php echo SITE_URL; ?>" class="hover:text-red-600 transition">Home</a>
                <a href="<?php echo SITE_URL; ?>/shop" class="hover:text-red-600 transition">Shop</a>
                
                <!-- Mega Menu (Categories) -->
                <div class="nav-item group">
                    <button class="flex items-center gap-1 hover:text-red-600 transition py-1">
                        Categories
                        <i class="ph ph-caret-down text-[10px]"></i>
                    </button>
                    <!-- Mega Menu Dropdown -->
                    <div class="mega-menu text-left">
                        <div class="columns-2 md:columns-3 lg:columns-4 gap-8">
                        <?php foreach($main_categories as $cat):
                            $catThumb = '';
                            if (!empty($cat['hero_image'])) {
                                $catThumb = (strpos($cat['hero_image'], 'http') === 0) ? $cat['hero_image'] : SITE_URL . '/' . ltrim($cat['hero_image'], '/');
                            }
                        ?>
                            <div class="category-block flex flex-col break-inside-avoid mb-8">
                                <a href="<?php echo SITE_URL; ?>/category/<?php echo $cat['slug']; ?>" class="flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest text-xs mb-4 hover:text-red-600 transition border-b border-slate-100 pb-2 group/cat">
                                    <?php if ($catThumb): ?>
                                    <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-100 shadow-sm flex-shrink-0 bg-slate-50">
                                        <img src="<?php echo htmlspecialchars($catThumb); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="w-full h-full object-cover group-hover/cat:scale-110 transition-transform duration-300">
                                    </div>
                                    <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-red-50 border border-red-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-red-600 text-[10px] font-black"><?php echo strtoupper(substr($cat['name'], 0, 1)); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                </a>
                                <?php if(!empty($cat['children'])): ?>
                                <ul class="space-y-3">
                                    <?php foreach($cat['children'] as $child): ?>
                                    <li>
                                        <a href="<?php echo SITE_URL; ?>/category/<?php echo $child['slug']; ?>" class="text-[11px] font-bold text-slate-500 hover:text-red-600 transition flex items-center gap-2 group/link">
                                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-red-600 transition-colors"></span> 
                                            <span class="group-hover/link:translate-x-1 transition-transform"><?php echo htmlspecialchars($child['name']); ?></span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <a href="<?php echo SITE_URL; ?>/video" class="hover:text-red-600 transition">Videos</a>
                <a href="<?php echo SITE_URL; ?>/blog" class="hover:text-red-600 transition">Blogs</a>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <div id="mobileMenu" class="hidden md:hidden fixed inset-0 z-[60] bg-white overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center shadow-lg">
                    <span class="text-white font-extrabold text-[10px]">ZK</span>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="font-extrabold text-slate-900 text-xs uppercase tracking-tighter">Zaman</span>
                    <span class="font-bold text-red-600 text-[8px] uppercase tracking-[0.2em] -mt-0.5">Kitchens</span>
                </div>
            </div>
            <button onclick="document.getElementById('mobileMenu').classList.add('hidden')" class="p-2">
                <i class="ph-bold ph-x text-2xl"></i>
            </button>
        </div>
        <div class="p-6 flex flex-col gap-5 font-bold uppercase tracking-widest text-xs text-slate-700">
            <a href="<?php echo SITE_URL; ?>" class="text-red-600">Home</a>
            
            <?php foreach($main_categories as $cat): ?>
            <div>
                <div class="flex items-center justify-between py-1 <?php echo empty($cat['children']) ? '' : 'cursor-pointer'; ?>" 
                     <?php if(!empty($cat['children'])): ?> onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.caret-icon').classList.toggle('rotate-180');" <?php endif; ?>>
                    <a href="<?php echo SITE_URL; ?>/category/<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
                    <?php if(!empty($cat['children'])): ?>
                        <i class="ph ph-caret-down text-slate-400 caret-icon transition-transform"></i>
                    <?php endif; ?>
                </div>
                <?php if(!empty($cat['children'])): ?>
                <div class="hidden flex flex-col gap-3 pl-4 mt-3 pb-2 border-l-2 border-slate-100">
                    <?php foreach($cat['children'] as $child): ?>
                    <a href="<?php echo SITE_URL; ?>/category/<?php echo $child['slug']; ?>" class="text-[11px] text-slate-500 hover:text-red-600 transition">
                        <?php echo htmlspecialchars($child['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            
            <hr class="border-gray-100 my-2">
            <a href="<?php echo SITE_URL; ?>/login.php" class="text-slate-400">Login / Register</a>
        </div>
    </div>
</header>

<script>
    // Keep badge counts updated
    function updateHeaderCounts() {
        const cart = JSON.parse(localStorage.getItem('zk_cart')) || [];
        const wishlist = JSON.parse(localStorage.getItem('zk_wishlist')) || [];
        const cartCount = cart.reduce((a, b) => a + b.qty, 0);
        const wishCount = wishlist.length;

        // Update all cart count elements
        ['cart-count-top', 'cart-count-header'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = cartCount;
        });
        // Update all wishlist count elements
        ['wishlist-count-top', 'wishlist-count-header'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = wishCount;
        });
        // Legacy IDs
        const legacyCart = document.getElementById('cart-count');
        if (legacyCart) legacyCart.innerText = cartCount;
        const legacyWish = document.getElementById('wishlist-count');
        if (legacyWish) legacyWish.innerText = wishCount;
    }
    document.addEventListener('DOMContentLoaded', updateHeaderCounts);
    window.addEventListener('storage', updateHeaderCounts);
    window.addEventListener('cartUpdated', updateHeaderCounts);
</script>
