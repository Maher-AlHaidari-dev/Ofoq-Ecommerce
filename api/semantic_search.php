<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$query = trim(filter_var($input['query'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS));

if (empty($query)) { echo json_encode(['products' => []]); exit; }

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

$apiKey = getenv('GEMINI_API_KEY') ?: "YOUR_GEMINI_API_KEY";
if ($apiKey === "YOUR_GEMINI_API_KEY") {
    $stmtS = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmtS->execute(["%$query%", "%$query%"]);
    echo json_encode(['products' => $stmtS->fetchAll()]);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
$prompt = "لديك منتجات: " . json_encode($products, JSON_UNESCAPED_UNICODE) . "\nالعميل يبحث عن: '{$query}'. أرجع فقط أرقام الـ ID مطابقة مفصولة بفواصل (مثال: 1,2).";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json']
]);
$res = curl_exec($ch); curl_close($ch);
$data = json_decode($res, true);
$text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
preg_match_all('/\d+/', $text, $matches);
$ids = array_map('intval', $matches[0] ?? []);

if (!empty($ids)) {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
    $st->execute($ids);
    echo json_encode(['products' => $st->fetchAll()]);
} else {
    echo json_encode(['products' => []]);
}
?>