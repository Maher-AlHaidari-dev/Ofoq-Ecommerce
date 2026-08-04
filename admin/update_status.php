<?php
require_once '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'shipped', 'delivered'])) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
    header("Location: index.php");
    exit;
}
?>