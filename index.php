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
     UNIQUE CATEGORY NAVIGATION: CIRCULAR GLASSMORPHISM
=========================== -->
<section id="products" class="py-20 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="inline-block bg-white/80 backdrop-blur-md border border-amber-200 text-amber-600 text-[10px] font-black uppercase tracking-[0.3em] px-6 py-2 rounded-full mb-6 shadow-sm">Premium Selections</span>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight">World-Class <span class="text-amber-500">Kitchens</span></h2>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg leading-relaxed">Select a collection to filter your dream kitchen components.</p>
        </div>

        <!-- CIRCULAR CATEGORY NAV -->
        <div class="flex flex-wrap items-center justify-center gap-6 md:gap-12 mb-16">
            <!-- Everything / All -->
            <div onclick="filterCategory('all', this)" class="cat-circle-item active group cursor-pointer flex flex-col items-center">
                <div class="w-20 h-20 md:w-32 md:h-32 rounded-full p-1 bg-gradient-to-tr from-amber-500 to-orange-400 group-hover:scale-110 transition-all duration-500 shadow-xl shadow-amber-200/50">
                    <div class="w-full h-full rounded-full bg-slate-900 flex items-center justify-center text-white text-2xl font-black italic">ALL</div>
                </div>
                <span class="mt-4 font-black uppercase text-[11px] tracking-widest text-slate-900 group-hover:text-amber-600 transition">Everything</span>
            </div>

            <?php foreach($gridCats as $cat): ?>
            <div onclick="filterCategory('<?php echo $cat['slug']; ?>', this)" class="cat-circle-item group cursor-pointer flex flex-col items-center">
                <div class="w-20 h-20 md:w-32 md:h-32 rounded-full p-1 bg-white border-2 border-slate-100 group-hover:border-amber-500 group-hover:scale-110 transition-all duration-500 shadow-lg group-hover:shadow-amber-100/50 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($cat['image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                         class="w-full h-full rounded-full object-cover grayscale-[40%] group-hover:grayscale-0 transition duration-500">
                </div>
                <span class="mt-4 font-bold text-[11px] uppercase tracking-wider text-slate-500 group-hover:text-amber-600 transition">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- PRODUCT GRID -->
        <?php
        $allProducts = $pdo->query("SELECT p.*, c.slug AS cat_slug FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
        ?>
        <div id="product-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-10">
            <?php foreach($allProducts as $p): ?>
            <div class="product-item transition-all duration-700 transform opacity-100 scale-100" data-category="<?php echo $p['cat_slug']; ?>">
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .cat-circle-item.active .rounded-full {
        border-color: #f59e0b !important;
        box-shadow: 0 15px 30px -5px rgba(245, 158, 11, 0.4) !important;
        transform: translateY(-5px) scale(1.1);
    }
    .cat-circle-item.active span {
        color: #f59e0b !important;
        font-weight: 900;
    }
    .cat-circle-item:hover .rounded-full {
        transform: translateY(-5px);
    }
</style>

<script>
    function filterCategory(slug, el) {
        document.querySelectorAll('.cat-circle-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');

        const items = document.querySelectorAll('.product-item');
        items.forEach(item => {
            if (slug === 'all' || item.getAttribute('data-category') === slug) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0) scale(1)';
                }, 50);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px) scale(0.95)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 500);
            }
        });
    }
</script>

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

<!-- Quick Inquiry Form: COMPACT & SLIM -->
<section class="py-12 bg-white" id="inquiry">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-slate-900 rounded-[2rem] overflow-hidden flex flex-col md:flex-row shadow-2xl">
            <div class="md:w-2/5 p-8 bg-gradient-to-br from-slate-900 to-slate-800 text-white border-r border-white/5">
                <h2 class="text-2xl font-black mb-4"><span class="text-amber-400">Custom</span> Design?</h2>
                <p class="text-slate-400 text-xs mb-6 leading-relaxed">Our experts will find the perfect fit for your kitchen.</p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm font-bold">
                        <span class="text-amber-500">📞</span> +880 1700-000000
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold">
                        <span class="text-amber-500">📍</span> Uttara, Dhaka
                    </div>
                </div>
            </div>
            <div class="md:w-3/5 p-8">
                <form action="api/submit_lead.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="name" required placeholder="Name" class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:border-amber-500 transition text-sm">
                    <input type="text" name="phone" required placeholder="Phone" class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:border-amber-500 transition text-sm">
                    <input type="text" name="message" placeholder="Requirements" class="md:col-span-2 w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:border-amber-500 transition text-sm">
                    <button type="submit" class="md:col-span-2 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black py-3 rounded-xl transition shadow-lg shadow-amber-500/20 text-sm">
                        Send Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

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
        <button onclick="toggleCart(); openCheckout();" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-900 text-center font-black py-4 rounded-2xl transition shadow-xl shadow-amber-500/20 block">
            Checkout Now
        </button>
    </div>
</div>

<script>
    // Global State
    let cart = JSON.parse(localStorage.getItem('zk_cart')) || [];

    function toggleCart() {
        const sideCart = document.getElementById('side-cart');
        sideCart.classList.toggle('translate-x-full');
        renderCart();
    }

    function addToCart(product) {
        const exists = cart.find(item => item.id == product.id);
        if (exists) {
            exists.qty++;
        } else {
            cart.push({...product, qty: 1});
        }
        localStorage.setItem('zk_cart', JSON.stringify(cart));
        
        // Fly-to-cart effect simulation
        const btn = event.target;
        btn.classList.add('bg-green-500');
        btn.innerHTML = 'Added! ✓';
        setTimeout(() => {
            btn.classList.remove('bg-green-500');
            btn.innerHTML = 'Add to Bag';
        }, 1000);

        renderCart();
        if (window.innerWidth > 768) toggleCart();
    }

    function renderCart() {
        const countEl = document.getElementById('cart-count');
        if (countEl) countEl.innerHTML = cart.reduce((a, b) => a + b.qty, 0);

        if (cart.length === 0) {
            container.innerHTML = `<div class="text-center py-12 text-slate-400">
                <div class="text-4xl mb-4">🛍️</div>
                <p class="font-bold">Your bag is empty</p>
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
                <div class="flex gap-4">
                    <button onclick='addToCart(${JSON.stringify(p)})' class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-black py-5 rounded-2xl transition shadow-2xl flex items-center justify-center gap-3">
                        <span>Add to Bag</span>
                    </button>
                    <button class="w-16 h-16 rounded-2xl border-2 border-slate-100 flex items-center justify-center text-2xl hover:bg-red-50 hover:border-red-100 transition">❤️</button>
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
<div id="checkout-modal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" onclick="closeCheckout()"></div>
    <div id="checkout-content" class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden transition-all duration-500 transform scale-75 opacity-0 border border-slate-100">
        <div class="bg-amber-500 p-8 text-white flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black italic">Place Order</h2>
                <p class="text-amber-100 text-xs font-bold uppercase tracking-widest mt-1">Cash on Delivery</p>
            </div>
            <button onclick="closeCheckout()" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/40 transition">✕</button>
        </div>
        
        <form id="order-form" onsubmit="submitOrder(event)" class="p-8 space-y-5">
            <div id="order-summary-mini" class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-4">
                <!-- Summary injected here -->
            </div>
            
            <div class="space-y-4">
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 opacity-20 group-focus-within:opacity-100 transition">👤</span>
                    <input type="text" name="name" required placeholder="Your Full Name" class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-transparent rounded-2xl focus:bg-white focus:border-amber-500 outline-none transition text-sm">
                </div>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 opacity-20 group-focus-within:opacity-100 transition">📞</span>
                    <input type="tel" name="phone" required placeholder="Mobile Number" class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-transparent rounded-2xl focus:bg-white focus:border-amber-500 outline-none transition text-sm">
                </div>
                <div class="relative group">
                    <span class="absolute left-4 top-4 opacity-20 group-focus-within:opacity-100 transition">📍</span>
                    <textarea name="address" required rows="3" placeholder="Full Delivery Address" class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-transparent rounded-2xl focus:bg-white focus:border-amber-500 outline-none transition text-sm resize-none"></textarea>
                </div>
            </div>

            <button type="submit" id="order-btn" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-2xl transition shadow-xl shadow-slate-200">
                Confirm Order ৳ <span id="checkout-total">0</span>
            </button>
            <p class="text-[10px] text-gray-400 text-center font-bold uppercase tracking-widest">Pay cash after receiving your items</p>
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
