
<?php 
require_once __DIR__ . '/../config/db.php'; 
// باقي كود لوحة التحكم...

$totalSales = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'pending'")->fetchColumn() ?: 0;
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم المتكاملة - أُفُق</title>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Tajawal', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 flex min-h-screen">

    <!-- القائمة الجانبية Sidebar -->
    <aside class="w-64 bg-slate-950 p-6 border-r border-slate-800 flex flex-col justify-between">
        <div>
            <h2 class="text-xl font-black text-indigo-400 mb-8 flex items-center gap-2">
                <i class="fa-solid fa-gauge"></i> لوحة الإدارة
            </h2>
            <nav class="space-y-3 text-sm">
                <a href="#orders-section" class="block py-2.5 px-4 rounded-xl bg-indigo-600/20 text-indigo-300 font-bold border border-indigo-500/30">
                    <i class="fa-solid fa-cart-shopping ml-2"></i> إدارة الطلبات
                </a>
                <a href="#products-section" class="block py-2.5 px-4 rounded-xl hover:bg-slate-800 text-slate-300">
                    <i class="fa-solid fa-boxes-stacked ml-2"></i> إدارة المنتجات
                </a>
                <a href="add_product.php" class="block py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition">
                    <i class="fa-solid fa-plus ml-2"></i> إضافة منتج جديد
                </a>
            </nav>
        </div>
        <div>
            <a href="../index.php" class="block py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-center text-xs font-bold">
                <i class="fa-solid fa-store ml-1"></i> الانتقال للمتجر
            </a>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <main class="flex-1 p-8 overflow-y-auto space-y-10">
        
        <!-- الإحصائيات -->
        <div>
            <h1 class="text-2xl font-black mb-6">متابعة الأداء والإحصائيات</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400">إجمالي المبيعات</span>
                        <h3 class="text-3xl font-black text-green-400 mt-1">$<?= number_format($totalSales, 2) ?></h3>
                    </div>
                    <i class="fa-solid fa-wallet text-4xl text-green-500/20"></i>
                </div>
                <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400">إجمالي الطلبات</span>
                        <h3 class="text-3xl font-black text-indigo-400 mt-1"><?= $totalOrders ?> طلب</h3>
                    </div>
                    <i class="fa-solid fa-bag-shopping text-4xl text-indigo-500/20"></i>
                </div>
            </div>
        </div>

        <!-- 📦 قسم إدارة المنتجات (تعديل + حذف) -->
        <section id="products-section" class="bg-slate-800 rounded-2xl p-6 border border-slate-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-indigo-400"></i> إدارة المنتجات المتاحة
                </h2>
                <a href="add_product.php" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                    + إضافة منتج
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700">
                            <th class="p-3">الصورة</th>
                            <th class="p-3">اسم المنتج</th>
                            <th class="p-3">التصنيف</th>
                            <th class="p-3">السعر</th>
                            <th class="p-3 text-center">الإجراءات (تعديل / حذف)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="5" class="p-4 text-center text-slate-500">لا توجد منتجات حالياً.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($products as $p): ?>
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/20 transition">
                            <td class="p-3">
                                <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-12 h-12 object-cover rounded-xl border border-slate-700">
                            </td>
                            <td class="p-3 font-bold"><?= htmlspecialchars($p['name']) ?></td>
                            <td class="p-3"><span class="px-2.5 py-1 rounded-full text-xs bg-slate-700 text-slate-300"><?= htmlspecialchars($p['category']) ?></span></td>
                            <td class="p-3 text-green-400 font-bold">$<?= number_format($p['price'], 2) ?></td>
                            <td class="p-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- زر التعديل -->
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white px-3 py-1.5 rounded-xl text-xs transition border border-amber-500/30">
                                        <i class="fa-solid fa-pen-to-square"></i> تعديل
                                    </a>

                                    <!-- زر الحذف -->
                                    <form action="delete_product.php" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف هذا المنتج نهائياً؟');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white px-3 py-1.5 rounded-xl text-xs transition border border-rose-500/30">
                                            <i class="fa-solid fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 🛒 قسم إدارة الطلبات الواردة -->
        <section id="orders-section" class="bg-slate-800 rounded-2xl p-6 border border-slate-700">
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-cart-flatbed text-indigo-400"></i> قائمة الطلبات الواردة
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700">
                            <th class="p-3">رقم التتبع</th>
                            <th class="p-3">العميل</th>
                            <th class="p-3">الهاتف</th>
                            <th class="p-3">المبلغ</th>
                            <th class="p-3">الحالة</th>
                            <th class="p-3">تحديث</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr class="border-b border-slate-700/50">
                            <td class="p-3 font-mono text-indigo-300"><?= $o['tracking_code'] ?></td>
                            <td class="p-3"><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td class="p-3 font-mono text-xs text-slate-400"><?= htmlspecialchars($o['customer_phone']) ?></td>
                            <td class="p-3 text-green-400 font-bold">$<?= $o['total_amount'] ?></td>
                            <td class="p-3"><span class="px-2.5 py-1 rounded-full text-xs bg-indigo-500/20 text-indigo-300"><?= strtoupper($o['status']) ?></span></td>
                            <td class="p-3">
                                <form action="update_status.php" method="POST" class="flex gap-2">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="status" class="bg-slate-900 border border-slate-700 text-xs rounded-lg p-1.5 text-white">
                                        <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Pending</option>
                                        <option value="shipped" <?= $o['status']=='shipped'?'selected':'' ?>>Shipped</option>
                                        <option value="delivered" <?= $o['status']=='delivered'?'selected':'' ?>>Delivered</option>
                                    </select>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-3 py-1.5 rounded-lg text-xs font-bold">حفظ</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</body>
</html>