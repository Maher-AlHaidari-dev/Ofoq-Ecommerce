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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>.glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }</style>
 </head>
 <body class="bg-[#0b0a1d] text-slate-100 min-h-screen flex flex-col justify-between">
     <header class="sticky top-0 z-40 glass border-b border-slate-800">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between gap-6">
        <a href="index.php" class="text-2xl font-bold text-indigo-400"><i class="fa-solid fa-gem"></i> أُفق</a>
        
        <div class="flex-1 max-w-lg relative hidden md:block">
            <input type="text" id="search-input" placeholder="ابحث بالذكاء الاصطناعي..." class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-2 rounded-xl focus:outline-none focus:border-indigo-500">
            <button onclick="performSearch()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition">بحث</button>
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
                    <div>
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-full h-48 object-cover">
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
                 <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                 <input type="hidden" name="cart_data" id="cart-data-input">
                 <input type="text" name="customer_name" required placeholder="اسم العميل الكامل" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500">
                 <input type="tel" name="customer_phone" required placeholder="رقم الهاتف" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500">
                 <input type="text" name="shipping_address" required placeholder="عنوان التوصيل (المدينة، الحي، الشارع)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500">
                 <button type="submit" class="w-full bg-green-600 hover:bg-green-500 font-bold py-3.5 rounded-xl text-sm transition-colors">إتمام الشراء وإصدار الفاتورة</button>
               </form>
        </div>
     </div>

     <footer class="glass border-t border-slate-800 py-6 text-center text-xs text-slate-500">
      <footer class="bg-indigo-950 text-indigo-200 border-t border-indigo-900 mt-12 py-8">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h4 class="text-lg font-bold text-white mb-3">عن متجر أُفُق</h4>
                <p class="text-xs text-indigo-300 leading-relaxed">منصة تسوق إلكترونية مدعومة بالذكاء الاصطناعي وبوابات الدفع التفاعلية من تطوير فريق **TikiTaka-Devs**.</p>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-3">خدمات العملاء</h4>
                <ul class="text-xs space-y-2">
                    <li><a href="track_order.php" class="hover:text-white">تتبع الطلبات وطباعة الفواتير</a></li>
                    <li><a href="#" onclick="toggleAIChat(); return false;" class="hover:text-white">المساعد الذكي للمشتريات</a></li>
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
    

        <script>
        <script src="/assets/js/app.js"></script>
     </script>
 </body>
</html>