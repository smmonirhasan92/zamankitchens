<?php
require_once __DIR__ . '/includes/db.php';
include_once __DIR__ . '/includes/header.php';

// Fetch all categories for the category section
try {
    $catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categoriesList = $catStmt->fetchAll();
} catch (Exception $e) {
    $categoriesList = [];
}

// Handle Category Filtering
$categorySlug = isset($_GET['category']) ? $_GET['category'] : null;
$categoryName = "Recommended for You";

try {
    if ($categorySlug) {
        // Fetch category details
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$categorySlug]);
        $currentCategory = $stmt->fetch();
        
        if ($currentCategory) {
            $categoryName = $currentCategory['name'];
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY created_at DESC");
            $stmt->execute([$currentCategory['id']]);
        } else {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
        }
    } else {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
    }
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
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl text-balance">
                        <span class="block xl:inline">Refining Your</span>
                        <span class="block text-amber-600 xl:inline">Kitchen Experience</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Discover our collection of premium kitchen sinks and high-performance accessories designed to bring elegance and efficiency to your culinary sanctuary.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="#products" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 md:py-4 md:text-lg md:px-10 transition">
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
        <h2 class="text-3xl font-bold text-center mb-12">Browse by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach($categoriesList as $cat): ?>
            <a href="category/<?php echo $cat['slug']; ?>" class="group block p-6 bg-white rounded-2xl shadow-sm border border-transparent hover:border-amber-500 hover:shadow-md transition text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-600 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800"><?php echo $cat['name']; ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section id="products" class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <h2 class="text-3xl font-bold"><?php echo $categoryName; ?></h2>
            <?php if($categorySlug): ?>
                <a href="<?php echo SITE_URL; ?>" class="text-amber-600 font-semibold hover:underline border-b-2 border-amber-600 pb-1">Show All</a>
            <?php else: ?>
                <a href="#" class="text-amber-600 font-semibold hover:underline border-b-2 border-amber-600 pb-1">See All Products</a>
            <?php endif; ?>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (empty($products)): ?>
                <!-- No Products Found Message -->
                <div class="col-span-full py-20 text-center">
                    <div class="mb-4 text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-3.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">No products found in this category</h3>
                    <p class="text-gray-500 mt-2">Check back soon for new arrivals!</p>
                </div>
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
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
