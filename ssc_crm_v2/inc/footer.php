<?php
// inc/footer.php
$base_url = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
?>
</main>
    </div>
    <script>
        // Xử lý đổi theme Light/Dark
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

        // Xử lý điểm danh AJAX
        function submitAttendance(actionType) {
            const formData = new FormData();
            formData.append('action', actionType);

            fetch('<?= $base_url ?>attendance.php', { method: 'POST', body: formData })
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
</body>
</html>
