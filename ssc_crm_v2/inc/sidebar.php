<?php
// inc/sidebar.php
$current_page = basename($_SERVER['SCRIPT_NAME']);
$base_url = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
$admin_url = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '' : 'admin/';
?>
<aside>
    <div>
        <div class="sidebar-title">Menu hệ thống</div>
        <ul class="menu-list">
            <li><a class="menu-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="<?= $base_url ?>dashboard.php">🏠 Dashboard</a></li>
            <li><a class="menu-link <?= $current_page == 'customers.php' ? 'active' : '' ?>" href="<?= $base_url ?>customers.php">👥 Khách hàng</a></li>
            <li><a class="menu-link <?= $current_page == 'submit_report.php' ? 'active' : '' ?>" href="<?= $base_url ?>submit_report.php">📝 Báo cáo ngày</a></li>
        </ul>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="sidebar-title">(Admin / Quản lý)</div>
            <ul class="menu-list">
                <li><a class="menu-link <?= $current_page == 'manage_sales.php' ? 'active' : '' ?>" href="<?= $base_url . $admin_url ?>manage_sales.php">👤 Quản lý nhân viên</a></li>
                <li><a class="menu-link <?= $current_page == 'manage_reports.php' ? 'active' : '' ?>" href="<?= $base_url . $admin_url ?>manage_reports.php">📂 Quản lý báo cáo</a></li>
                <li><a class="menu-link <?= $current_page == 'statistics.php' ? 'active' : '' ?>" href="<?= $base_url . $admin_url ?>statistics.php">📊 Thống kê chỉ số</a></li>
            </ul>
        <?php endif; ?>
    </div>
    <div class="sidebar-footer">
        <button class="theme-btn" id="themeToggle">☀️ Chuyển Theme Sáng</button>
        <a class="logout-btn" href="<?= $base_url ?>logout.php">Đăng xuất</a>
    </div>
</aside>
<main>
