<?php
// index.php
session_start();
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
                $error = "Tài khoản hoặc mật khẩu không chính xác, hoặc đã bị khóa!";
            }
        } catch (PDOException $e) {
            $error = "Lỗi kết nối: " . $e->getMessage();
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - SSC Fintech CRM</title>
    <style>
        :root, [data-theme="dark"] {
            --bg-main: #0c0e12; --bg-card: #11141b; --border-color: #1f2633;
            --text-main: #f3f4f6; --text-heading: #ffffff; --accent-purple: #7c3aed;
        }
        [data-theme="light"] {
            --bg-main: #f3f4f6; --bg-card: #ffffff; --border-color: #e5e7eb;
            --text-main: #1f2937; --text-heading: #111827; --accent-purple: #3b82f6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-main); display: flex; align-items: center; justify-content: center; min-height: 100vh; transition: all 0.3s; }
        .login-box { width: 100%; max-width: 400px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 40px 30px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .logo { font-size: 24px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px; }
        .logo span { color: var(--accent-purple); }
        .subtitle { font-size: 13px; color: #9ca3af; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; text-align: left; display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; }
        input { background: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); padding: 12px; border-radius: 8px; font-size: 14px; outline: none; }
        input:focus { border-color: var(--accent-purple); }
        .btn-login { width: 100%; background: var(--accent-purple); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .btn-login:hover { opacity: 0.9; }
        .error-msg { color: #ef4444; font-size: 13px; margin-bottom: 15px; text-align: left; font-weight: 600; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="logo"><span>SSC</span> FINTECH CRM</div>
    <div class="subtitle">Đăng nhập để bắt đầu ca làm việc</div>
    <?php if (!empty($error)): ?>
        <div class="error-msg">⚠️ <?= $error ?></div>
    <?php endif; ?>
    <form action="index.php" method="POST">
        <div class="form-group">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" required placeholder="Tên đăng nhập...">
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn-login">Đăng Nhập 🚀</button>
    </form>
</div>
</body>
</html>
