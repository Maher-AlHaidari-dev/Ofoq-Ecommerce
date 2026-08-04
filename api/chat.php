<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'يرجى كتابة استفسارك.']);
    exit;
}

// ضع مفتاح Gemini الخاص بك هنا
$apiKey = getenv('GEMINI_API_KEY') ?: "YOUR_GEMINI_API_KEY";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "أنت مساعد ذكي ومبيعات لمتجر 'أُفُق' الإلكتروني. أجب على استفسار العميل التالي بلطف واحترافية وباللغة العربية:\n" . $userMessage;

$postData = [
    "contents" => [["parts" => [["text" => $prompt]]]]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // لتجاوز مشاكل شهادات SSL في البيئة المحلية

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$reply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'تعدّر الحصول على رد من الذكاء الاصطناعي حالياً.';

echo json_encode(['reply' => trim($reply)]);
?>