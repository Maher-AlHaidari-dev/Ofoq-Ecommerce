<?php
$request = $_SERVER['REQUEST_URI'];
// إزالة أي Query Parameters من الرابط
$parsedUrl = parse_url($request, PHP_URL_PATH);
$file = __DIR__ . '/..' . $parsedUrl;

// 1. إذا كان الطلب يستهدف ملف ثابت موجود فعلياً (JS, CSS, Images)
if (file_exists($file) && !is_dir($file)) {
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    
    // ضبط الميم تايب الصحيح لكي يقبله المتصفح كـ JavaScript أو CSS
    if ($extension === 'js') {
        header('Content-Type: application/javascript; charset=utf-8');
    } elseif ($extension === 'css') {
        header('Content-Type: text/css; charset=utf-8');
    }
    
    readfile($file);
    exit;
}

// 2. إذا كان طلب صفحة رئيسية أو مسار غير معروف
if ($parsedUrl == '/' || $parsedUrl == '' || $parsedUrl == '/index.php') {
    require __DIR__ . '/../index.php';
} else {
    // 3. مسارات الـ API أو الأسطر الأخرى
    $apiFile = __DIR__ . $parsedUrl;
    if (file_exists($apiFile) && !is_dir($apiFile)) {
        require $apiFile;
    } else {
        require __DIR__ . '/../index.php';
    }
}