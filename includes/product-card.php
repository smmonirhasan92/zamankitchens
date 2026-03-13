<?php
/**
 * Reusable Product Card Component
 * Expects: $p array with keys: id, name, main_image, price, slug
 */
$imgSrc = !empty($p['image']) ? $p['image'] : (!empty($p['main_image']) ? $p['main_image'] : 'assets/images/placeholder.jpg');
?>
<div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-amber-300 hover:shadow-xl transition duration-300 flex flex-col">
    <!-- Image -->
    <a href="product/<?php echo htmlspecialchars($p['slug'] ?? ''); ?>" class="block overflow-hidden relative" style="padding-top: 75%;">
        <img loading="lazy"
            src="<?php echo htmlspecialchars($imgSrc); ?>"
            alt="<?php echo htmlspecialchars($p['name']); ?>"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500"
            onerror="this.src='https://placehold.co/400x300/f5f5f5/aaa?text=No+Image'">
        <?php if (!empty($p['is_featured'])): ?>
        <span class="absolute top-2 left-2 bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Featured</span>
        <?php endif; ?>
    </a>
    <!-- Info -->
    <div class="p-5 flex flex-col flex-1">
        <h3 class="font-black text-slate-800 text-sm md:text-lg mb-2 line-clamp-2 italic">
            <a href="product/<?php echo htmlspecialchars($p['slug'] ?? ''); ?>" class="hover:text-amber-600 transition">
                <?php echo htmlspecialchars($p['name']); ?>
            </a>
        </h3>
        <div class="mt-auto pt-4 flex items-center justify-between border-t border-slate-50">
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Price</span>
                <span class="text-xl font-black text-amber-600">৳ <?php echo number_format($p['price']); ?></span>
            </div>
            <div class="flex gap-2">
                <!-- Data for JS -->
                <?php 
                $jsData = json_encode([
                    'id' => $p['id'],
                    'name' => $p['name'],
                    'price' => $p['price'],
                    'image' => $imgSrc,
                    'description' => $p['description'] ?? 'Premium quality kitchen appliance.'
                ], JSON_HEX_APOS);
                ?>
                <button onclick='openQuickView(<?php echo $jsData; ?>)' class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-amber-100 flex items-center justify-center transition" title="Quick View">
                    👁️
                </button>
                <button onclick='addToCart(<?php echo $jsData; ?>)' class="w-10 h-10 rounded-xl bg-slate-900 hover:bg-slate-800 flex items-center justify-center transition shadow-lg shadow-slate-200" title="Add to Bag">
                    <span class="text-white">👜</span>
                </button>
            </div>
        </div>
    </div>
</div>
