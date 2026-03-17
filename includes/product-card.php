<?php
/**
 * Reusable Product Card Component \u2014 Vibrant Premium Edition
 */
$imgSrc = !empty($p['image']) ? $p['image'] : (!empty($p['main_image']) ? $p['main_image'] : 'assets/images/placeholder.jpg');
?>
<?php 
$jsData = json_encode([
    'id' => $p['id'],
    'name' => $p['name'],
    'price' => $p['price'],
    'image' => $imgSrc,
    'description' => $p['description'] ?? 'Premium quality kitchen appliance.'
], JSON_HEX_APOS);
?>
<style>
.gazi-card { transition: all 0.3s ease; }
.gazi-card:hover { border-color: #d80032; }
.gazi-card:hover .gazi-img { transform: scale(1.05); }
.gazi-img { transition: transform 0.5s ease; }
.gazi-action-btn:hover { background: #d80032; color: white; border-color: #d80032; }
</style>

<div onclick='openQuickView(<?php echo $jsData; ?>)' class="gazi-card cursor-pointer bg-white rounded-xl overflow-hidden border border-slate-100 flex flex-col h-full group relative">
    <!-- Image Section -->
    <div class="relative overflow-hidden bg-white p-4 aspect-square flex items-center justify-center">
        <?php if (!empty($imgSrc)): ?>
            <img loading="lazy" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="gazi-img w-full h-full object-contain">
        <?php else: ?>
            <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-300">
                <i class="ph-bold ph-image text-4xl"></i>
            </div>
        <?php endif; ?>
        
        <!-- Add to Wishlist Overlay -->
        <button onclick="event.stopPropagation(); toggleWishlist(<?php echo htmlspecialchars($jsData); ?>)" 
            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white shadow-md border border-slate-50 flex items-center justify-center text-slate-400 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0">
            <i class="ph-bold ph-heart"></i>
        </button>
    </div>

    <!-- Content Section -->
    <div class="p-4 flex flex-col items-center flex-1 border-t border-slate-50 text-center">
        <h3 class="font-bold text-slate-800 text-xs md:text-sm leading-tight mb-4 line-clamp-2 group-hover:text-red-600 transition-colors min-h-[2.5rem]">
            <?php echo htmlspecialchars($p['name']); ?>
        </h3>
        
        <div class="mt-auto w-full flex flex-col items-center gap-3">
            <div class="flex flex-col items-center">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Price</span>
                <span class="text-sm md:text-xl font-black text-red-600 leading-none">৳ <?php echo number_format($p['price']); ?></span>
            </div>
            
            <button onclick="event.stopPropagation(); addToCart(<?php echo htmlspecialchars(json_encode($p ?? [])); ?>);"
                class="gazi-action-btn w-full py-2 rounded-lg bg-slate-50 border border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2 hover:bg-red-600 hover:text-white">
                <i class="ph-bold ph-shopping-cart-simple text-sm"></i> Add to Cart
            </button>
        </div>
    </div>
    
    <!-- Hover "View Details" Overlay -->
    <div class="absolute inset-0 bg-red-600/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
</div>


