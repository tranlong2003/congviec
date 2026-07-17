<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập trang này!");
}

if (isset($_GET['approve_id'])) {
    $report_id = intval($_GET['approve_id']);
    try {
        $stmt = $pdo->prepare("UPDATE daily_reports SET status = 'Đã duyệt' WHERE id = ?");
        $stmt->execute([$report_id]);
        header("Location: manage_reports.php");
        exit;
    } catch (PDOException $e) {
        die("Lỗi duyệt báo cáo: " . $e->getMessage());
    }
}

$reports = $pdo->query("SELECT r.*, u.fullname FROM daily_reports r JOIN users u ON r.user_id = u.id ORDER BY r.report_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Duyệt báo cáo - SSC Fintech CRM</title>
    <style>
        :root, [data-theme="dark"] { --bg-main: #0c0e12; --bg-card: #11141b; --border-color: #1f2633; --text-main: #f3f4f6; --text-heading: #ffffff; --accent-purple: #7c3aed; }
        [data-theme="light"] { --bg-main: #f3f4f6; --bg-card: #ffffff; --border-color: #e5e7eb; --text-main: #1f2937; --text-heading: #111827; --accent-purple: #3b82f6; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-main); padding: 30px; transition: all 0.3s; }
        .container { max-width: 1200px; margin: 0 auto; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        h2 { margin-bottom: 20px; color: var(--text-heading); border-left: 4px solid var(--accent-purple); padding-left: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-size: 11px; text-transform: uppercase; }
        .btn-approve { background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; }
        .btn-back { display: inline-block; margin-bottom: 15px; color: var(--text-main); text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <a href="../dashboard.php" class="btn-back">&larr; Quay lại Dashboard</a>
    <h2>📂 Duyệt Báo Cáo Cuối Ngày</h2>
    <table>
        <thead>
            <tr><th>Ngày</th><th>Nhân viên</th><th>Data</th><th>Đã gọi</th><th>Quan tâm</th><th>MT5</th><th>FTD</th><th>Lot</th><th>Doanh Số</th><th>Trạng thái</th><th>Thao tác</th></tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $row): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($row['report_date'])) ?></td>
                <td style="font-weight: 600; color: var(--text-heading);"><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= $row['allocated_data'] ?></td>
                <td><?= $row['calls_made'] ?></td>
                <td><?= $row['interested_customers'] ?></td>
                <td><?= $row['opened_mt5'] ?></td>
                <td><?= $row['ftd_count'] ?></td>
                <td><?= $row['lot_size'] ?></td>
                <td style="font-weight: 600; color: #f59e0b;"><?= number_format($row['revenue']) ?>đ</td>
                <td style="font-weight: 600; color: <?= $row['status'] === 'Đã duyệt' ? '#10b981' : '#f59e0b' ?>;"><?= $row['status'] ?></td>
                <td>
                    <?php if ($row['status'] === 'Chờ duyệt'): ?>
                        <a href="manage_reports.php?approve_id=<?= $row['id'] ?>" class="btn-approve">✓ Duyệt</a>
                    <?php else: ?>
                        <span style="color: #6b7280; font-size:12px;">Đã duyệt</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
