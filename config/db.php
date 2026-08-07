<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// 
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}

// قراءة متغيّرات البيئة دون وضع أي بيانات سحابية صريحة في الكود
$host   = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$port   = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'defaultdb';
$user   = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
$pass   = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    // تعديل هذا السطر ليطبع الخطأ الحقيقي على الشاشة مباشرة
    die("خطأ الاتصال الفعلي: " . $e->getMessage());
}
?>