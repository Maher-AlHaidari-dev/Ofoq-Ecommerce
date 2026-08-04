<?php
require_once 'config/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) { die("خطأ أمني CSRF."); }

$name    = trim(filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_SPECIAL_CHARS));
$phone   = trim(filter_input(INPUT_POST, 'customer_phone', FILTER_SANITIZE_SPECIAL_CHARS));
$address = trim(filter_input(INPUT_POST, 'shipping_address', FILTER_SANITIZE_SPECIAL_CHARS));
$cart    = json_decode($_POST['cart_data'] ?? '[]', true);

if (!$name || !$phone || !$address || empty($cart)) { die("بيانات غير صالحة"); }

$total = 0;
foreach ($cart as $item) { $total += floatval($item['price']); }

$tracking = 'OFQ-' . strtoupper(bin2hex(random_bytes(3)));
$stmt = $pdo->prepare("INSERT INTO orders (tracking_code, customer_name, customer_phone, shipping_address, total_amount) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$tracking, $name, $phone, $address, $total]);

header("Location: print_invoice.php?code=" . urlencode($tracking));
exit;
?>