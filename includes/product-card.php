<?php
/**
 * Reusable Product Card Component
 * Expects: $p array with keys: id, name, main_image, price, slug
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
<div onclick='openQuickView(<?php echo $jsData; ?>)' class="cursor-pointer group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-amber-300 hover:shadow-xl transition duration-300 flex flex-col relative">
    <!-- Image -->
    <div class="block overflow-hidden relative" style="padding-top: 75%;">
        <img loading="lazy"
            src="<?php echo htmlspecialchars($imgSrc); ?>"
            alt="<?php echo htmlspecialchars($p['name']); ?>"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500"
            onerror="this.src='https://placehold.co/400x300/f5f5f5/aaa?text=No+Image'">
        <?php if (!empty($p['is_featured'])): ?>
        <span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-md z-10">Featured</span>
        <?php endif; ?>
        
        <!-- Wishlist Button -->
        <button onclick="event.stopPropagation(); toggleWishlist(<?php echo htmlspecialchars($jsData); ?>)" class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/80 backdrop-blur border border-transparent flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-white hover:border-rose-100 shadow-sm transition wishlist-btn-<?php echo $p['id']; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>

        <!-- Compare Button -->
        <button onclick="event.stopPropagation(); toggleCompare(<?php echo htmlspecialchars($jsData); ?>)" class="absolute top-14 right-3 z-20 w-8 h-8 rounded-full bg-white/80 backdrop-blur border border-transparent flex items-center justify-center text-slate-400 hover:text-amber-600 hover:bg-white hover:border-amber-100 shadow-sm transition" title="Compare">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
        </button>
    </div>
    <!-- Info -->
    <div class="p-4 flex flex-col flex-1">
        <h3 class="font-black text-slate-800 text-sm md:text-base mb-2 line-clamp-2 italic group-hover:text-amber-600 transition">
            <?php echo htmlspecialchars($p['name']); ?>
        </h3>
        <div class="mt-auto pt-3 flex items-center justify-between border-t border-slate-100 gap-2">
            <div class="flex flex-col min-w-[70px]">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Price</span>
                <span class="text-sm md:text-base font-black text-amber-600 leading-none">৳ <?php echo number_format($p['price']); ?></span>
            </div>
            
            <div class="flex items-center gap-1.5 relative z-20 flex-1 justify-end">
                <!-- Add to Bag Icon Only -->
                <button onclick="event.stopPropagation(); addToCart(<?php echo htmlspecialchars(json_encode($p)); ?>);"
                    class="h-8 w-8 md:h-9 md:w-9 flex-shrink-0 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-600 transition group/btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </button>

                <!-- Direct Buy Now -->
                <button onclick="event.stopPropagation(); buyNow(<?php echo htmlspecialchars(json_encode($p)); ?>);"
                    class="bg-slate-900 hover:bg-slate-800 text-white text-[10px] md:text-xs font-black uppercase tracking-wider px-3 md:px-4 py-2 md:py-2.5 rounded-lg transition shadow-md shadow-slate-200 whitespace-nowrap">
                    Buy Now
                </button>
            </div>
        </div>
    </div>
</div>
