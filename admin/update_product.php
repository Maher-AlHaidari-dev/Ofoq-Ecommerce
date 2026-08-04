<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("خطأ أمني CSRF.");
    }

    $id    = intval($_POST['id']);
    $name  = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $desc  = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $price = floatval($_POST['price']);
    $cat   = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
    $img   = trim(filter_input(INPUT_POST, 'image_url', FILTER_VALIDATE_URL));

    if ($id > 0 && $name && $price > 0 && $cat && $img) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$name, $desc, $price, $cat, $img, $id]);
    }

    header("Location: index.php");
    exit;
}
?>