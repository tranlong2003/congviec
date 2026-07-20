<?php 
require_once 'inc/header.php'; 
require_once 'inc/sidebar.php'; 
$msg = "";

// 1. XỬ LÝ THÊM MỚI KHÁCH HÀNG (Có Tuổi & Quê Quán)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_customer') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $telegram = trim($_POST['telegram'] ?? '');
    $age = !empty($_POST['age']) ? intval($_POST['age']) : null;
    $hometown = trim($_POST['hometown'] ?? '');
    $source = $_POST['source'] ?? 'Khác';
    $status = $_POST['status'] ?? 'Chưa gọi';
    $note = trim($_POST['note'] ?? '');

    if (!empty($name) && !empty($phone)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO customers (name, phone, telegram, age, hometown, source, status, note, assigned_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $telegram, $age, $hometown, $source, $status, $note, $user_id]);
            $msg = "<p style='color: #10b981; font-weight:600; margin-bottom: 15px;'>➕ Thêm khách hàng thành công!</p>";
        } catch (PDOException $e) {
            $msg = "<p style='color: #ef4444; font-weight:600; margin-bottom: 15px;'>Lỗi: " . $e->getMessage() . "</p>";
        }
    }
}

// 2. XỬ LÝ XÓA KHÁCH HÀNG
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ? AND assigned_to = ?");
        $stmt->execute([$delete_id, $user_id]);
        echo "<script>location.href='customers.php';</script>";
        exit;
    } catch (PDOException $e) {
        $msg = "<p style='color: #ef4444; font-weight:600;'>Lỗi xóa: " . $e->getMessage() . "</p>";
    }
}

// 3. XỬ LÝ CẬP NHẬT REALTIME (Tự động lưu khi Sale sửa trên bảng)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_customer') {
    $customer_id = $_POST['customer_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $source = $_POST['source'] ?? '';
    $age = !empty($_POST['age']) ? intval($_POST['age']) : null;
    $hometown = $_POST['hometown'] ?? '';
    $note = $_POST['note'] ?? '';
    try {
        $update = $pdo->prepare("UPDATE customers SET status = ?, source = ?, age = ?, hometown = ?, note = ? WHERE id = ? AND assigned_to = ?");
        $update->execute([$status, $source, $age, $hometown, $note, $customer_id, $user_id]);
        echo json_encode(['status' => 'success']);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error']);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE assigned_to = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$customers = $stmt->fetchAll();
?>
<div style="display: grid; grid-template-columns: 0.6fr 1.4fr; gap: 24px; align-items: start; width: 100%;">
    <!-- FORM THÊM KHÁCH HÀNG -->
    <div class="panel">
        <div class="panel-header">📝 Thêm Nhanh Khách Hàng</div>
        <div style="margin-top: 15px;">
            <?= $msg ?>
            <form action="customers.php" method="POST">
                <input type="hidden" name="action" value="add_customer">
                <div class="form-group"><label>Họ và Tên *</label><input type="text" name="name" required placeholder="Nguyễn Văn A"></div>
                <div class="form-group"><label>Số điện thoại *</label><input type="text" name="phone" required placeholder="0987xxxxxx"></div>
                <div class="form-group"><label>Telegram Username</label><input type="text" name="telegram" placeholder="@username"></div>
                
                <!-- Bổ sung Tuổi & Quê quán vào Form thêm -->
                <div class="form-group"><label>Tuổi khách hàng</label><input type="number" name="age" placeholder="Ví dụ: 25"></div>
                <div class="form-group"><label>Quê quán / Nơi sống</label><input type="text" name="hometown" placeholder="Ví dụ: Hà Nội, Đà Nẵng..."></div>
                
                <div class="form-group">
                    <label>Nguồn khách</label>
                    <select name="source">
                        <option value="Khác">Khác</option>
                        <option value="TikTok">TikTok</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Google">Google</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="Chưa gọi">Chưa gọi</option>
                        <option value="Đã gọi">Đã gọi</option>
                        <option value="Quan tâm">Quan tâm</option>
                    </select>
                </div>
                <div class="form-group"><label>Ghi chú ban đầu</label><input type="text" name="note" placeholder="Nhập ghi chú nhanh..."></div>
                <button type="submit" class="btn-submit" style="width:100%; margin-top: 10px;">🚀 Thêm Khách Hàng</button>
            </form>
        </div>
    </div>

    <!-- BẢNG HIỂN THỊ DANH SÁCH + CHỨC NĂNG SỬA/XÓA -->
    <div class="panel">
        <div class="panel-header">👥 Khách Hàng Chăm Sóc Của Bạn</div>
        <div style="margin-top: 15px; overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Khai thác hồ sơ (Realtime)</th>
                        <th>Nguồn</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú làm việc (Realtime)</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($customers) === 0): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">Bạn chưa tự thêm khách hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $row): ?>
                        <tr>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($row['name']) ?></div>
                                <div style="font-size:11px; color:var(--text-muted);">ID: #<?= $row['id'] ?></div>
                                <div style="font-size:12px; color: #3b82f6; margin-top:4px;">📞 <?= htmlspecialchars($row['phone']) ?></div>
                                <div style="font-size:11px; color: #9ca3af;">✈️ <?= htmlspecialchars($row['telegram'] ?? 'N/A') ?></div>
                            </td>
                            <!-- Ô SỬA TUỔI VÀ QUÊ QUÁN TRỰC TIẾP TRÊN BẢNG -->
                            <td>
                                <div style="margin-bottom: 6px; display:flex; align-items:center; gap:4px;">
                                    <span style="font-size:12px; color:var(--text-muted); width:35px;">Tuổi:</span>
                                    <input type="number" value="<?= htmlspecialchars($row['age'] ?? '') ?>" id="age-<?= $row['id'] ?>" onchange="updateCustomer(<?= $row['id'] ?>)" style="width: 70px; padding:4px 8px; font-size:13px;">
                                </div>
                                <div style="display:flex; align-items:center; gap:4px;">
                                    <span style="font-size:12px; color:var(--text-muted); width:35px;">Quê:</span>
                                    <input type="text" value="<?= htmlspecialchars($row['hometown'] ?? '') ?>" id="hometown-<?= $row['id'] ?>" onchange="updateCustomer(<?= $row['id'] ?>)" style="width: 110px; padding:4px 8px; font-size:13px;">
                                </div>
                            </td>
                            <td>
                                <select onchange="updateCustomer(<?= $row['id'] ?>)" id="source-<?= $row['id'] ?>" style="width:105px;">
                                    <option value="Khác" <?= $row['source']=='Khác'?'selected':'' ?>>Khác</option>
                                    <option value="TikTok" <?= $row['source']=='TikTok'?'selected':'' ?>>TikTok</option>
                                    <option value="Facebook" <?= $row['source']=='Facebook'?'selected':'' ?>>Facebook</option>
                                    <option value="Google" <?= $row['source']=='Google'?'selected':'' ?>>Google</option>
                                </select>
                            </td>
                            <td>
                                <select onchange="updateCustomer(<?= $row['id'] ?>)" id="status-<?= $row['id'] ?>" style="width:115px; font-weight:600;">
                                    <option value="Chưa gọi" <?= $row['status']=='Chưa gọi'?'selected':'' ?>>Chưa gọi</option>
                                    <option value="Đã gọi" <?= $row['status']=='Đã gọi'?'selected':'' ?>>Đã gọi</option>
                                    <option value="Quan tâm" <?= $row['status']=='Quan tâm'?'selected':'' ?>>Quan tâm</option>
                                    <option value="Đã mở MT5" <?= $row['status']=='Đã mở MT5'?'selected':'' ?>>Đã mở MT5</option>
                                    <option value="Đã nạp" <?= $row['status']=='Đã nạp'?'selected':'' ?>>Đã nạp</option>
                                    <option value="Khách VIP" <?= $row['status']=='Khách VIP'?'selected':'' ?>>Khách VIP</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" value="<?= htmlspecialchars($row['note'] ?? '') ?>" id="note-<?= $row['id'] ?>" onchange="updateCustomer(<?= $row['id'] ?>)" style="width: 100%; min-width: 140px; background: rgba(0,0,0,0.2);">
                            </td>
                            <!-- NÚT XÓA KHÁCH HÀNG -->
                            <td>
                                <a href="customers.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này không?')" class="btn-reject" style="padding: 6px 10px; font-size: 11px; text-decoration:none; display:inline-block;">🗑️ Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateCustomer(id) {
    const status = document.getElementById('status-' + id).value;
    const source = document.getElementById('source-' + id).value;
    const age = document.getElementById('age-' + id).value;
    const hometown = document.getElementById('hometown-' + id).value;
    const note = document.getElementById('note-' + id).value;

    const formData = new FormData();
    formData.append('action', 'update_customer');
    formData.append('customer_id', id);
    formData.append('status', status);
    formData.append('source', source);
    formData.append('age', age);
    formData.append('hometown', hometown);
    formData.append('note', note);

    fetch('customers.php', { method: 'POST', body: formData });
}
</script>
<?php require_once 'inc/footer.php'; ?>
