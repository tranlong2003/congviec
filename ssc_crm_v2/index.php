<?php
// index.php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!file_exists('config/database.php')) {
    die("<h3 style='color:red; font-family:sans-serif; padding:20px;'>❌ LỖI HỆ THỐNG: Không tìm thấy file 'config/database.php'. Vui lòng kiểm tra lại cấu trúc thư mục!</h3>");
}

require_once 'config/database.php';
$error = "";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Tài khoản hoặc mật khẩu không chính xác!";
            }
        } catch (PDOException $e) {
            $error = "Lỗi database khi đăng nhập: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - SSC Fintech CRM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: #0c0e12; color: #f3f4f6; font-family: sans-serif; }
        .login-box { width: 100%; max-width: 400px; background: #11141b; border: 1px solid #1f2633; border-radius: 12px; padding: 40px 30px; text-align: center; box-shadow: 0 4px 20px rgba(124, 58, 237, 0.15); }
        .logo { font-size: 24px; font-weight: 800; margin-bottom: 8px; color: #ffffff; }
        .logo span { color: #7c3aed; text-shadow: 0 0 8px #7c3aed; }
        .error-msg { color: #ef4444; font-size: 13px; margin-bottom: 15px; text-align: left; font-weight: 600; }
        .form-group { margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px; }
        input { background: #0c0e12; color: #f3f4f6; border: 1px solid #1f2633; padding: 12px; border-radius: 8px; outline: none; }
        input:focus { border-color: #7c3aed; }
        .btn-submit { width: 100%; background: #7c3aed; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="logo"><span>SSC</span> FINTECH CRM</div>
    <div style="font-size: 13px; color: #9ca3af; margin-bottom: 30px;">Hệ thống CRM tài chính số 1 Việt Nam</div>
    
    <?php if (!empty($error)): ?>
        <div class="error-msg">⚠️ <?= $error ?></div>
    <?php endif; ?>
    
    <form action="index.php" method="POST">
        <div class="form-group">
            <label style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; text-align: left;">Tên đăng nhập</label>
            <input type="text" name="username" required placeholder="Tên đăng nhập...">
        </div>
        <div class="form-group">
            <label style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; text-align: left;">Mật khẩu</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn-submit">Đăng Nhập 🚀</button>
    </form>
</div>
</body>
</html>
