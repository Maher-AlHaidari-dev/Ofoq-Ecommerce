<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'يرجى كتابة استفسارك.']);
    exit;
}

// 1. جلب قائمة المنتجات المتاحة لإعطائها كـ Context للذكاء الاصطناعي
$stmt = $pdo->query("SELECT name, price, description FROM products");
$products = $stmt->fetchAll();

$context = "قائمة المنتجات المتاحة في المتجر حالياً:\n";
foreach ($products as $p) {
    $context .= "- {$p['name']}: بسعر \${$p['price']} ({$p['description']})\n";
}

// 2. إعداد الـ Prompt
$apiKey = getenv('GEMINI_API_KEY') ?: "YOUR_GEMINI_API_KEY";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$systemPrompt = "أنت مساعد ذكي لمتجر 'أُفُق'. استعن بالمعلومات التالية للرد على العميل باللغة العربية بأسلوب احترافي:\n" . $context;

$postData = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemPrompt . "\nسؤال العميل: " . $userMessage]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$aiReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? "عذراً، حدث خطأ أثناء معالجة الطلب.";

echo json_encode(['reply' => $aiReply]);
?>