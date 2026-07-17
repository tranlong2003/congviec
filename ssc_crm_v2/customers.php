<?php
// customers.php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_customer') {
    $customer_id = $_POST['customer_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $source = $_POST['source'] ?? '';

    try {
        $update = $pdo->prepare("UPDATE customers SET status = ?, source = ? WHERE id = ? AND assigned_to = ?");
        $update->execute([$status, $source, $customer_id, $user_id]);
        echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật!']);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE assigned_to = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>SSC Fintech CRM - Khách hàng</title>
    <style>
        :root, [data-theme="dark"] { --bg-main: #0c0e12; --bg-card: #11141b; --border-color: #1f2633; --text-main: #f3f4f6; --text-heading: #ffffff; --accent-purple: #7c3aed; }
        [data-theme="light"] { --bg-main: #f3f4f6; --bg-card: #ffffff; --border-color: #e5e7eb; --text-main: #1f2937; --text-heading: #111827; --accent-purple: #3b82f6; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-main); padding: 30px; transition: all 0.3s; }
        .container { max-width: 1000px; margin: 0 auto; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        h2 { margin-bottom: 20px; color: var(--text-heading); border-left: 4px solid var(--accent-purple); padding-left: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-size: 11px; text-transform: uppercase; }
        select { background: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color); padding: 6px; border-radius: 6px; outline: none; }
        .btn-back { display: inline-block; margin-bottom: 15px; color: var(--text-main); text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="btn-back">&larr; Quay lại Dashboard</a>
    <h2>👥 Danh Sách Khách Hàng Được Giao</h2>
    <table>
        <thead>
            <tr>
                <th>Tên</th>
                <th>SĐT</th>
                <th>Telegram</th>
                <th>Nguồn</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $row): ?>
            <tr>
                <td style="font-weight:600; color:var(--text-heading);"><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['telegram'] ?? 'N/A') ?></td>
                <td>
                    <select onchange="updateCustomer(<?= $row['id'] ?>)" id="source-<?= $row['id'] ?>">
                        <option value="Khác" <?= $row['source']=='Khác'?'selected':'' ?>>Khác</option>
                        <option value="TikTok" <?= $row['source']=='TikTok'?'selected':'' ?>>TikTok</option>
                        <option value="Facebook" <?= $row['source']=='Facebook'?'selected':'' ?>>Facebook</option>
                        <option value="Google" <?= $row['source']=='Google'?'selected':'' ?>>Google</option>
                    </select>
                </td>
                <td>
                    <select onchange="updateCustomer(<?= $row['id'] ?>)" id="status-<?= $row['id'] ?>">
                        <option value="Chưa gọi" <?= $row['status']=='Chưa gọi'?'selected':'' ?>>Chưa gọi</option>
                        <option value="Đã gọi" <?= $row['status']=='Đã gọi'?'selected':'' ?>>Đã gọi</option>
                        <option value="Quan tâm" <?= $row['status']=='Quan tâm'?'selected':'' ?>>Quan tâm</option>
                        <option value="Đã mở MT5" <?= $row['status']=='Đã mở MT5'?'selected':'' ?>>Đã mở MT5</option>
                        <option value="Đã nạp" <?= $row['status']=='Đã nạp'?'selected':'' ?>>Đã nạp</option>
                        <option value="Khách VIP" <?= $row['status']=='Khách VIP'?'selected':'' ?>>Khách VIP</option>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
function updateCustomer(id) {
    const status = document.getElementById('status-' + id).value;
    const source = document.getElementById('source-' + id).value;
    const formData = new FormData();
    formData.append('action', 'update_customer');
    formData.append('customer_id', id);
    formData.append('status', status);
    formData.append('source', source);

    fetch('customers.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => { if(data.status !== 'success') alert(data.message); })
    .catch(err => console.error(err));
}
</script>
</body>
</html>
