<?php require_once __DIR__ . '/../config/db.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("خطأ أمني.");
    }
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $desc = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $price = floatval($_POST['price']);
    $cat = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
    $img = trim(filter_input(INPUT_POST, 'image_url', FILTER_VALIDATE_URL));

    if ($name && $price > 0 && $cat && $img) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $cat, $img]);
    }
    header("Location: index.php");
    exit;
}
?>