<?php
// attendance.php
session_start();
require_once 'config/database.php';

// ÉP HỆ THỐNG SỬ DỤNG MÚI GIỜ VIỆT NAM
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
    exit;
}
$user_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $method = $_POST['method'] ?? 'Wifi Văn Phòng';
    $today = date('Y-m-d');
    $now = date('H:i:s'); // Giờ chuẩn Việt Nam sau khi đặt timezone
    $ip_address = $_SERVER['REMOTE_ADDR'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM attendances WHERE user_id = ? AND attendance_date = ?");
        $stmt->execute([$user_id, $today]);
        $attendance = $stmt->fetch();

        if ($action === 'check_in') {
            if ($attendance) {
                echo json_encode(['status' => 'error', 'message' => 'Bạn đã check-in ngày hôm nay rồi!']);
                exit;
            }
            $late_time = "08:30:00";
            $status = ($now > $late_time) ? 'Đi muộn' : 'Đi làm';
            $insert = $pdo->prepare("INSERT INTO attendances (user_id, attendance_date, check_in, status, method, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$user_id, $today, $now, $status, $method, $ip_address]);
            echo json_encode(['status' => 'success', 'message' => 'Check-in bằng [' . $method . '] thành công lúc ' . $now]);
            exit;
        } elseif ($action === 'check_out') {
            if (!$attendance || $attendance['check_out'] !== null) {
                echo json_encode(['status' => 'error', 'message' => 'Thao tác không hợp lệ!']);
                exit;
            }
            $update = $pdo->prepare("UPDATE attendances SET check_out = ? WHERE user_id = ? AND attendance_date = ?");
            $update->execute([$now, $user_id, $today]);
            echo json_encode(['status' => 'success', 'message' => 'Check-out thành công lúc ' . $now]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>
