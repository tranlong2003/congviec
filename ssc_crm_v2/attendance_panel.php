<?php 
require_once 'inc/header.php'; 
require_once 'inc/sidebar.php'; 

date_default_timezone_set('Asia/Ho_Chi_Minh');

$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM attendances WHERE user_id = ? AND attendance_date = ?");
$stmt->execute([$user_id, $today]);
$attendance = $stmt->fetch();
?>

<div class="form-box" style="max-width: 550px;">
    <h2>📍 Bảng Chấm Công Ca Làm Việc</h2>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Xác thực nhanh bằng Wifi văn phòng hoặc GPS tọa độ thực tế.</p>

    <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 10px; border: 1px solid var(--border-color); text-align: center; margin-bottom: 20px;">
        <span style="font-size: 13px; color: var(--text-muted); display: block; margin-bottom: 6px;">Trạng thái hôm nay:</span>
        <?php if (!$attendance): ?>
            <span style="font-size: 18px; font-weight: 700; color: #ef4444;">🔴 Chưa điểm danh</span>
        <?php else: ?>
            <span style="font-size: 18px; font-weight: 700; color: #10b981;">🟢 Đã Check-in (<?= $attendance['method'] ?>) lúc <?= $attendance['check_in'] ?></span>
            <?php if ($attendance['check_out']): ?>
                <span style="font-size: 14px; color: #3b82f6; display: block; margin-top: 6px;">(Đã Check-out lúc: <?= $attendance['check_out'] ?>)</span>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>1. Chọn phương thức xác thực</label>
        <select id="attendanceMethod" style="padding: 12px; font-size:14px;">
            <option value="Wifi Văn Phòng">🌐 Kết nối Wifi Văn Phòng (Băng tần 5Ghz)</option>
            <option value="Vị Trí (GPS)">📍 Xác thực vị trí thực tế (Tọa độ GPS)</option>
        </select>
    </div>

    <div id="methodGuide" style="background: rgba(124,58,237,0.05); border: 1px dashed var(--accent-purple); padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
        Hệ thống sẽ đối soát IP tĩnh để xác định bạn có đang kết nối mạng nội bộ công ty không.
    </div>

    <div style="display: flex; gap: 12px;">
        <?php if (!$attendance): ?>
            <button class="btn-attendance" onclick="processAttendance('check_in')" style="flex: 1; padding: 14px; background: #10b981; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                📍 Xác Nhận Check-In
            </button>
        <?php elseif ($attendance && !$attendance['check_out']): ?>
            <button class="btn-attendance" onclick="processAttendance('check_out')" style="flex: 1; padding: 14px; background: #ef4444; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                🕒 Xác Nhận Check-Out
            </button>
        <?php else: ?>
            <button class="btn-submit" disabled style="flex: 1; opacity: 0.5; background: #6b7280; cursor: not-allowed; box-shadow: none; transform: none;">
                Ca làm việc đã hoàn thành
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
const methodSelect = document.getElementById('attendanceMethod');
const methodGuide = document.getElementById('methodGuide');

methodSelect.addEventListener('change', () => {
    if (methodSelect.value === 'Wifi Văn Phòng') {
        methodGuide.innerHTML = "Hệ thống sẽ đối soát IP tĩnh để xác định bạn có đang kết nối mạng nội bộ công ty không.";
    } else {
        methodGuide.innerHTML = "Vui lòng cho phép thiết bị của bạn truy cập vị trí GPS để tính toán bán kính trong phạm vi 100m của công ty.";
    }
});

function processAttendance(actionType) {
    const selectedMethod = methodSelect.value;
    const formData = new FormData();
    formData.append('action', actionType);
    formData.append('method', selectedMethod);

    if (selectedMethod === 'Vị Trí (GPS)') {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                formData.append('lat', position.coords.latitude);
                formData.append('lng', position.coords.longitude);
                sendAttendanceRequest(formData);
            }, (error) => {
                alert("Không thể lấy tọa độ định vị. Vui lòng bật GPS trên thiết bị!");
            });
        } else {
            alert("Trình duyệt không hỗ trợ định vị vị trí!");
        }
    } else {
        sendAttendanceRequest(formData);
    }
}

function sendAttendanceRequest(formData) {
    fetch('attendance.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            location.reload(); 
        }
    })
    .catch(err => console.error(err));
}
</script>
<?php require_once 'inc/footer.php'; ?>
