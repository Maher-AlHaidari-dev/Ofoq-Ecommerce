<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من وجود الـ Token والجلسة
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postToken = $_POST['csrf_token'] ?? '';

    if (empty($sessionToken) || !hash_equals($sessionToken, $postToken)) {
        die("خطأ أمني");
    }

    $name  = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $desc  = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $price = floatval($_POST['price'] ?? 0);
    $cat   = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
    
    // فحص حقل الصورة من أكثر من احتمال لمنع الأخطاء
    $img   = trim($_POST['image_url'] ?? $_POST['image'] ?? '');

    if ($name && $price > 0 && $cat && $img) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $cat, $img]);
    }

    header("Location: index.php");
    exit;
}