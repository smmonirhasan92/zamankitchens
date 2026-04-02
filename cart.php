<?php
/**
 * Zaman Kitchens - Cart Page
 * Server-rendered cart page for SEO + direct URL access
 */
require_once __DIR__ . '/includes/db.php';
$pageTitle = "Shopping Cart";
include_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4 max-w-4xl">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">
                🛍️ Shopping Bag
            </h1>
            <a href="<?php echo SITE_URL; ?>" class="text-sm font-bold text-slate-400 hover:text-red-600 transition flex items-center gap-2">
                <i class="ph ph-arrow-left"></i> Continue Shopping
            </a>
        </div>

        <!-- Cart Container -->
        <div class="grid lg:grid-cols-3 gap-8">

            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div id="cart-page-items" class="space-y-4">
                    <!-- Loading State -->
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-100" id="cart-empty-state">
                        <div class="text-6xl mb-4">🛍️</div>
                        <h2 class="text-xl font-black text-slate-900 mb-2">Your bag is empty</h2>
                        <p class="text-slate-500 mb-6">Add some kitchen magic to get started!</p>
                        <a href="<?php echo SITE_URL; ?>" class="inline-block bg-red-600 hover:bg-red-700 text-white font-black px-8 py-3 rounded-xl transition">
                            Browse Products
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div id="cart-summary-box" class="bg-white rounded-3xl border border-slate-100 p-6 sticky top-24 hidden">
                    <h3 class="font-black text-slate-900 mb-6 text-sm uppercase tracking-widest">Order Summary</h3>

                    <div class="space-y-3 mb-6 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Subtotal</span>
                            <span id="cart-subtotal" class="font-black text-slate-900">৳ 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Shipping</span>
                            <span class="font-bold text-green-600">FREE</span>
                        </div>
                        <div class="flex justify-between text-base border-t border-slate-100 pt-3 mt-3">
                            <span class="font-black text-slate-900">Total</span>
                            <span id="cart-total-final" class="font-black text-red-600 text-xl">৳ 0</span>
                        </div>
                    </div>

                    <button onclick="openCheckout()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-2xl transition shadow-xl shadow-red-600/20 text-sm uppercase tracking-widest">
                        Proceed to Checkout
                    </button>
                    <p class="text-center text-[10px] text-slate-400 mt-3 font-bold uppercase tracking-widest">Cash on Delivery Available</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    renderCartPage();
});

function renderCartPage() {
    const cart = JSON.parse(localStorage.getItem('zk_cart')) || [];
    const container = document.getElementById('cart-page-items');
    const emptyState = document.getElementById('cart-empty-state');
    const summaryBox = document.getElementById('cart-summary-box');

    if (cart.length === 0) {
        emptyState.classList.remove('hidden');
        summaryBox.classList.add('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    summaryBox.classList.remove('hidden');

    let total = 0;
    let html = '';

    cart.forEach((item, index) => {
        total += item.price * item.qty;
        html += `
        <div class="bg-white rounded-2xl border border-slate-100 p-5 flex gap-5 items-center shadow-sm">
            <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-50 flex-shrink-0 border border-slate-100">
                <img src="${item.image}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/80x80/f8fafc/94a3b8?text=Img'">
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-black text-slate-900 text-sm leading-tight mb-1">${item.name}</h4>
                <p class="text-red-600 font-black text-base">৳ ${parseInt(item.price).toLocaleString()}</p>
                <div class="flex items-center gap-3 mt-3">
                    <button onclick="cartPageQty(${index}, -1)" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-bold hover:bg-red-50 hover:text-red-600 transition">-</button>
                    <span class="font-black text-sm">${item.qty}</span>
                    <button onclick="cartPageQty(${index}, 1)" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-bold hover:bg-red-50 hover:text-red-600 transition">+</button>
                </div>
            </div>
            <div class="text-right">
                <p class="font-black text-slate-900 mb-3">৳ ${(item.price * item.qty).toLocaleString()}</p>
                <button onclick="cartPageRemove(${index})" class="text-slate-300 hover:text-red-500 transition">
                    <i class="ph ph-trash text-lg"></i>
                </button>
            </div>
        </div>`;
    });

    container.innerHTML = html;
    document.getElementById('cart-subtotal').innerText = '৳ ' + total.toLocaleString();
    document.getElementById('cart-total-final').innerText = '৳ ' + total.toLocaleString();
}

function cartPageQty(index, delta) {
    let cart = JSON.parse(localStorage.getItem('zk_cart')) || [];
    cart[index].qty += delta;
    if (cart[index].qty < 1) {
        cart.splice(index, 1);
    }
    localStorage.setItem('zk_cart', JSON.stringify(cart));
    renderCartPage();
    renderCart(); // update side cart too
}

function cartPageRemove(index) {
    let cart = JSON.parse(localStorage.getItem('zk_cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('zk_cart', JSON.stringify(cart));
    renderCartPage();
    renderCart();
}
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
