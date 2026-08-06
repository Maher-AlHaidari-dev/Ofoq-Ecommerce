// 1. إدارة سلة التسوق
let cart = [];

// إضافة عنصر للسلة
function addToCart(id, name, price) {
    cart.push({ id, name, price });
    
    const cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) {
        cartCountEl.innerText = cart.length;
    }
    
    alert(`تمت إضافة "${name}" إلى السلة بنجاح!`);
}

// 2. إظهار/إخفاء نافذة المساعد الذكي
function toggleAIChat() {
    const chatBox = document.getElementById('ai-chat-box');
    if (chatBox) {
        chatBox.classList.toggle('hidden');
    }
}

// 3. إرسال سؤال للمساعد الذكي (مرتبط بمنتجات المتجر والسلة)
async function sendAIMessage() {
    const input = document.getElementById('ai-input');
    if (!input) return;

    const message = input.value.trim();
    if (!message) return;

    const chatMessages = document.getElementById('chat-messages');

    // عرض رسالة المستخدم
    chatMessages.innerHTML += `
        <div class="bg-indigo-600 text-white p-3 rounded-lg max-w-[85%] ml-auto text-right mb-2">
            ${message}
        </div>`;
    input.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // حالة التحميل
    const loadingId = 'loading-' + Date.now();
    chatMessages.innerHTML += `
        <div id="${loadingId}" class="bg-slate-700 text-slate-200 p-3 rounded-lg max-w-[85%] mb-2">
            جاري التفكير...
        </div>`;
    chatMessages.scrollTop = chatMessages.scrollHeight;

    try {
        const response = await fetch('api/ai_assistant.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                message: message,
                cart: cart 
            })
        });
        const data = await response.json();
        
        const loadingEl = document.getElementById(loadingId);
        if (loadingEl) {
            loadingEl.innerText = data.reply || data.error || "لم يتم استلام رد مناسب.";
        }
    } catch (e) {
        const loadingEl = document.getElementById(loadingId);
        if (loadingEl) {
            loadingEl.innerText = "حدث خطأ في الاتصال بالخادم.";
        }
    }
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// 4. تنفيذ البحث بالذكاء الاصطناعي وإظهار التنبيهات والتمرير
async function performSearch() {
    const searchInput = document.getElementById('search-input'); // افترضنا أن id الإدخال هو search-input
    if (!searchInput) return;

    const searchQuery = searchInput.value.trim();
    if (!searchQuery) return;

    const productsGrid = document.getElementById('products-grid');
    if (!productsGrid) return;

    // حالة الانتظار
    productsGrid.innerHTML = `
        <div class="col-span-full text-center py-12">
            <p class="text-xl text-indigo-400">جاري البحث بالذكاء الاصطناعي عن: "${searchQuery}"...</p>
        </div>`;

    try {
        const response = await fetch('api/semantic_search.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: searchQuery })
        });

        const data = await response.json();

        // 1. في حال عدم وجود نتائج
        if (!data.products || data.products.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <p class="text-xl text-slate-400">عذراً، لم نجد أي منتجات تطابق "${searchQuery}"</p>
                </div>`;
            return;
        }

        // 2. عرض المنتجات المرجعة
        renderProducts(data.products);

        // 3. التمرير التلقائي السلس لقسم المنتجات ليرى المستخدم التغيير
        productsGrid.scrollIntoView({ behavior: 'smooth' });

    } catch (error) {
        console.error("خطأ في عملية البحث:", error);
        productsGrid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-xl text-red-400">حدث خطأ أثناء جلب النتائج، يرجى المحاولة لاحقاً.</p>
            </div>`;
    }
}

// 5. دالة إعادة رسم كروت المنتجات داخل الشبكة
function renderProducts(products) {
    const productsGrid = document.getElementById('products-grid');
    if (!productsGrid) return;

    productsGrid.innerHTML = products.map(product => `
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-lg flex flex-col justify-between">
            <img src="${product.image || 'https://via.placeholder.com/300'}" alt="${product.name}" class="w-full h-48 object-cover">
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
}