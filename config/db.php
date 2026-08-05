<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// تحميل مكتبة Dotenv تلقائياً في حال وجودها
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}

// قراءة بيانات الاتصال من ملف .env أو متغيّرات البيئة
$host   = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'mysql-2a1d3b5d-mahersport968-5537.a.aivencloud.com';
$port   = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '27602';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'defaultdb';
$user   = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'avnadmin';
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
    error_log("Database Connection Error: " . $e->getMessage());
    die("خطأ في الاتصال بقاعدة البيانات.");
}
?>