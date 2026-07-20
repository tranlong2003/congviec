<?php 
require_once 'inc/header.php'; 
require_once 'inc/sidebar.php'; 

date_default_timezone_set('Asia/Ho_Chi_Minh');

$msg = "";
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM daily_reports WHERE user_id = ? AND report_date = ?");
$stmt->execute([$user_id, $today]);
$existing_report = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allocated_data = intval($_POST['allocated_data'] ?? 0);
    $calls_made = intval($_POST['calls_made'] ?? 0);
    $interested = intval($_POST['interested_customers'] ?? 0);
    $mt5 = intval($_POST['opened_mt5'] ?? 0);
    $ftd = intval($_POST['ftd_count'] ?? 0);
    $lot = floatval($_POST['lot_size'] ?? 0);
    $revenue = floatval($_POST['revenue'] ?? 0);

    if (!$existing_report || $existing_report['status'] === 'Từ chối') {
        try {
            $sql = "INSERT INTO daily_reports (user_id, report_date, allocated_data, calls_made, interested_customers, opened_mt5, ftd_count, lot_size, revenue, status, reject_reason) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Chờ duyệt', NULL) 
                    ON DUPLICATE KEY UPDATE 
                        allocated_data = VALUES(allocated_data), 
                        calls_made = VALUES(calls_made), 
                        interested_customers = VALUES(interested_customers), 
                        opened_mt5 = VALUES(opened_mt5), 
                        ftd_count = VALUES(ftd_count), 
                        lot_size = VALUES(lot_size), 
                        revenue = VALUES(revenue), 
                        status = 'Chờ duyệt',
                        reject_reason = NULL";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $today, $allocated_data, $calls_made, $interested, $mt5, $ftd, $lot, $revenue]);
            $msg = "<p style='color: #10b981; font-weight: 600; margin-bottom:15px;'>Báo cáo ngày đã được gửi lại cho sếp duyệt!</p>";
            
            $stmt_refresh = $pdo->prepare("SELECT * FROM daily_reports WHERE user_id = ? AND report_date = ?");
            $stmt_refresh->execute([$user_id, $today]);
            $existing_report = $stmt_refresh->fetch();
        } catch (PDOException $e) {
            $msg = "<p style='color: #ef4444; font-weight: 600; margin-bottom:15px;'>Lỗi: " . $e->getMessage() . "</p>";
        }
    }
}
?>
<div class="form-box">
    <h2>📝 Gửi báo cáo doanh số ngày</h2>
    <p style="font-size:12px; color:#9ca3af; margin-bottom:20px;">Thông số được đối soát tự động cuối ngày làm việc.</p>
    <?= $msg ?>

    <?php if ($existing_report && $existing_report['status'] === 'Đã duyệt'): ?>
        <div style="background: rgba(16,185,129,0.1); border: 1px solid #10b981; padding: 15px; border-radius: 8px; color: #10b981; font-weight: 600; text-align: center;">
            ✓ Sếp đã duyệt báo cáo ngày hôm nay. Form đã đóng băng!
        </div>
    <?php elseif ($existing_report && $existing_report['status'] === 'Chờ duyệt'): ?>
        <div style="background: rgba(245,158,11,0.1); border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; color: #f59e0b; font-weight: 600; text-align: center;">
            🕒 Báo cáo đang chờ duyệt. Bạn không thể chỉnh sửa lúc này!
        </div>
    <?php else: ?>
        <?php if ($existing_report && $existing_report['status'] === 'Từ chối'): ?>
            <div style="background: rgba(239,68,68,0.1); border: 1px solid #ef4444; padding: 15px; border-radius: 8px; color: #ef4444; font-weight: 600; margin-bottom: 20px;">
                ❌ BÁO CÁO BỊ TỪ CHỐI!<br>
                <span style="font-size:13px; font-weight:normal; color: var(--text-main);">Lý do của sếp: "<?= htmlspecialchars($existing_report['reject_reason']) ?>"</span><br>
                <span style="font-size:12px; font-weight:normal; color: var(--text-muted);">-> Vui lòng nhập lại số liệu chính xác dưới đây!</span>
            </div>
        <?php endif; ?>

        <form action="submit_report.php" method="POST">
            <div class="form-group">
                <label>Data được giao</label>
                <input type="number" name="allocated_data" required value="<?= $existing_report ? $existing_report['allocated_data'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Cuộc gọi đã thực hiện</label>
                <input type="number" name="calls_made" required value="<?= $existing_report ? $existing_report['calls_made'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Khách hàng quan tâm</label>
                <input type="number" name="interested_customers" required value="<?= $existing_report ? $existing_report['interested_customers'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Tài khoản MT5 mới</label>
                <input type="number" name="opened_mt5" required value="<?= $existing_report ? $existing_report['opened_mt5'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Khách nạp đầu (FTD)</label>
                <input type="number" name="ftd_count" required value="<?= $existing_report ? $existing_report['ftd_count'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Tổng số Lot giao dịch</label>
                <input type="number" step="0.01" name="lot_size" required value="<?= $existing_report ? $existing_report['lot_size'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Tổng doanh thu (VND)</label>
                <input type="number" name="revenue" required value="<?= $existing_report ? $existing_report['revenue'] : '' ?>">
            </div>
            <button type="submit" class="btn-submit" style="width: 100%;">🚀 Gửi Báo Cáo</button>
        </form>
    <?php endif; ?>
</div>
<?php require_once 'inc/footer.php'; ?>
