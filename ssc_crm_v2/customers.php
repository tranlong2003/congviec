<?php 
require_once 'inc/header.php'; 
require_once 'inc/sidebar.php'; 
$msg = "";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'ajax_edit') {
        $customer_id = intval($_POST['customer_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $telegram = trim($_POST['telegram'] ?? '');
        $age = !empty($_POST['age']) ? intval($_POST['age']) : null;
        $hometown = trim($_POST['hometown'] ?? '');
        $source = $_POST['source'] ?? 'Khác';
        $status = $_POST['status'] ?? 'Chưa gọi';
        $note = trim($_POST['note'] ?? '');

        try {
            $update = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, telegram = ?, age = ?, hometown = ?, source = ?, status = ?, note = ? WHERE id = ? AND assigned_to = ?");
            $update->execute([$name, $phone, $telegram, $age, $hometown, $source, $status, $note, $customer_id, $user_id]);
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công!']);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    if ($_POST['action'] === 'ajax_delete') {
        $customer_id = intval($_POST['customer_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ? AND assigned_to = ?");
            $stmt->execute([$customer_id, $user_id]);
            echo json_encode(['status' => 'success']);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE assigned_to = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$customers = $stmt->fetchAll();
?>

<div style="display: grid; grid-template-columns: 0.6fr 1.4fr; gap: 24px; align-items: start; width: 100%;">
    <div class="panel">
        <div class="panel-header">📝 Thêm Nhanh Khách Hàng</div>
        <div style="margin-top: 15px;">
            <?= $msg ?>
            <form action="customers.php" method="POST">
                <input type="hidden" name="action" value="add_customer">
                <div class="form-group"><label>Họ và Tên *</label><input type="text" name="name" required placeholder="Nguyễn Văn A"></div>
                <div class="form-group"><label>Số điện thoại *</label><input type="text" name="phone" required placeholder="0987xxxxxx"></div>
                <div class="form-group"><label>Telegram Username</label><input type="text" name="telegram" placeholder="@username"></div>
                <div class="form-group"><label>Tuổi khách hàng</label><input type="number" name="age" placeholder="Ví dụ: 25"></div>
                <div class="form-group"><label>Quê quán / Nơi sống</label><input type="text" name="hometown" placeholder="Ví dụ: Hà Nội..."></div>
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

    <div class="panel">
        <div class="panel-header">👥 Khách Hàng Chăm Sóc Của Bạn</div>
        <div style="margin-top: 15px; overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Hồ sơ chi tiết</th>
                        <th>Phân loại</th>
                        <th>Ghi chú làm việc</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    <?php if (count($customers) === 0): ?>
                        <tr id="noDataRow"><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">Bạn chưa có khách hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $row): ?>
                        <tr id="row-<?= $row['id'] ?>">
                            <td>
                                <div id="txt-name-<?= $row['id'] ?>" style="font-weight:600; color:var(--text-heading); font-size:15px;"><?= htmlspecialchars($row['name']) ?></div>
                                <div style="font-size:11px; color:var(--text-muted); margin-bottom:4px;">ID: #<?= $row['id'] ?></div>
                                <div style="font-size:13px; color: #10b981;">📞 <span id="txt-phone-<?= $row['id'] ?>"><?= htmlspecialchars($row['phone']) ?></span></div>
                                <div style="font-size:12px; color: #3b82f6;">✈️ <span id="txt-tele-<?= $row['id'] ?>"><?= htmlspecialchars($row['telegram'] ? $row['telegram'] : 'N/A') ?></span></div>
                            </td>
                            <td>
                                <div style="font-size:13px; color:var(--text-main);">🎂 Tuổi: <span id="txt-age-<?= $row['id'] ?>" style="font-weight:600;"><?= $row['age'] ? $row['age'] : '---' ?></span></div>
                                <div style="font-size:13px; color:var(--text-main); margin-top:4px;">📍 Quê: <span id="txt-home-<?= $row['id'] ?>" style="font-weight:600;"><?= $row['hometown'] ? htmlspecialchars($row['hometown']) : '---' ?></span></div>
                            </td>
                            <td>
                                <div style="font-size:12px; color:var(--text-muted);">Nguồn: <span id="txt-source-<?= $row['id'] ?>" style="color:var(--text-heading); font-weight:600;"><?= $row['source'] ?></span></div>
                                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Trạng thái: <span id="txt-status-<?= $row['id'] ?>" style="color:var(--color-revenue); font-weight:600;"><?= $row['status'] ?></span></div>
                            </td>
                            <td id="txt-note-<?= $row['id'] ?>" style="font-size:13px; max-width:180px; font-style:italic; color:var(--text-muted);">
                                <?= $row['note'] ? htmlspecialchars($row['note']) : 'Chưa có ghi chú...' ?>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:6px; align-items:center; justify-content:center;">
                                    <button onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)" class="btn-approve" style="padding: 6px 12px; font-size: 12px; font-weight:600; width:70px; text-align:center;">✏️ Sửa</button>
                                    <button onclick="deleteCustomerAJAX(<?= $row['id'] ?>)" class="btn-reject" style="padding: 6px 12px; font-size: 12px; width:70px; text-align:center; font-weight:600; cursor:pointer;">🗑️ Xóa</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editCustomerModal" style="display:none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); align-items: center; justify-content: center; padding:20px;">
    <div class="panel" style="width:100%; max-width:450px; background: var(--bg-card); border-color: var(--accent-purple); box-shadow: var(--shadow-hover);">
        <h3 style="color:var(--text-heading); margin-bottom:15px; border-left:4px solid var(--accent-purple); padding-left:10px;">✏️ Chỉnh Sửa Hồ Sơ Khách Hàng</h3>
        
        <form id="editCustomerForm" onsubmit="submitEditAJAX(event)">
            <input type="hidden" name="action" value="ajax_edit">
            <input type="hidden" name="customer_id" id="edit_id">
            
            <div class="form-group"><label>Họ và Tên</label><input type="text" name="name" id="edit_name" required></div>
            <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" id="edit_phone" required></div>
            <div class="form-group"><label>Telegram Username</label><input type="text" name="telegram" id="edit_telegram"></div>
            <div class="form-group"><label>Tuổi</label><input type="number" name="age" id="edit_age"></div>
            <div class="form-group"><label>Quê quán / Nơi sống</label><input type="text" name="hometown" id="edit_hometown"></div>
            
            <div class="form-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label>Nguồn khách</label>
                    <select name="source" id="edit_source">
                        <option value="Khác">Khác</option>
                        <option value="TikTok">TikTok</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Google">Google</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" id="edit_status">
                        <option value="Chưa gọi">Chưa gọi</option>
                        <option value="Đã gọi">Đã gọi</option>
                        <option value="Quan tâm">Quan tâm</option>
                        <option value="Đã mở MT5">Đã mở MT5</option>
                        <option value="Đã nạp">Đã nạp</option>
                        <option value="Khách VIP">Khách VIP</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group"><label>Ghi chú làm việc chi tiết</label><input type="text" name="note" id="edit_note"></div>
            
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" class="btn-submit" style="flex:1;">💾 Lưu Thay Đổi</button>
                <button type="button" onclick="closeEditModal()" class="theme-btn" style="flex:1; background:rgba(255,255,255,0.05); color:var(--text-main);">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(customerData) {
    document.getElementById('edit_id').value = customerData.id;
    document.getElementById('edit_name').value = customerData.name;
    document.getElementById('edit_phone').value = customerData.phone;
    document.getElementById('edit_telegram').value = customerData.telegram || '';
    document.getElementById('edit_age').value = customerData.age || '';
    document.getElementById('edit_hometown').value = customerData.hometown || '';
    document.getElementById('edit_source').value = customerData.source;
    document.getElementById('edit_status').value = customerData.status;
    document.getElementById('edit_note').value = customerData.note || '';
    
    document.getElementById('editCustomerModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editCustomerModal').style.display = 'none';
}

function submitEditAJAX(event) {
    event.preventDefault();
    
    const form = document.getElementById('editCustomerForm');
    const formData = new FormData(form);
    const id = document.getElementById('edit_id').value;

    fetch('customers.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('txt-name-' + id).innerText = document.getElementById('edit_name').value;
            document.getElementById('txt-phone-' + id).innerText = document.getElementById('edit_phone').value;
            
            const teleVal = document.getElementById('edit_telegram').value;
            document.getElementById('txt-tele-' + id).innerText = teleVal ? teleVal : 'N/A';
            
            const ageVal = document.getElementById('edit_age').value;
            document.getElementById('txt-age-' + id).innerText = ageVal ? ageVal : '---';
            
            const homeVal = document.getElementById('edit_hometown').value;
            document.getElementById('txt-home-' + id).innerText = homeVal ? homeVal : '---';
            
            document.getElementById('txt-source-' + id).innerText = document.getElementById('edit_source').value;
            document.getElementById('txt-status-' + id).innerText = document.getElementById('edit_status').value;
            
            const noteVal = document.getElementById('edit_note').value;
            document.getElementById('txt-note-' + id).innerText = noteVal ? noteVal : 'Chưa có ghi chú...';

            closeEditModal();
            alert("🎉 Đã sửa thông tin thành công!");
        } else {
            alert("Có lỗi xảy ra: " + data.message);
        }
    })
    .catch(err => console.error(err));
}

function deleteCustomerAJAX(id) {
    if(!confirm('Bạn có chắc chắn muốn xóa khách hàng này không?')) return;

    const formData = new FormData();
    formData.append('action', 'ajax_delete');
    formData.append('customer_id', id);

    fetch('customers.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            const row = document.getElementById('row-' + id);
            row.style.opacity = '0';
            row.style.transform = 'scale(0.9)';
            setTimeout(() => {
                row.remove();
                const tableBody = document.getElementById('customerTableBody');
                if(tableBody.children.length === 0) {
                    tableBody.innerHTML = '<tr id="noDataRow"><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">Bạn chưa có khách hàng nào.</td></tr>';
                }
            }, 200);
        } else {
            alert("Lỗi không thể xóa: " + data.message);
        }
    })
    .catch(err => console.error(err));
}
</script>

<?php require_once 'inc/footer.php'; ?>
