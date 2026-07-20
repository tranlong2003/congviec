<?php
// config/database.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$host = '127.0.0.1';
$dbname = 'ssc_fintech_crm';
$username = 'root'; 
$password = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("<h3 style='color:red; font-family:sans-serif; padding:20px;'>❌ LỖI KẾT NỐI DATABASE: " . $e->getMessage() . "</h3>");
}
?>
