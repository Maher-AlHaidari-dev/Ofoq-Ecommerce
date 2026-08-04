<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$productName = $data['product_name'] ?? '';

if (empty($productName)) {
    echo json_encode(['success' => false, 'message' => 'اسم المنتج مطلوب']);
    exit;
}

$apiKey = getenv('GEMINI_API_KEY') ?: "YOUR_GEMINI_API_KEY";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "اكتب وصفاً تسويقياً جذاباً ومختصراً (في حدود 2-3 جمل) باللغة العربية لمنتج اسمه: '{$productName}'. ركز على الفوائد والجودة.";

$postData = [
    "contents" => [["parts" => [["text" => $prompt]]]]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$description = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'تعذر توليد الوصف حالياً.';

echo json_encode(['success' => true, 'description' => trim($description)]);
?>