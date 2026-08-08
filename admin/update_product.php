<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من CSRF Token
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postToken = $_POST['csrf_token'] ?? '';

    if (empty($sessionToken) || !hash_equals($sessionToken, $postToken)) {
        die("خطأ أمني عند التحديث");
    }

    $id    = intval($_POST['id'] ?? 0);
    $name  = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $desc  = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $price = floatval($_POST['price'] ?? 0);
    $cat   = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
    $img   = trim($_POST['image_url'] ?? $_POST['image'] ?? '');

    if ($id > 0 && $name && $price > 0 && $cat && $img) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$name, $desc, $price, $cat, $img, $id]);
    }

    header("Location: index.php");
    exit;
}