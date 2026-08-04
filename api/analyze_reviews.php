<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$reviews = $data['reviews'] ?? [];

if (empty($reviews)) {
    echo json_encode(['summary' => 'لا توجد تقييمات لتحليلها.']);
    exit;
}

$apiKey = getenv('GEMINI_API_KEY') ?: "YOUR_GEMINI_API_KEY";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$reviewsText = implode("\n- ", $reviews);
$prompt = "قم بتحليل تقييمات المراجعات التالية للعملاء:\n- {$reviewsText}\n\nقدم ملخصاً إيجازياً في سطرين عن مستوى رضا العملاء وأهم نقاط القوة أو الضعف المذكورة.";

$postData = ["contents" => [["parts" => [["text" => $prompt]]]]];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$summary = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'تعذر تحليل الآراء.';

echo json_encode(['summary' => trim($summary)]);
?>