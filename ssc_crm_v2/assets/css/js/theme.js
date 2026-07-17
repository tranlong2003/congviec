// assets/js/theme.js
document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    // 1. Kiểm tra trạng thái đã lưu, nếu chưa có thì mặc định dùng Dark Mode
    const savedTheme = localStorage.getItem('theme') || 'dark';
    htmlElement.setAttribute('data-theme', savedTheme);
    updateButtonText(savedTheme);

    // 2. Lắng nghe sự kiện click nút đổi Theme
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateButtonText(newTheme);
        });
    }

    function updateButtonText(theme) {
        if (!themeToggleBtn) return;
        if (theme === 'dark') {
            themeToggleBtn.innerHTML = '☀️ Chuyển Theme Sáng';
        } else {
            themeToggleBtn.innerHTML = '🌙 Chuyển Theme Tối';
        }
    }
});
