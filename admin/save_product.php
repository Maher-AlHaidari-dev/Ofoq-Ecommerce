<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postToken    = $_POST['csrf_token'] ?? '';

    // التحقق من الـ CSRF Token فقط إذا كان معرّفاً في الجلسة، لتجنب الإغلاق المفاجئ
    if (!empty($sessionToken) && !hash_equals($sessionToken, $postToken)) {
        die("خطأ أمني: رمز CSRF غير مطابق");
    }

    $name  = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $desc  = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $price = floatval($_POST['price'] ?? 0);
    $cat   = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));

    // استقبال رابط الصور وتنظيف المساحات الزائدة بين الفواصل
    $raw_img = $_POST['image_url'] ?? $_POST['image'] ?? '';
    $img     = implode(',', array_map('trim', explode(',', $raw_img)));

    if ($name && $price > 0 && $cat && $img) {
        // الترتيب الصحيح: name, description, price, category, image_url
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $cat, $img]);
    }

    header("Location: index.php");
    exit;
}