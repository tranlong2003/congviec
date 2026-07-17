<?php
// inc/header.php
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
$today = date('Y-m-d');

// Lấy thông tin điểm danh ngày hôm nay
$stmt = $pdo->prepare("SELECT * FROM attendances WHERE user_id = ? AND attendance_date = ?");
$stmt->execute([$user_id, $today]);
$attendance = $stmt->fetch();

$attendance_status = "📍 CHƯA ĐIỂM DANH HÔM NAY";
$attendance_color = "#ef4444"; 
if ($attendance) {
    if ($attendance['check_out'] !== null) {
        $attendance_status = "📍 ĐÃ CHECK-OUT: " . $attendance['check_out'];
        $attendance_color = "#3b82f6"; 
    } else {
        $attendance_status = "📍 ĐÃ CHECK-IN: " . $attendance['check_in'];
        $attendance_color = "#10b981"; 
    }
}

// Xác định đường dẫn tương đối tùy theo vị trí file đang chạy
$base_url = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
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
    <header>
        <div class="logo"><span>SSC</span> FINTECH CRM</div>
        <div class="header-actions">
            <span class="attendance-status-badge" style="background-color: <?= $attendance_color ?>;" id="attendanceBadge"><?= $attendance_status ?></span>
            <button class="btn-attendance btn-checkin" onclick="submitAttendance('check_in')">📍 Check-In</button>
            <button class="btn-attendance btn-checkout" onclick="submitAttendance('check_out')">🕒 Check-Out</button>
        </div>
    </header>
    <div class="wrapper">
