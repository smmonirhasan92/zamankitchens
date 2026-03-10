<?php
require_once __DIR__ . '/includes/db.php';
include_once __DIR__ . '/includes/header.php';

// Fetch initial products for display
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $products = [];
}
?>

<!-- Hero Section -->
<header class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Refining Your</span>
                        <span class="block text-amber-600 xl:inline">Kitchen Experience</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Discover our collection of premium kitchen sinks and high-performance accessories designed to bring elegance and efficiency to your culinary sanctuary.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 md:py-4 md:text-lg md:px-10 transition">
                                Shop Collection
                            </a>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-amber-700 bg-amber-100 hover:bg-amber-200 md:py-4 md:text-lg md:px-10 transition">
                                View Catalog
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="<?php echo ASSETS_PATH; ?>/images/hero.png" alt="Modern Kitchen">
    </div>
</header>

<!-- Categories Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Browse Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Category Card 1 -->
            <div class="group relative overflow-hidden rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                <img src="https://images.unsplash.com/photo-1584622781564-1d9876a13d00?auto=format&fit=crop&q=80&w=800" class="w-full h-64 object-cover" alt="Sinks">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                    <div>
                        <h3 class="text-white text-xl font-bold">Premium Sinks</h3>
                        <a href="#" class="text-amber-400 text-sm font-semibold hover:underline">Explore More &rarr;</a>
                    </div>
                </div>
            </div>
            <!-- Category Card 2 -->
            <div class="group relative overflow-hidden rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                <img src="https://images.unsplash.com/photo-1590333247377-50a3dec42441?auto=format&fit=crop&q=80&w=800" class="w-full h-64 object-cover" alt="Faucets">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                    <div>
                        <h3 class="text-white text-xl font-bold">Kitchen Faucets</h3>
                        <a href="#" class="text-amber-400 text-sm font-semibold hover:underline">Explore More &rarr;</a>
                    </div>
                </div>
            </div>
            <!-- Category Card 3 -->
            <div class="group relative overflow-hidden rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                <img src="<?php echo ASSETS_PATH; ?>/images/category-accessories.png" class="w-full h-64 object-cover" alt="Accessories">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                    <div>
                        <h3 class="text-white text-xl font-bold">Must-have Accessories</h3>
                        <a href="#" class="text-amber-400 text-sm font-semibold hover:underline">Explore More &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <h2 class="text-3xl font-bold">Recommended for You</h2>
            <a href="#" class="text-amber-600 font-semibold hover:underline border-b-2 border-amber-600 pb-1">See All Products</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (empty($products)): ?>
                <!-- Dummy Product if DB is empty -->
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition group">
                   <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1593539502857-4560b3780562?auto=format&fit=crop&q=80&w=600" class="w-full h-56 object-cover group-hover:scale-110 transition duration-500" alt="Product">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-gray-800">New</div>
                   </div>
                   <div class="p-4">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Kitchen Sinks</p>
                        <h3 class="font-bold text-gray-800 mb-2 truncate">Luxury Nano Diamond Sink</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-extrabold text-amber-600">৳ 12,500</span>
                            <button class="bg-amber-600 text-white p-2 rounded-lg hover:bg-amber-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                   </div>
                </div>
                <!-- Repeat Dummy 3 more times for initial look -->
                <?php for($i=0; $i<3; $i++): ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition group">
                   <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1584622781564-1d9876a13d00?auto=format&fit=crop&q=80&w=600" class="w-full h-56 object-cover group-hover:scale-110 transition duration-500" alt="Product">
                   </div>
                   <div class="p-4">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Accessories</p>
                        <h3 class="font-bold text-gray-800 mb-2 truncate">Premium Matte Black Faucet</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-extrabold text-amber-600">৳ 4,200</span>
                            <button class="bg-amber-600 text-white p-2 rounded-lg hover:bg-amber-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                   </div>
                </div>
                <?php endfor; ?>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                    <!-- Dynamic Product Card -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition group">
                        <div class="relative overflow-hidden">
                            <img src="<?php echo $product['image_url']; ?>" class="w-full h-56 object-cover group-hover:scale-110 transition duration-500" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 mb-2 truncate"><?php echo $product['name']; ?></h3>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-extrabold text-amber-600">৳ <?php echo number_format($product['price']); ?></span>
                                <button class="bg-amber-600 text-white p-2 rounded-lg hover:bg-amber-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
