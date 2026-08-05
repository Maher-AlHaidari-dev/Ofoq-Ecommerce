<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../config/db.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $query = trim(filter_var($input['query'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($query)) { 
        echo json_encode(['products' => []]); 
        exit; 
    }

    $apiKey = $_ENV['GEMINI_API_KEY'] ?? $_SERVER['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? '';

    // جلب جميع منتجات الجدول لتفادي خطأ أسماء الأعمدة غير الموجودة
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        echo json_encode(['products' => []]);
        exit;
    }

    // إذا لم يتوفر مفتاح API نستخدم البحث التقليدي
    if (empty($apiKey) || $apiKey === "YOUR_GEMINI_API_KEY") {
        $stmtS = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmtS->execute(["%$query%", "%$query%"]);
        echo json_encode(['products' => $stmtS->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
    $prompt = "لدينا قائمة بالمنتجات التالية بصيغة JSON:\n" . json_encode($products, JSON_UNESCAPED_UNICODE) . "\n\nالمستخدم يبحث عن: '{$query}'.\nاختر المنتجات المناسبة وأرجع أرقام الـ ID الخاصة بها فقط مفصولة بأرقام مثل: 1, 2, 3. إذا لم تجد أي منتج أرجع الرقم 0 فقط.";

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
    $ids = array_filter(array_map('intval', $matches[0] ?? []), function($id) {
        return $id > 0;
    });

    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $st->execute(array_values($ids));
        echo json_encode(['products' => $st->fetchAll(PDO::FETCH_ASSOC)]);
    } else {
        // Fallback للبحث العادي إذا لم يُرجع AI ناتجة
        $stmtS = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmtS->execute(["%$query%", "%$query%"]);
        echo json_encode(['products' => $stmtS->fetchAll(PDO::FETCH_ASSOC)]);
    }

} catch (Exception $e) {
    echo json_encode(['products' => [], 'error' => $e->getMessage()]);
}
?>