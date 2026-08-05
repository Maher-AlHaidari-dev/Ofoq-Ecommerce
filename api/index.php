<?php
// استدعاء الملف المطلوب بناءً على الرابط
$request = $_SERVER['REQUEST_URI'];

if ($request == '/' || $request == '') {
    require __DIR__ . '/../index.php';
} else {
    $file = __DIR__ . '/..' . strtok($request, '?');
    if (file_exists($file) && !is_dir($file)) {
        require $file;
    } else {
        require __DIR__ . '/../index.php';
    }
}