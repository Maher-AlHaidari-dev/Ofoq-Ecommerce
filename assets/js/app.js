let cart = [];

function addToCart(id, name, price) {
    cart.push({ id, name, price });
    updateCartUI();
    alert('تمت الإضافة للسلة!');
}

function updateCartUI() {
    const cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) cartCountEl.innerText = cart.length;

    const list = document.getElementById('cart-items-list');
    const totalEl = document.getElementById('cart-total-price');
    const cartDataInput = document.getElementById('cart-data-input');

    let total = 0;
    if (list) {
        list.innerHTML = '';
        cart.forEach(i => {
            total += Number(i.price);
            list.innerHTML += `<div class="flex justify-between py-1"><span>${i.name}</span><span class="text-green-400">$${i.price}</span></div>`;
        });
    }

    if (totalEl) totalEl.innerText = `$${total.toFixed(2)}`;
    if (cartDataInput) cartDataInput.value = JSON.stringify(cart);
}

function openCheckoutModal() {
    if (cart.length === 0) return alert('السلة فارغة!');
    const modal = document.getElementById('checkout-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeCheckoutModal() {
    const modal = document.getElementById('checkout-modal');
    if (modal) modal.classList.add('hidden');
}

async function performSmartSearch() {
    const searchInput = document.getElementById('smart-search-input');
    if (!searchInput) return;
    
    const query = searchInput.value;
    if (!query) return;

    try {
        const res = await fetch('api/semantic_search.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query })
        });
        const data = await res.json();
        const grid = document.getElementById('products-grid');

        if (grid && data.products) {
            grid.innerHTML = '';
            data.products.forEach(p => {
                grid.innerHTML += `<div class="glass rounded-3xl overflow-hidden p-6"><img src="${p.image_url}" class="w-full h-48 object-cover rounded-xl mb-4"><h3 class="text-xl font-bold">${p.name}</h3></div>`;
            });
        }
    } catch (err) {
        console.error('Error in smart search:', err);
    }
}