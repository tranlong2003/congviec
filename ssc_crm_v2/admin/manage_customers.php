<?php 
require_once '../inc/header.php'; 
require_once '../inc/sidebar.php'; 

$admin_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.*, u.fullname as sale_name 
    FROM customers c 
    INNER JOIN users u ON c.assigned_to = u.id 
    WHERE u.manager_id = ?
    ORDER BY c.id DESC
");
$stmt->execute([$admin_id]);
$all_customers = $stmt->fetchAll();
?>
<div class="container" style="max-width: 100%;">
    <h2>🗂️ Quản Lý Khách Hàng Thuộc Đội Nhóm</h2>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Hệ thống tự động đồng bộ thời gian thực thông tin do Sale nhóm bạn trực tiếp cập nhật.</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Khách Hàng</th>
                <th>Thông tin liên hệ</th>
                <th>Hồ sơ khai thác</th>
                <th>Nguồn khách</th>
                <th>Sale Phụ Trách</th>
                <th>Trạng Thái</th>
                <th>Ghi chú làm việc mới nhất</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($all_customers) === 0): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Chưa có nhân viên nào trong đội nhóm của bạn nhập khách hàng.</td></tr>
            <?php else: ?>
                <?php foreach ($all_customers as $row): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td style="font-weight: 600; color: var(--text-heading);"><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <div>📞 <?= htmlspecialchars($row['phone']) ?></div>
                        <div style="font-size: 12px; color: #3b82f6;">✈️ <?= htmlspecialchars($row['telegram'] ?? 'N/A') ?></div>
                    </td>
                    <td>
                        <div style="font-size: 13px;">🎂 Tuổi: <span style="color:var(--text-heading); font-weight:600;"><?= $row['age'] ?? 'Chưa rõ' ?></span></div>
                        <div style="font-size: 13px; margin-top:4px;">📍 Quê: <span style="color:var(--text-heading); font-weight:600;"><?= htmlspecialchars($row['hometown'] ?? 'Chưa rõ') ?></span></div>
                    </td>
                    <td><span style="background: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 4px; font-size:12px;"><?= htmlspecialchars($row['source']) ?></span></td>
                    <td style="font-weight: 600; color: var(--accent-purple);">👤 <?= htmlspecialchars($row['sale_name']) ?></td>
                    <td><span style="font-weight: 600; color: var(--color-data);"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td style="font-size: 13px; color: var(--text-muted); font-style: italic;"><?= htmlspecialchars($row['note'] ?? 'Chưa có ghi chú...') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once '../inc/footer.php'; ?>
