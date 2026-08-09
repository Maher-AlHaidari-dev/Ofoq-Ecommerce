<?php 
require_once 'config/db.php';
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر أُفُق - التسوق الذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>.glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }</style>
</head>
<body class="bg-[#0b0a1d] text-slate-100 min-h-screen flex flex-col justify-between">
    <header class="sticky top-0 z-40 glass border-b border-slate-800">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between gap-6">
            <a href="index.php" class="text-2xl font-bold text-indigo-400"><i class="fa-solid fa-gem"></i> أُفق</a>
            
            <div class="flex-1 max-w-lg relative hidden md:block">
                <input type="text" id="smart-search-input" placeholder="ابحث بالذكاء الاصطناعي..." class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-2 rounded-xl focus:outline-none focus:border-indigo-500">
                <button onclick="performSmartSearch()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition">بحث</button>
            </div>

            <div class="flex items-center gap-4">
                <a href="track_order.php" class="text-xs bg-slate-800 px-4 py-2.5 rounded-xl border border-slate-700">تتبع الطلب</a>
                <button onclick="openCheckoutModal()" class="bg-indigo-600 px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-bag-shopping"></i> السلة <span id="cart-count" class="bg-white text-indigo-950 text-xs px-2 py-0.5 rounded-full font-bold">0</span>
                </button>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10 flex-1">
        <div id="products-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($products as $p): ?>
                <div class="glass rounded-3xl overflow-hidden flex flex-col justify-between">
                    <>
                               <?php
// تحضير روابط الصور من حقل image_url
$images = array_map('trim', explode(',', $p['image_url'] ?? ''));
$images = array_filter($images);
$mainImage = !empty($images) ? $images[0] : 'assets/images/default.jpg';
?>

<div class="product-gallery">
    <!-- الصورة الرئيسية -->
    <img id="mainImg_<?php echo $p['id']; ?>" 
         src="<?php echo htmlspecialchars($mainImage); ?>" 
         class="w-full h-48 object-cover transition-all duration-300">

    <!-- صور الخيارات والألوان -->
    <?php if (count($images) > 1): ?>
        <div class="flex justify-center gap-1 p-2 bg-slate-900/50 border-b border-slate-800 overflow-x-auto">
            <?php foreach ($images as $index => $imgUrl): ?>
                <img src="<?php echo htmlspecialchars($imgUrl); ?>" 
                     onclick="changeProductImg('mainImg_<?php echo $p['id']; ?>', '<?php echo htmlspecialchars($imgUrl); ?>', this)" 
                     class="color-thumb-<?php echo $p['id']; ?> w-8 h-8 rounded-md border-2 <?php echo $index === 0 ? 'border-indigo-500' : 'border-transparent'; ?> hover:border-indigo-400 cursor-pointer object-cover transition-all" 
                     alt="خيار اللون">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

    
    
                        <div class="p-6">
                            <span class="text-xs bg-indigo-500/20 text-indigo-300 px-3 py-1 rounded-full"><?= htmlspecialchars($p['category']) ?></span>
                            <h3 class="text-xl font-bold mt-3"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="text-slate-400 text-sm mt-2"><?= htmlspecialchars($p['description']) ?></p>
                        </div>
                    </div>
                    <div class="p-6 pt-0 flex justify-between items-center border-t border-slate-800 mt-4">
                        <span class="text-2xl font-black">$<?= number_format($p['price'], 2) ?></span>
                        <button onclick="addToCart(<?= $p['id'] ?>, '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>', <?= $p['price'] ?>)" class="bg-indigo-600 px-4 py-2 rounded-xl text-sm font-bold">إضافة للسلة</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="checkout-modal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="glass w-full max-w-lg rounded-3xl p-8 bg-[#0e0d24] relative">
            <button onclick="closeCheckoutModal()" class="absolute top-6 left-6 text-slate-400"><i class="fa-solid fa-xmark text-xl"></i></button>
            <h2 class="text-2xl font-bold mb-6">إتمام الدفع</h2>
            <div id="cart-items-list" class="max-h-40 overflow-y-auto mb-4 border-y border-slate-800 py-3 text-sm"></div>
            <div class="flex justify-between font-bold text-xl mb-6"><span>الإجمالي:</span><span id="cart-total-price" class="text-green-400">$0.00</span></div>
            <form action="checkout.php" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="cart_data" id="cart-data-input">
                <input type="text" name="customer_name" required placeholder="اسم العميل الكامل" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500">
                <input type="tel" name="customer_phone" required placeholder="رقم الهاتف" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500">
                <input type="text" name="shipping_address" required placeholder="عنوان التوصيل (المدينة، الحي، الشارع)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-500 font-bold py-3.5 rounded-xl text-sm transition-colors">إتمام الشراء وإصدار الفاتورة</button>
            </form>
        </div>
    </div>

    <footer class="bg-indigo-950 text-indigo-200 border-t border-indigo-900 mt-12 py-8">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h4 class="text-lg font-bold text-white mb-3">عن متجر أُفُق</h4>
                <p class="text-xs text-indigo-300 leading-relaxed">منصة تسوق إلكترونية مدعومة بالذكاء الاصطناعي وبوابات الدفع التفاعلية من تطوير فريق TikiTaka-Devs.</p>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-3">خدمات العملاء</h4>
                <ul class="text-xs space-y-2">
                    <li><a href="track_order.php" class="hover:text-white">تتبع الطلبات وطباعة الفواتير</a></li>
                    <li><a href="#" onclick="alert('جاري تشغيل المساعد الذكي...'); return false;" class="hover:text-white">المساعد الذكي للمشتريات</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-3">إدارة النظام</h4>
                <a href="admin/index.php" target="_blank" class="inline-block text-xs bg-indigo-900 hover:bg-indigo-800 border border-indigo-700 text-indigo-200 px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-lock ml-1"></i> دخول لوحة التحكم (للإدارة فقط)
                </a>
                <p class="text-xs text-indigo-400 mt-4">© 2026 TikiTaka-Devs. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
    let cart = [];

    function addToCart(id, name, price) {
        cart.push({ id, name, price });
        updateCartUI();
        alert('تمت إضافة المنتج للسلة بنجاح!');
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
                    grid.innerHTML += `
                        <div class="glass rounded-3xl overflow-hidden p-6 flex flex-col justify-between">
                            <img src="${p.image_url}" class="w-full h-48 object-cover rounded-xl mb-4">
                            <div>
                                <h3 class="text-xl font-bold mb-2">${p.name}</h3>
                                <p class="text-slate-400 text-sm mb-4">${p.description || ''}</p>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold">$${p.price}</span>
                                <button onclick="addToCart(${p.id}, '${p.name}', ${p.price})" class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-xl text-sm font-bold">إضافة للسلة</button>
                            </div>
                        </div>`;
                });
            }
        } catch (err) {
            console.error('Error in search:', err);
        }
    }
    <>
function changeProductImg(mainImgId, newSrc, element) {
    document.getElementById(mainImgId).src = newSrc;
    
    // إزالة التحديد عن باقي الخيارات لنفس المنتج
    const container = element.parentElement;
    container.querySelectorAll('img').forEach(img => {
        img.classList.remove('border-indigo-500');
        img.classList.add('border-transparent');
    });
    
    // إضافة الإطار الملون على الخيار المختار
    element.classList.remove('border-transparent');
    element.classList.add('border-indigo-500');
}

    </script>
</body>
</html>