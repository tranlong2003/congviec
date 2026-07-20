<?php 
require_once '../inc/header.php'; 
require_once '../inc/sidebar.php'; 

$admin_id = $_SESSION['user_id'];

if (isset($_GET['approve_id'])) {
    $report_id = intval($_GET['approve_id']);
    try {
        $stmt = $pdo->prepare("
            UPDATE daily_reports r 
            INNER JOIN users u ON r.user_id = u.id 
            SET r.status = 'Đã duyệt', r.reject_reason = NULL 
            WHERE r.id = ? AND u.manager_id = ?
        ");
        $stmt->execute([$report_id, $admin_id]);
        echo "<script>location.href='manage_reports.php';</script>";
        exit;
    } catch (PDOException $e) { die($e->getMessage()); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject_report') {
    $report_id = intval($_POST['report_id'] ?? 0);
    $reason = trim($_POST['reject_reason'] ?? 'Số liệu không khớp hoặc thiếu minh bạch!');

    if ($report_id > 0) {
        try {
            $stmt = $pdo->prepare("
                UPDATE daily_reports r 
                INNER JOIN users u ON r.user_id = u.id 
                SET r.status = 'Từ chối', r.reject_reason = ? 
                WHERE r.id = ? AND u.manager_id = ?
            ");
            $stmt->execute([$reason, $report_id, $admin_id]);
            echo "<script>location.href='manage_reports.php';</script>";
            exit;
        } catch (PDOException $e) { die($e->getMessage()); }
    }
}

$stmt = $pdo->prepare("
    SELECT r.*, u.fullname 
    FROM daily_reports r 
    INNER JOIN users u ON r.user_id = u.id 
    WHERE u.manager_id = ?
    ORDER BY r.report_date DESC
");
$stmt->execute([$admin_id]);
$reports = $stmt->fetchAll();
?>
<div class="container" style="max-width: 100%;">
    <h2>📂 Duyệt Báo Cáo Doanh Số Cuối Ngày</h2>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Duyệt số liệu hiệu suất nhóm nhân sự dưới quyền trực tiếp.</p>
    <table>
        <thead>
            <tr>
                <th>Ngày</th><th>Nhân viên</th><th>Data</th><th>Đã gọi</th><th>Quan tâm</th><th>MT5</th><th>FTD</th><th>Lot</th><th>Doanh Số</th><th>Trạng thái</th><th>Lý do từ chối</th><th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($reports) === 0): ?>
                <tr><td colspan="12" style="text-align: center; color: var(--text-muted); padding: 30px;">Nhóm của bạn chưa có báo cáo nào được gửi.</td></tr>
            <?php else: ?>
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
                    <td style="font-weight:600; color:var(--color-revenue)"><?= number_format($row['revenue']) ?>đ</td>
                    
                    <td style="font-weight:600; color:<?= $row['status']=='Đã duyệt'?'#10b981':($row['status']=='Từ chối'?'#ef4444':'#f59e0b') ?>">
                        <?= $row['status'] ?>
                    </td>
                    
                    <td style="font-size:12px; color:var(--text-muted); font-style:italic;">
                        <?= htmlspecialchars($row['reject_reason'] ?? '---') ?>
                    </td>
                    
                    <td>
                        <?php if ($row['status'] === 'Chờ duyệt'): ?>
                            <a href="manage_reports.php?approve_id=<?= $row['id'] ?>" class="btn-approve" style="display:inline-block; margin-bottom:4px;">✓ Duyệt</a>
                            <button onclick="openRejectModal(<?= $row['id'] ?>)" class="btn-reject" style="display:inline-block;">✗ Từ Chối</button>
                        <?php elseif ($row['status'] === 'Đã duyệt'): ?>
                            <span style="color: #6b7280; font-size:12px; font-weight:600;">✓ Đã Duyệt</span>
                        <?php else: ?>
                            <span style="color: #ef4444; font-size:12px; font-weight:600;">❌ Đã Từ Chối</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="rejectModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); align-items: center; justify-content: center;">
    <div class="panel" style="width:100%; max-width:400px; background: var(--bg-card); border-color: #ef4444;">
        <h3 style="color:#ef4444; margin-bottom:15px;">❌ Lý Do Từ Chối Báo Cáo</h3>
        <form action="manage_reports.php" method="POST">
            <input type="hidden" name="action" value="reject_report">
            <input type="hidden" name="report_id" id="modalReportId">
            <div class="form-group">
                <label style="font-size:12px; color:var(--text-muted);">Nhập lý do gửi trả báo cáo:</label>
                <input type="text" name="reject_reason" required placeholder="Ví dụ: Sai số doanh số thực tế..." style="width:100%;">
            </div>
            <div style="display:flex; gap:10px; margin-top:15px;">
                <button type="submit" class="btn-reject" style="flex:1;">Gửi Yêu Cầu Sửa</button>
                <button type="button" onclick="closeRejectModal()" class="theme-btn" style="flex:1; background:rgba(255,255,255,0.05); color:var(--text-main);">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(reportId) {
    document.getElementById('modalReportId').value = reportId;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
<?php require_once '../inc/footer.php'; ?>
