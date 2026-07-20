<?php
// inc/header.php
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false ? '../index.php' : 'index.php'));
    exit;
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];
$base_url = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';

$alert_msg = "";
if ($role === 'sale') {
    $stmt = $pdo->prepare("SELECT * FROM daily_reports WHERE user_id = ? AND status = 'Từ chối' LIMIT 1");
    $stmt->execute([$user_id]);
    $rejected_report = $stmt->fetch();
    if ($rejected_report) {
        $alert_msg = "⚠️ CẢNH BÁO: Báo cáo ngày " . date('d/m/Y', strtotime($rejected_report['report_date'])) . " của bạn BỊ TỪ CHỐI! Lý do của sếp: \"" . htmlspecialchars($rejected_report['reject_reason']) . "\". Vui lòng kiểm tra lại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSC Fintech CRM</title>
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">
</head>
<body>
    <?php if (!empty($alert_msg)): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                alert(<?= json_encode($alert_msg) ?>);
            });
        </script>
    <?php endif; ?>
    <header>
        <div class="logo"><span>SSC</span> FINTECH CRM</div>
        <div class="header-actions">
            <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">
                Xin chào, <span style="color: var(--text-heading);"><?= htmlspecialchars($fullname) ?></span> (<?= strtoupper($role) ?>)
            </span>
        </div>
    </header>
    <div class="wrapper">
