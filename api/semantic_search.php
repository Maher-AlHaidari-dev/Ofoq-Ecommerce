<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    // تحديد المسار المطلق لتفادي خطأ المسارات في Vercel
    require_once __DIR__ . '/../config/db.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $query = trim(filter_var($input['query'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($query)) { 
        echo json_encode(['products' => []]); 
        exit; 
    }

    $apiKey = $_ENV['GEMINI_API_KEY'] ?? $_SERVER['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? '';

    if (empty($apiKey) || $apiKey === "YOUR_GEMINI_API_KEY") {
        $stmtS = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmtS->execute(["%$query%", "%$query%"]);
        echo json_encode(['products' => $stmtS->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    $stmt = $pdo->query("SELECT id, name, description, price, category, image FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
    $prompt = "لديك منتجات: " . json_encode($products, JSON_UNESCAPED_UNICODE) . "\nالعميل يبحث عن: '{$query}'. أرجع فقط أرقام الـ ID المطابقة مفصولة بفواصل مثل: 1,2 بدون أي كلام إضافي.";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $res = curl_exec($ch); 
    curl_close($ch);

    $data = json_decode($res, true);
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    preg_match_all('/\d+/', $text, $matches);
    $ids = array_map('intval', $matches[0] ?? []);

    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $st->execute($ids);
        echo json_encode(['products' => $st->fetchAll(PDO::FETCH_ASSOC)]);
    } else {
        echo json_encode(['products' => []]);
    }

} catch (Exception $e) {
    echo json_encode(['products' => [], 'error' => $e->getMessage()]);
}
?>