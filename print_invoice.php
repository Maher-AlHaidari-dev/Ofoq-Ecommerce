<?php
require_once 'config/db.php';
$code = trim(filter_input(INPUT_GET, 'code', FILTER_SANITIZE_SPECIAL_CHARS));
$stmt = $pdo->prepare("SELECT * FROM orders WHERE tracking_code = ?");
$stmt->execute([$code]);
$order = $stmt->fetch();
if (!$order) die("الفاتورة غير موجودة.");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><title>فاتورة #<?= $order['tracking_code'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> @media print { .no-print { display: none !important; } } </style>
</head>
<body class="bg-slate-900 text-slate-100 p-8 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full bg-slate-800 p-8 rounded-3xl border border-slate-700 shadow-2xl">
        <h1 class="text-2xl font-bold text-indigo-400 mb-2">متجر أُفُق</h1>
        <p class="text-xs text-slate-400 mb-6">فاتورة شراء رسمية مؤكدة</p>
        <div class="space-y-2 text-sm mb-6 bg-slate-900 p-4 rounded-xl">
            <div>رقم التتبع: <span class="font-mono text-indigo-300"><?= $order['tracking_code'] ?></span></div>
            <div>اسم العميل: <span class="font-bold"><?= htmlspecialchars($order['customer_name']) ?></span></div>
            <div>الإجمالي: <span class="text-green-400 font-bold">$<?= number_format($order['total_amount'], 2) ?></span></div>
        </div>
        <div class="text-center no-print flex gap-4 justify-center">
            <button onclick="window.print()" class="bg-indigo-600 px-6 py-2 rounded-xl text-sm font-bold">طباعة الفاتورة</button>
            <a href="index.php" class="bg-slate-700 px-6 py-2 rounded-xl text-sm font-bold">العودة للمتجر</a>
        </div>
    </div>
</body>
</html>