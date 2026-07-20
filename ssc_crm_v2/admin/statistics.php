<?php 
require_once '../inc/header.php'; 
require_once '../inc/sidebar.php'; 

$admin_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'sale' AND manager_id = ?");
$stmt->execute([$admin_id]);
$total_sales = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM customers c 
    INNER JOIN users u ON c.assigned_to = u.id 
    WHERE u.manager_id = ?
");
$stmt->execute([$admin_id]);
$total_customers = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT SUM(r.revenue) FROM daily_reports r 
    INNER JOIN users u ON r.user_id = u.id 
    WHERE u.manager_id = ? AND r.status = 'Đã duyệt'
");
$stmt->execute([$admin_id]);
$total_revenue = $stmt->fetchColumn() ?? 0;

$stmt = $pdo->prepare("
    SELECT SUM(r.lot_size) FROM daily_reports r 
    INNER JOIN users u ON r.user_id = u.id 
    WHERE u.manager_id = ? AND r.status = 'Đã duyệt'
");
$stmt->execute([$admin_id]);
$total_lots = $stmt->fetchColumn() ?? 0;
?>
<div class="welcome-section">
    <h1>📊 Thống Kê Chỉ Số Nhóm Của Bạn</h1>
    <p>Dữ liệu doanh số và hoạt động của các thành viên trực thuộc quản lý.</p>
</div>
<div class="kpi-grid" style="margin-top: 20px;">
    <div class="kpi-card"><div class="kpi-header"><span>Nhân Sự Quản Lý</span></div><div class="kpi-value"><?= $total_sales ?> Sale</div></div>
    <div class="kpi-card"><div class="kpi-header"><span>Tổng Khách Hàng Nhóm</span></div><div class="kpi-value"><?= number_format($total_customers) ?></div></div>
    <div class="kpi-card"><div class="kpi-header"><span>Doanh Số Nhóm Đã Duyệt</span></div><div class="kpi-value" style="color:#f59e0b;"><?= number_format($total_revenue) ?>đ</div></div>
    <div class="kpi-card"><div class="kpi-header"><span>Tổng Khối Lượng Chốt</span></div><div class="kpi-value" style="color:#8b5cf6;"><?= number_format($total_lots, 2) ?> Lot</div></div>
</div>
<div class="panel" style="margin-top: 24px;">
    <div class="panel-header">Hiệu Suất Phát Triển Đội Nhóm</div>
    <div class="chart-mock" style="margin-top:20px;">
        <div class="chart-bar" style="height: 40px;"></div>
        <div class="chart-bar" style="height: 90px;"></div>
        <div class="chart-bar" style="height: 120px;"></div>
        <div class="chart-bar" style="height: 160px;"></div>
    </div>
</div>
<?php require_once '../inc/footer.php'; ?>
