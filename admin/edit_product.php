<?php require_once __DIR__ . '/../config/db.php'; 

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("المنتج غير موجود.");
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل منتج - لوحة التحكم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Tajawal', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-lg bg-slate-800 border border-slate-700 rounded-3xl p-8 shadow-2xl">
        <h1 class="text-xl font-bold mb-6 text-indigo-400">تعديل بيانات المنتج</h1>
        
        <form action="update_product.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">اسم المنتج</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">السعر ($)</label>
                    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">التصنيف</label>
                    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">رابط الصورة (URL)</label>
                <input type="url" name="image_url" value="<?= htmlspecialchars($product['image_url']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1">وصف المنتج</label>
                <textarea name="description" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 font-bold py-3 rounded-xl transition text-sm shadow-lg">حفظ التغييرات 💾</button>
            <div class="text-center mt-4"><a href="index.php" class="text-xs text-slate-400 hover:underline">إلغاء والعودة للوحة التحكم</a></div>
        </form>
    </div>
</body>
</html>