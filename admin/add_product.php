<?php require_once '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة منتج - لوحة التحكم</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-lg bg-slate-800 border border-slate-700 rounded-3xl p-8 shadow-2xl">
        <h1 class="text-xl font-bold mb-6 text-indigo-400">إضافة منتج جديد للمتجر</h1>
        <form action="save_product.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="name" required placeholder="اسم المنتج" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <input type="number" step="0.01" name="price" required placeholder="السعر ($)" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm">
                <input type="text" name="category" required placeholder="التصنيف" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm">
            </div>
            <input type="url" name="image_url" required placeholder="رابط الصورة (URL)" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm">
            <textarea name="description" placeholder="وصف المنتج" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm" rows="3"></textarea>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 font-bold py-3 rounded-xl transition text-sm">حفظ ونشر المنتج</button>
            <div class="text-center mt-4"><a href="index.php" class="text-xs text-slate-400 hover:underline">العودة للوحة التحكم</a></div>
        </form>
    </div>
</body>
</html>