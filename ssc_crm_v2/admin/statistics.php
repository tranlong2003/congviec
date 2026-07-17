<?php
// admin/statistics.php
require_once '../inc/header.php';
require_once '../inc/sidebar.php';

// Tính toán tổng số liệu từ database để làm báo cáo thống kê
$total_sales = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'sale'")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(revenue) FROM daily_reports WHERE status = 'Đã duyệt'")->fetchColumn() ?? 0;
$total_lots = $pdo->query("SELECT SUM(lot_size) FROM daily_reports WHERE status = 'Đã duyệt'")->fetchColumn() ?? 0;
?>

<div class="welcome-section">
    <h1>📊 Thống Kê Chỉ Số Toàn Doanh Nghiệp</h1>
    <p>Dữ liệu tổng hợp tình hình kinh doanh thực tế.</p>
</div>

<div class="kpi-grid" style="margin-top: 20px;">
    <div class="kpi-card">
        <div class="kpi-header"><span>Tổng Nhân Sự Sale</span></div>
        <div class="kpi-value"><?= $total_sales ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span>Tổng Data Khách Hàng</span></div>
        <div class="kpi-value"><?= number_format($total_customers) ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span>Tổng Doanh Số Đã Duyệt</span></div>
        <div class="kpi-value" style="color: #f59e0b;"><?= number_format($total_revenue) ?>đ</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span>Tổng Khối Lượng Giao Dịch</span></div>
        <div class="kpi-value" style="color: #8b5cf6;"><?= number_format($total_lots, 2) ?> Lot</div>
    </div>
</div>

<div class="panel" style="margin-top: 24px;">
    <div class="panel-header">Hiệu Suất Tăng Trưởng Hệ Thống</div>
    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 20px;">Dữ liệu tự động cập nhật đồng bộ realtime từ hoạt động điểm danh và chốt deal của Sale.</p>
    <div class="chart-mock">
        <div class="chart-bar" style="height: 40px;"></div>
        <div class="chart-bar" style="height: 90px;"></div>
        <div class="chart-bar" style="height: 120px;"></div>
        <div class="chart-bar" style="height: 160px;"></div>
    </div>
</div>

<?php require_once '../inc/footer.php'; ?>
