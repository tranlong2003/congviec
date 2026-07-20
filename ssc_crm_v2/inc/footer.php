<?php
// inc/footer.php
?>
</main>
    </div>
    <script>
        const themeToggleBtn = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'dark';
        htmlElement.setAttribute('data-theme', savedTheme);
        if(themeToggleBtn) {
            themeToggleBtn.innerHTML = savedTheme === 'dark' ? '☀️ Chuyển Theme Sáng' : '🌙 Chuyển Theme Tối';
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                themeToggleBtn.innerHTML = newTheme === 'dark' ? '☀️ Chuyển Theme Sáng' : '🌙 Chuyển Theme Tối';
            });
        }
    </script>
</body>
</html>
