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
.gazi-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.gazi-card:hover { border-color: rgba(216, 0, 50, 0.4); transform: translateY(-4px); }
.gazi-card:hover .gazi-img { transform: scale(1.04); }
.gazi-img { transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
</style>

<div onclick='openQuickView(<?php echo $jsData; ?>)' class="gazi-card cursor-pointer bg-white rounded-2xl overflow-hidden border border-slate-100 flex flex-col h-full group relative shadow-sm hover:shadow-xl hover:shadow-red-500/5">
    <!-- Image Section -->
    <div class="relative overflow-hidden bg-white p-3 aspect-[4/3] flex items-center justify-center">
        <?php if (!empty($imgSrc)): ?>
            <img loading="lazy" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="gazi-img w-full h-full object-contain">
        <?php else: ?>
            <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200">
                <i class="ph-bold ph-image text-3xl"></i>
            </div>
        <?php endif; ?>
        
        <!-- Wishlist Toggle -->
        <button onclick="event.stopPropagation(); toggleWishlist(<?php echo htmlspecialchars($jsData); ?>)" 
            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 backdrop-blur shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 text-xs">
            <i class="ph-bold ph-heart"></i>
        </button>
    </div>

    <!-- Content Section -->
    <div class="p-4 pt-1 flex flex-col items-center flex-1 text-center">
        <h3 class="font-bold text-slate-700 text-[11px] md:text-sm leading-snug mb-3 line-clamp-2 group-hover:text-red-600 transition-colors min-h-[2.4rem] px-2">
            <?php echo htmlspecialchars($p['name']); ?>
        </h3>
        
        <div class="mt-auto w-full flex flex-col items-center">
            <div class="mb-4">
                <span class="text-[14px] md:text-[18px] font-black text-red-600 tracking-tight">৳ <?php echo number_format($p['price']); ?></span>
            </div>
            
            <button onclick="event.stopPropagation(); addToCart(<?php echo htmlspecialchars(json_encode($p ?? [])); ?>);"
                class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2 hover:bg-red-600 shadow-lg shadow-slate-900/10 hover:shadow-red-600/20 active:scale-95">
                <i class="ph-bold ph-shopping-bag text-xs"></i> <span>Add to Bag</span>
            </button>
        </div>
    </div>
</div>


