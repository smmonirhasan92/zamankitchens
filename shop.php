<?php
/**
 * Zaman Kitchens - All Products (Shop)
 * Features: Category Sidebar, Product Grid, Filtering
 */
require_once __DIR__ . '/includes/db.php';

$selectedCat = $_GET['cat'] ?? null;
$searchTerm = $_GET['q'] ?? null;

$products = [];
$categoryName = "All Products";

try {
    if ($selectedCat) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$selectedCat]);
        $catData = $stmt->fetch();
        
        if ($catData) {
            $categoryName = $catData['name'];
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
            $stmt->execute([$catData['id']]);
            $subIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $allIds = array_merge([$catData['id']], $subIds);
            $placeholders = implode(',', array_fill(0, count($allIds), '?'));
            
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id IN ($placeholders) ORDER BY created_at DESC");
            $stmt->execute($allIds);
            $products = $stmt->fetchAll();
        }
    } elseif ($searchTerm) {
        $categoryName = "Search Results: " . htmlspecialchars($searchTerm);
        $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY created_at DESC");
        $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
        $products = $stmt->fetchAll();
    } else {
        $products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
    }
} catch(Exception $e) {}

include_once __DIR__ . '/includes/header.php';
?>

<!-- Shop Header -->
<div class="bg-slate-900 py-12 border-b border-white/5 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-red-600/10 to-transparent"></div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-white italic uppercase tracking-tighter mb-2">
            <?php echo htmlspecialchars($categoryName); ?>
        </h1>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
            Showing <?php echo count($products); ?> quality items
        </p>
    </div>
</div>

<section class="py-12 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Category Sidebar -->
            <aside class="lg:w-1/4">
                <div class="sticky top-24 space-y-8">
                    
                    <!-- Search Widget -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 px-1">Quick Find</h3>
                        <form action="shop.php" method="GET" class="relative">
                            <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>" placeholder="What are you looking for?" 
                                   class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-slate-100 rounded-xl focus:outline-none focus:border-red-600 transition-all text-sm font-bold">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-600">
                                <i class="ph-bold ph-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Category List Widget -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 px-1">Filter by Category</h3>
                        <nav class="flex flex-col gap-1">
                            <a href="shop.php" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all font-bold text-sm <?php echo !$selectedCat ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'text-slate-600 hover:bg-gray-50 hover:pl-6'; ?>">
                                <span>All Products</span>
                                <?php if(!$selectedCat): ?> <i class="ph-bold ph-check"></i> <?php endif; ?>
                            </a>
                            
                            <?php foreach($main_categories as $cat): ?>
                            <div>
                                <a href="shop.php?cat=<?php echo $cat['slug']; ?>" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all font-bold text-sm <?php echo $selectedCat === $cat['slug'] ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'text-slate-600 hover:bg-gray-50 hover:pl-6'; ?>">
                                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                    <?php if($selectedCat === $cat['slug']): ?> <i class="ph-bold ph-check"></i> <?php endif; ?>
                                </a>
                                
                                <?php if(!empty($cat['children']) && ($selectedCat === $cat['slug'] || in_array($selectedCat, array_column($cat['children'], 'slug')))): ?>
                                <div class="ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-2">
                                    <?php foreach($cat['children'] as $child): ?>
                                    <a href="shop.php?cat=<?php echo $child['slug']; ?>" class="block px-4 py-2 rounded-lg text-xs font-black transition-all <?php echo $selectedCat === $child['slug'] ? 'text-red-600' : 'text-slate-400 hover:text-slate-900'; ?>">
                                        — <?php echo htmlspecialchars($child['name']); ?>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <!-- Featured Promo Widget -->
                    <div class="bg-gradient-to-br from-red-600 to-red-800 p-8 rounded-[2.5rem] shadow-xl text-white relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-2 italic">Special Offer!</h3>
                            <p class="text-xs text-red-100 font-bold mb-6">Upgrade your kitchen with 10% discount on first order.</p>
                            <a href="tel:<?php echo SITE_PHONE_RAW; ?>" class="inline-block bg-white text-red-600 font-black px-6 py-2.5 rounded-full text-[10px] uppercase tracking-widest hover:scale-105 transition-transform">Call Now</a>
                        </div>
                        <div class="absolute -bottom-10 -right-10 opacity-20 text-9xl group-hover:scale-110 transition-transform">🍳</div>
                    </div>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="lg:w-3/4">
                <?php if (empty($products)): ?>
                <div class="bg-white p-20 rounded-[3rem] shadow-sm border border-slate-100 text-center">
                    <div class="text-7xl mb-6 opacity-20">🛒</div>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">No matching products</h2>
                    <p class="text-slate-400 font-medium">Try selecting a different category or refining your search.</p>
                    <a href="shop.php" class="mt-8 inline-block bg-slate-900 text-white font-black px-8 py-3.5 rounded-2xl hover:bg-red-600 transition-all">Clear Filters</a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
                    <?php foreach($products as $p): ?>
                        <div class="product-item">
                            <?php include __DIR__ . '/includes/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
