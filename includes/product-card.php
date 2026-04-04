<?php
/**
 * Reusable Product Card Component — Vibrant Premium Edition
 */
$imgSrc = !empty($p['main_image']) ? $p['main_image'] : (!empty($p['image']) ? $p['image'] : '');
$isOutOfStock = isset($p['stock_status']) && $p['stock_status'] === 'Out of Stock';
?>
<?php
$jsData = json_encode([
    'id'          => $p['id'],
    'name'        => $p['name'],
    'price'       => $p['price'],
    'old_price'   => $p['old_price'] ?? 0,
    'image'       => $imgSrc,
    'description' => $p['description'] ?? 'Premium quality kitchen appliance.'
], JSON_HEX_APOS);

$oldPrice = (float)($p['old_price'] ?? 0);
$currentPrice = (float)$p['price'];
$discountPercent = 0;
if ($oldPrice > $currentPrice) {
    $discountPercent = round((($oldPrice - $currentPrice) / $oldPrice) * 100);
}
?>

<style>
.gazi-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.gazi-card:hover { border-color: rgba(216, 0, 50, 0.4); transform: translateY(-4px); }
.gazi-card:hover .gazi-img { transform: scale(1.08); }
.gazi-img { transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
.btn-buy { background: #d80032; } /* Unifying to Red theme */
.btn-cart { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
</style>
</style>

<div onclick='<?php echo $isOutOfStock ? "" : "openQuickView($jsData)"; ?>' 
     class="gazi-card <?php echo $isOutOfStock ? 'cursor-not-allowed opacity-75' : 'cursor-pointer'; ?> bg-white rounded-2xl overflow-hidden border border-slate-100 flex flex-col h-full group relative shadow-sm hover:shadow-xl hover:shadow-red-500/5">
    
    <!-- Image Section -->
    <div class="relative overflow-hidden bg-white p-3 aspect-[4/3] flex items-center justify-center">
        <?php if (!empty($imgSrc)): ?>
            <img loading="lazy" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="gazi-img w-full h-full object-contain">
        <?php else: ?>
            <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200">
                <i class="ph-bold ph-image text-3xl"></i>
            </div>
        <?php endif; ?>

        <!-- Discount Badge -->
        <?php if ($discountPercent > 0): ?>
        <div class="absolute top-2 left-2 z-20">
            <span class="bg-red-600 text-white text-[10px] md:text-[11px] font-black px-2 py-1 rounded shadow-lg flex items-center gap-1">
                -<?php echo $discountPercent; ?>%
            </span>
        </div>
        <?php endif; ?>

        <!-- Out of Stock Badge -->
        <?php if ($isOutOfStock): ?>
        <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center">
            <span class="bg-red-600 text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full">Sold Out</span>
        </div>
        <?php endif; ?>

        <!-- Wishlist Toggle (only if in stock) -->
        <?php if (!$isOutOfStock): ?>
        <button onclick="event.stopPropagation(); toggleWishlist(<?php echo htmlspecialchars($jsData); ?>)" 
            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 backdrop-blur shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 text-xs">
            <i class="ph-bold ph-heart"></i>
        </button>
        <?php endif; ?>
    </div>

    <!-- Content Section -->
    <div class="p-4 pt-1 flex flex-col items-center flex-1 text-center">
        <h3 class="font-bold text-slate-700 text-[11px] md:text-sm leading-snug mb-3 line-clamp-2 group-hover:text-red-600 transition-colors min-h-[2.4rem] px-2">
            <?php echo htmlspecialchars($p['name']); ?>
        </h3>

        <div class="mt-auto w-full flex flex-col items-center">
            <div class="flex flex-col items-center mb-4">
                <?php if ($discountPercent > 0): ?>
                    <span class="text-[10px] md:text-[12px] text-slate-400 line-through font-bold">৳ <?php echo number_format($oldPrice); ?></span>
                <?php endif; ?>
                <span class="text-[15px] md:text-[20px] font-black text-red-600 tracking-tight leading-none">৳ <?php echo number_format($currentPrice); ?></span>
            </div>

            <?php if ($isOutOfStock): ?>
            <button disabled
                class="w-full py-2.5 rounded-xl bg-slate-300 text-slate-500 font-bold text-[10px] uppercase tracking-widest cursor-not-allowed flex items-center justify-center gap-2">
                <i class="ph-bold ph-x-circle text-xs"></i> <span>Out of Stock</span>
            </button>
            <?php else: ?>
            <div class="grid grid-cols-2 gap-2 w-full">
                <button onclick="event.stopPropagation(); buyNow(<?php echo htmlspecialchars($jsData); ?>);"
                    class="btn-buy text-white font-bold text-[9px] md:text-[11px] py-3 rounded-lg uppercase tracking-tight transition-all active:scale-95 shadow-md">
                    Buy Now
                </button>
                <button onclick="event.stopPropagation(); addToCart(<?php echo htmlspecialchars($jsData); ?>);"
                    class="btn-cart font-bold text-[9px] md:text-[11px] py-3 rounded-lg uppercase tracking-tight transition-all active:scale-95 border border-slate-200 shadow-sm">
                    Add to cart
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


