<?php require_once __DIR__ . '/../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("خطأ أمني CSRF.");
    }

    $id = intval($_POST['id']);

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: index.php");
    exit;
}
?>