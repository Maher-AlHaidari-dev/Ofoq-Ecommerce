<?php
$request = $_SERVER['REQUEST_URI'];

// إزالة Query Parameters من الرابط
$parsedUrl = parse_url($request, PHP_URL_PATH);
$file = __DIR__ . '/..' . $parsedUrl;

// 1. إذا كان الطلب يستهدف ملفاً ثابتاً (JS, CSS, Images)
if (file_exists($file) && !is_dir($file)) {
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    if ($extension === 'js') {
        header('Content-Type: application/javascript; charset=utf-8');
        readfile($file);
        exit;
    } elseif ($extension === 'css') {
        header('Content-Type: text/css; charset=utf-8');
        readfile($file);
        exit;
    }
}

// 2. إذا كان طلب الصفحة الرئيسية
if ($parsedUrl === '/' || $parsedUrl === '' || $parsedUrl === '/index.php') {
    require __DIR__ . '/../index.php';
} else {
    // 3. معالجة طلبات الأدمن والملفات الأخرى في الجذر (Root)
    $targetFile = __DIR__ . '/..' . $parsedUrl;

    if (file_exists($targetFile) && !is_dir($targetFile)) {
        chdir(dirname($targetFile));
        require $targetFile;
    } else {
        require __DIR__ . '/../index.php';
    }
}