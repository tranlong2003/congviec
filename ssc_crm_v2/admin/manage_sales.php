<?php 
require_once '../inc/header.php'; 
require_once '../inc/sidebar.php'; 
$msg = "";

$admin_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_sale') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, role, manager_id, status) VALUES (?, ?, ?, 'sale', ?, 'active')");
            $stmt->execute([$username, $password, $fullname, $admin_id]);
            $msg = "<p style='color: #10b981; font-weight:600;'>Thêm nhân viên mới thuộc nhóm bạn thành công!</p>";
        } catch (PDOException $e) {
            $msg = "<p style='color: #ef4444; font-weight:600;'>Lỗi: Tên đăng nhập đã tồn tại!</p>";
        }
}

$stmt = $pdo->prepare("
    SELECT u1.*, u2.fullname as manager_name 
    FROM users u1 
    LEFT JOIN users u2 ON u1.manager_id = u2.id 
    WHERE u1.manager_id = ? OR u1.id = ?
    ORDER BY u1.role ASC, u1.id DESC
");
$stmt->execute([$admin_id, $admin_id]);
$users = $stmt->fetchAll();
?>
<div style="display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 24px; width: 100%; align-items: start;">
    <div class="panel">
        <h2>👤 Thêm Nhân Sự Nhóm Quản Lý</h2><br>
        <?= $msg ?>
        <form action="manage_sales.php" method="POST">
            <input type="hidden" name="action" value="add_sale">
            <div class="form-group"><label>Họ và Tên</label><input type="text" name="fullname" required placeholder="Nguyễn Văn A"></div>
            <div class="form-group"><label>Tên đăng nhập</label><input type="text" name="username" required placeholder="user_sale"></div>
            <div class="form-group"><label>Mật khẩu</label><input type="text" name="password" required placeholder="••••••••"></div>
            <div class="form-group">
                <label>Admin quản lý trực tiếp</label>
                <input type="text" readonly value="Sếp: <?= htmlspecialchars($fullname) ?>" style="background: rgba(255,255,255,0.05); color: var(--text-muted);">
            </div>
            <button type="submit" class="btn-submit" style="width:100%; margin-top: 10px;">Tạo tài khoản</button>
        </form>
    </div>

    <div class="panel">
        <h2>👥 Cơ Cấu Đội Nhóm Của Bạn</h2><br>
        <table>
            <thead>
                <tr><th>Họ & Tên nhân viên</th><th>Vai trò</th><th>Người quản lý trực tiếp</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--text-heading);"><?= htmlspecialchars($row['fullname']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);">ID: @<?= htmlspecialchars($row['username']) ?></div>
                    </td>
                    <td><span style="font-weight:600; color:var(--accent-purple);"><?= strtoupper($row['role']) ?></span></td>
                    <td style="font-weight: 600; color: var(--color-revenue);">
                        <?= $row['id'] === $admin_id ? '🛡️ Trưởng nhóm (Bạn)' : '👤 ' . htmlspecialchars($row['manager_name']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../inc/footer.php'; ?>
