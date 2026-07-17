<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập trang này!");
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_sale') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $role = $_POST['role'] ?? 'sale';

    if (!empty($username) && !empty($password) && !empty($fullname)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $fullname, $role]);
            $msg = "<p style='color: #10b981; font-weight:600;'>Thêm tài khoản thành công!</p>";
        } catch (PDOException $e) {
            $msg = "<p style='color: #ef4444; font-weight:600;'>Lỗi: Tên đăng nhập đã tồn tại!</p>";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY role ASC, id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Quản lý nhân viên - SSC Fintech CRM</title>
    <style>
        :root, [data-theme="dark"] { --bg-main: #0c0e12; --bg-card: #11141b; --border-color: #1f2633; --text-main: #f3f4f6; --text-heading: #ffffff; --accent-purple: #7c3aed; }
        [data-theme="light"] { --bg-main: #f3f4f6; --bg-card: #ffffff; --border-color: #e5e7eb; --text-main: #1f2937; --text-heading: #111827; --accent-purple: #3b82f6; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-main); padding: 30px; transition: all 0.3s; }
        .container { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 20px; }
        .panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        h2 { margin-bottom: 20px; color: var(--text-heading); border-left: 4px solid var(--accent-purple); padding-left: 10px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; }
        input, select { background: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; outline: none; }
        input:focus, select:focus { border-color: var(--accent-purple); }
        .btn-submit { background: var(--accent-purple); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: #9ca3af; font-size: 11px; text-transform: uppercase; }
        .btn-back { display: inline-block; margin-bottom: 15px; color: var(--text-main); text-decoration: none; }
    </style>
</head>
<body>
<a href="../dashboard.php" class="btn-back">&larr; Quay lại Dashboard</a>
<div class="container">
    <div class="panel">
        <h2>👤 Thêm Nhân Viên Mới</h2>
        <?= $msg ?>
        <form action="manage_sales.php" method="POST">
            <input type="hidden" name="action" value="add_sale">
            <div class="form-group"><label>Họ và Tên</label><input type="text" name="fullname" required></div>
            <div class="form-group"><label>Tên đăng nhập</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Mật khẩu</label><input type="text" name="password" required></div>
            <div class="form-group"><label>Phân Quyền</label>
                <select name="role">
                    <option value="sale">Nhân viên (Sale)</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Tạo tài khoản</button>
        </form>
    </div>
    <div class="panel">
        <h2>👥 Danh Sách Nhân Sự</h2>
        <table>
            <thead><tr><th>Họ và Tên</th><th>Username</th><th>Chức vụ</th></tr></thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                <tr><td style="font-weight: 600; color: var(--text-heading);"><?= htmlspecialchars($row['fullname']) ?></td><td><?= htmlspecialchars($row['username']) ?></td><td style="font-weight: 600; color: var(--accent-purple);"><?= strtoupper($row['role']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
