async function performSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    const searchQuery = searchInput.value.trim();
    if (!searchQuery) return;

    try {
        const response = await fetch('api/semantic_search.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: searchQuery })
        });

        const data = await response.json();
        const productsGrid = document.getElementById('products-grid');
        
        if (!productsGrid) return;

        // التحقق من وجود منتجات راجعة
        if (!data.products || data.products.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <p class="text-xl text-slate-400">عذراً، لم نجد أي منتجات تطابق "${searchQuery}"</p>
                </div>`;
            return;
        }

        // إعادة رسم الكروت بالمنتجات المطابقة للبحث فقط
        productsGrid.innerHTML = data.products.map(product => `
            <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-lg flex flex-col justify-between">
                <img src="${product.image_url || product.image || 'https://via.placeholder.com/300'}" alt="${product.name}" class="w-full h-48 object-cover">
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-2">${product.name}</h3>
                        <p class="text-slate-400 text-sm mb-4">${product.description || ''}</p>
                    </div>
                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-xl font-bold text-indigo-400">$${product.price}</span>
                        <button onclick="addToCart(${product.id}, '${product.name}', ${product.price})" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            إضافة للسلة
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // التمرير التلقائي للنتائج
        productsGrid.scrollIntoView({ behavior: 'smooth' });

    } catch (error) {
        console.error("خطأ في البحث:", error);
    }
}