<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSC Fintech CRM - Dashboard</title>
    <style>
        /* --- ĐỊNH NGHĨA BIẾN MÀU SẮC CHO 2 THEME --- */
        :root, [data-theme="dark"] {
            --bg-main: #0c0e12;
            --bg-card: #11141b;
            --border-color: #1f2633;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --text-heading: #ffffff;
            --accent-purple: #7c3aed;
            --accent-purple-hover: rgba(124, 58, 237, 0.15);
            --color-data: #10b981;
            --color-call: #3b82f6;
            --color-mt5: #8b5cf6;
            --color-revenue: #f59e0b;
        }

        [data-theme="light"] {
            --bg-main: #f3f4f6;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --text-main: #1f2937;
            --text-muted: #4b5563;
            --text-heading: #111827;
            --accent-purple: #3b82f6;
            --accent-purple-hover: rgba(59, 130, 246, 0.1);
            --color-data: #059669;
            --color-call: #2563eb;
            --color-mt5: #7c3aed;
            --color-revenue: #d97706;
        }

        /* --- CSS RESET & PHÂN BỐ CỤC --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        /* --- TOP HEADER --- */
        header {
            height: 70px;
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: 0.5px;
        }

        .logo span {
            color: var(--accent-purple);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .attendance-badge {
            background-color: var(--color-data);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-heading);
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* --- WRAPPER CHÍNH --- */
        .wrapper {
            display: flex;
            flex: 1;
        }

        /* --- SIDEBAR MENU BÊN TRÁI --- */
        aside {
            width: 260px;
            background-color: var(--bg-card);
            border-right: 1px solid var(--border-color);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin: 18px 0 8px 10px;
            font-weight: 700;
        }

        .sidebar-title:first-of-type {
            margin-top: 0;
        }

        .menu-list {
            list-style: none;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: all 0.2s;
        }

        .menu-link:hover {
            background-color: var(--border-color);
            color: var(--text-heading);
        }

        .menu-link.active {
            background-color: var(--accent-purple-hover);
            color: var(--accent-purple);
            font-weight: 600;
            border-left: 3px solid var(--accent-purple);
            padding-left: 9px;
        }

        .sidebar-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .theme-btn {
            width: 100%;
            background-color: var(--border-color);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .theme-btn:hover {
            opacity: 0.9;
        }

        .logout-btn {
            text-align: center;
            color: #ef4444;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 8px;
        }

        /* --- KHU VỰC HIỂN THỊ CHÍNH --- */
        main {
            flex: 1;
            padding: 32px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .welcome-section h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 4px;
        }

        .welcome-section p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* --- CARDS KPI GRID --- */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .kpi-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .kpi-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .kpi-badge {
            font-size: 11px;
            font-weight: 600;
        }

        .kpi-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 12px;
        }

        .progress-bar {
            height: 6px;
            background-color: var(--border-color);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        /* --- HAI KHỐI THỐNG KÊ (SPLIT BOX) --- */
        .split-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .split-grid {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }

        .panel-header {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 20px;
            border-left: 4px solid var(--accent-purple);
            padding-left: 12px;
        }

        /* Biểu đồ giả lập */
        .chart-mock {
            height: 160px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding-top: 20px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .chart-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .chart-bar {
            width: 32px;
            background: linear-gradient(180deg, var(--accent-purple) 0%, rgba(124, 58, 237, 0.1) 100%);
            border-radius: 6px 6px 0 0;
            transition: height 0.5s ease;
        }

        .chart-label {
            margin-top: 8px;
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Bảng xếp hạng */
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leaderboard-table th {
            text-align: left;
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .leaderboard-table td {
            padding: 12px 0;
            font-size: 14px;
            border-bottom: 1px solid rgba(31, 38, 51, 0.2);
        }

        .rank {
            font-weight: 700;
            width: 40px;
        }

        .rank-1 { color: #fbbf24; }
        .rank-2 { color: #94a3b8; }
        .rank-3 { color: #b45309; }

        .name {
            font-weight: 600;
            color: var(--text-heading);
        }

        .value {
            text-align: right;
            font-weight: 600;
            color: var(--accent-purple);
        }

        /* --- NHẬT KÝ HOẠT ĐỘNG (LOGS) --- */
        .logs-panel {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }

        .log-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .log-item {
            font-size: 13.5px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(31, 38, 51, 0.1);
            color: var(--text-main);
        }

        .log-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .log-time {
            color: var(--text-muted);
            font-weight: 600;
            margin-right: 12px;
        }

        .log-highlight {
            color: var(--accent-purple);
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- TOP HEADER -->
    <header>
        <div class="logo"><span>SSC</span> FINTECH CRM</div>
        <div class="header-actions">
            <div class="attendance-badge">📍 ĐÃ ĐIỂM DANH CA SÁNG</div>
            <div class="user-profile">
                <span class="user-name">Nguyễn Văn Long</span>
                <span class="user-role">Quản trị viên (Admin)</span>
            </div>
        </div>
    </header>

    <!-- CONTAINER LAYOUT -->
    <div class="wrapper">
        
        <!-- MENU BÊN TRÁI -->
        <aside>
            <div class="menu-container">
                <div class="sidebar-title">Menu hệ thống</div>
                <ul class="menu-list">
                    <li><a class="menu-link active" href="#">🏠 Dashboard</a></li>
                    <li><a class="menu-link" href="#">📍 Điểm danh</a></li>
                    <li><a class="menu-link" href="#">👥 Khách hàng</a></li>
                    <li><a class="menu-link" href="#">📞 Công việc hôm nay</a></li>
                    <li><a class="menu-link" href="#">📝 Báo cáo</a></li>
                    <li><a class="menu-link" href="#">🎯 KPI</a></li>
                    <li><a class="menu-link" href="#">🏆 BXH Sale</a></li>
                    <li><a class="menu-link" href="#">🕒 Lịch sử</a></li>
                    <li><a class="menu-link" href="#">⚙️ Tài khoản</a></li>
                </ul>

                <div class="sidebar-title">(Admin / Quản lý)</div>
                <ul class="menu-list">
                    <li><a class="menu-link" href="#">👤 Quản lý nhân viên</a></li>
                    <li><a class="menu-link" href="#">📂 Quản lý báo cáo</a></li>
                    <li><a class="menu-link" href="#">📊 Thống kê</a></li>
                </ul>
            </div>

            <!-- CHÂN SIDEBAR VỚI NÚT CHUYỂN THEME -->
            <div class="sidebar-footer">
                <button class="theme-btn" id="themeToggle">☀️ Chuyển Theme Sáng</button>
                <a class="logout-btn" href="#">Đăng xuất</a>
            </div>
        </aside>

        <!-- KHU VỰC NỘI DUNG CHÍNH -->
        <main>
            <div class="welcome-section">
                <h1>Chào buổi sáng, Long!</h1>
                <p>Dữ liệu tổng quan hệ thống SSC FINTECH ngày hôm nay.</p>
            </div>

            <!-- KPI Row -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Data Giao</span>
                        <span class="kpi-badge" style="color: var(--color-data);">+12% hôm qua</span>
                    </div>
                    <div class="kpi-value">120</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 80%; background-color: var(--color-data);"></div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Đã Thực Hiện</span>
                        <span class="kpi-badge" style="color: var(--color-call);">Mục tiêu: 100</span>
                    </div>
                    <div class="kpi-value">85 <span style="font-size: 13px; color: var(--text-muted); font-weight: normal;">cuộc gọi</span></div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%; background-color: var(--color-call);"></div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Tài Khoản MT5</span>
                        <span class="kpi-badge" style="color: var(--color-mt5);">Tỉ lệ: 13.3%</span>
                    </div>
                    <div class="kpi-value">16 <span style="font-size: 13px; color: var(--text-muted); font-weight: normal;">mở mới</span></div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 60%; background-color: var(--color-mt5);"></div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Doanh Số (FTD)</span>
                        <span class="kpi-badge" style="color: var(--color-revenue);">Tháng này</span>
                    </div>
                    <div class="kpi-value">15.000.000đ</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%; background-color: var(--color-revenue);"></div>
                    </div>
                </div>
            </div>

            <!-- Split Row (Chart + Leaderboard) -->
            <div class="split-grid">
                
                <!-- Biểu đồ doanh số -->
                <div class="panel">
                    <div class="panel-header">Doanh Số & Lots Tuần Này</div>
                    <div class="chart-mock">
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 50px;"></div>
                            <div class="chart-label">Thứ 2</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 110px;"></div>
                            <div class="chart-label">Thứ 3</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 80px;"></div>
                            <div class="chart-label">Thứ 4</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 140px;"></div>
                            <div class="chart-label">Thứ 5</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 95px;"></div>
                            <div class="chart-label">Thứ 6</div>
                        </div>
                    </div>
                </div>

                <!-- Bảng xếp hạng -->
                <div class="panel">
                    <div class="panel-header">Chiến Thần Doanh Số</div>
                    <table class="leaderboard-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">Hạng</th>
                                <th>Sale</th>
                                <th style="text-align: center;">FTD</th>
                                <th style="text-align: right;">Doanh số</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="rank rank-1">🥇</td>
                                <td class="name">Nguyễn Hoàng Nam</td>
                                <td style="text-align: center; font-weight: 600;">18</td>
                                <td class="value">120.000.000đ</td>
                            </tr>
                            <tr>
                                <td class="rank rank-2">🥈</td>
                                <td class="name">Trần Thủy Tiên</td>
                                <td style="text-align: center; font-weight: 600;">12</td>
                                <td class="value">85.500.000đ</td>
                            </tr>
                            <tr>
                                <td class="rank rank-3">🥉</td>
                                <td class="name">Lê Quốc Anh</td>
                                <td style="text-align: center; font-weight: 600;">10</td>
                                <td class="value">60.000.000đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Realtime Logs -->
            <div class="logs-panel">
                <div class="panel-header" style="border-left-color: #ef4444;">Hoạt Động Gần Đây (Realtime Logs)</div>
                <div class="log-list">
                    <div class="log-item">
                        <span class="log-time">[10:15]</span> Sale <span class="log-highlight">Quốc Anh</span> vừa cập nhật trạng thái khách hàng "Nguyễn Văn A" &rarr; <span style="color: var(--color-mt5); font-weight: 600;">Đã mở MT5</span>.
                    </div>
                    <div class="log-item">
                        <span class="log-time">[10:10]</span> Hệ thống tự động phân chia <span class="log-highlight">20 Data mới</span> cho Sale Thủy Tiên.
                    </div>
                    <div class="log-item">
                        <span class="log-time">[09:55]</span> Sale <span class="log-highlight">Hoàng Nam</span> hoàn thành Báo cáo cuối ngày (Doanh số: <span style="color: var(--color-data); font-weight: 600;">+15.000.000đ</span>).
                    </div>
                </div>
            </div>
        </main>

    </div>

    <!-- SCRIPT CHUYỂN ĐỔI THEME -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;

            // Kiểm tra theme đã lưu trong localStorage hoặc mặc định là dark
            const savedTheme = localStorage.getItem('theme') || 'dark';
            htmlElement.setAttribute('data-theme', savedTheme);
            updateButtonText(savedTheme);

            // Xử lý sự kiện click chuyển theme
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateButtonText(newTheme);
            });

            function updateButtonText(theme) {
                if (theme === 'dark') {
                    themeToggleBtn.innerHTML = '☀️ Chuyển Theme Sáng';
                } else {
                    themeToggleBtn.innerHTML = '🌙 Chuyển Theme Tối';
                }
            }
        });
    </script>
</body>
</html>
