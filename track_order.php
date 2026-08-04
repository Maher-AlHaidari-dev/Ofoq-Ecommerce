<?php
require_once 'config/db.php';
$order = null;
if (isset($_GET['code'])) {
    $c = trim($_GET['code']);
    $st = $pdo->prepare("SELECT * FROM orders WHERE tracking_code = ?");
    $st->execute([$c]);
    $order = $st->fetch();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"><title>تتبع الطلب</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-900 text-slate-100 p-8 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-slate-800 p-8 rounded-3xl border border-slate-700">
        <h1 class="text-xl font-bold mb-4 text-indigo-400">تتبع حالة شحنتك</h1>
        <form method="GET" class="flex gap-2 mb-6">
            <input type="text" name="code" placeholder="أدخل رقم التتبع" required class="flex-1 bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm">
            <button type="submit" class="bg-indigo-600 px-4 rounded-xl text-sm font-bold">تتبع</button>
        </form>
        <?php if($order): ?>
            <div class="bg-slate-900 p-4 rounded-xl text-sm space-y-2">
                <div>العميل: <?= htmlspecialchars($order['customer_name']) ?></div>
                <div>الحالة: <span class="text-indigo-400 font-bold"><?= strtoupper($order['status']) ?></span></div>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4"><a href="index.php" class="text-xs text-slate-400 hover:underline">العودة للمتجر</a></div>
    </div>
</body>
</html>