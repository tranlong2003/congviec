<?php 
require_once 'inc/header.php'; 
require_once 'inc/sidebar.php'; 

date_default_timezone_set('Asia/Ho_Chi_Minh');
?>

<div class="welcome-section" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 20px; border-radius: 12px; box-shadow: var(--shadow-glow); margin-bottom: 8px;">
    <div>
        <h1 id="dynamicGreeting" style="font-size: 24px; color: var(--text-heading); margin-bottom: 6px;">Chào bạn, <?= htmlspecialchars($fullname) ?>!</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Hệ thống SSC FINTECH CRM của bạn đang hoạt động ổn định.</p>
    </div>
    
    <div style="text-align: right; background: rgba(124, 58, 237, 0.08); border: 1px solid var(--accent-purple); padding: 10px 20px; border-radius: 8px; box-shadow: inset 0 0 10px rgba(124, 58, 237, 0.1);">
        <div id="liveClock" style="font-size: 22px; font-weight: 700; color: var(--accent-purple); letter-spacing: 1px; font-family: monospace;">00:00:00</div>
        <div id="liveDate" style="font-size: 12px; color: var(--text-muted); font-weight: 600; margin-top: 4px; text-transform: uppercase;">Chủ Nhật, 19/07/2026</div>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span>Data Giao</span><span style="color: var(--color-data);">+12%</span></div>
        <div class="kpi-value">120</div>
        <div class="progress-bar"><div class="progress-fill" style="width: 80%; background-color: var(--color-data);"></div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span>Đã Gọi</span><span style="color: var(--color-call);">Mục tiêu: 100</span></div>
        <div class="kpi-value">85</div>
        <div class="progress-bar"><div class="progress-fill" style="width: 85%; background-color: var(--color-call);"></div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span>Tài khoản MT5</span><span style="color: var(--color-mt5);">Tỷ lệ: 13.3%</span></div>
        <div class="kpi-value">16</div>
        <div class="progress-bar"><div class="progress-fill" style="width: 60%; background-color: var(--color-mt5);"></div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span>Doanh Số</span><span style="color: var(--color-revenue);">FTD</span></div>
        <div class="kpi-value">15.000.000đ</div>
        <div class="progress-bar"><div class="progress-fill" style="width: 45%; background-color: var(--color-revenue);"></div></div>
    </div>
</div>

<div class="split-grid">
    <div class="panel">
        <div class="panel-header">Doanh Số Tuần Này</div>
        <div class="chart-mock">
            <div class="chart-bar" style="height: 60px;"></div>
            <div class="chart-bar" style="height: 110px;"></div>
            <div class="chart-bar" style="height: 80px;"></div>
            <div class="chart-bar" style="height: 140px;"></div>
            <div class="chart-bar" style="height: 95px;"></div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-header">Chiến Thần Doanh Số</div>
        <table class="leaderboard-table">
            <thead>
                <tr><th>Hạng</th><th>Sale</th><th style="text-align: right;">Doanh số</th></tr>
            </thead>
            <tbody>
                <tr><td class="rank">🥇</td><td>Nguyễn Hoàng Nam</td><td style="text-align: right; font-weight: 600;">120.000.000đ</td></tr>
                <tr><td class="rank" style="color: #94a3b8;">🥈</td><td>Trần Thủy Tiên</td><td style="text-align: right; font-weight: 600;">85.500.000đ</td></tr>
                <tr><td class="rank" style="color: #b45309;">🥉</td><td>Lê Quốc Anh</td><td style="text-align: right; font-weight: 600;">60.000.000đ</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateClockAndGreeting() {
    const now = new Date();
    const hours = now.getHours();
    let greeting = "";
    const name = "<?= htmlspecialchars($fullname) ?>";

    if (hours >= 5 && hours < 12) {
        greeting = `☀️ Chào buổi sáng, ${name}!`;
    } else if (hours >= 12 && hours < 18) {
        greeting = `⛅ Chào buổi chiều, ${name}!`;
    } else {
        greeting = `🌙 Chào buổi tối, ${name}!`;
    }
    document.getElementById('dynamicGreeting').innerText = greeting;

    const displayHours = String(hours).padStart(2, '0');
    const displayMinutes = String(now.getMinutes()).padStart(2, '0');
    const displaySeconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('liveClock').innerText = `${displayHours}:${displayMinutes}:${displaySeconds}`;

    const daysOfWeek = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
    const dayName = daysOfWeek[now.getDay()];
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    document.getElementById('liveDate').innerText = `${dayName}, ${day}/${month}/${year}`;
}

updateClockAndGreeting();
setInterval(updateClockAndGreeting, 1000);
</script>

<?php require_once 'inc/footer.php'; ?>
