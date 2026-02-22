    <!-- JavaScript -->
    <script>
        // تبديل القائمة الجانبية في الجوال
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // إغلاق القوائم المنسدلة عند النقر خارجها
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                document.querySelectorAll('.user-dropdown .dropdown-content').forEach(d => {
                    d.classList.remove('show');
                });
            }
            if (!e.target.closest('.notification-dropdown')) {
                document.querySelectorAll('.notification-dropdown .dropdown-content').forEach(d => {
                    d.classList.remove('show');
                });
            }
        });

        // فتح/إغلاق القوائم المنسدلة
        document.querySelectorAll('.user-btn, .notification-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                this.closest('.user-dropdown, .notification-dropdown').querySelector('.dropdown-content').classList.toggle('show');
            });
        });

        // تفعيل القوائم الفرعية
        document.querySelectorAll('.nav-item > .nav-link').forEach(link => {
            if(link.nextElementSibling && link.nextElementSibling.classList.contains('nav-submenu')) {
                link.addEventListener('click', function(e) {
                    if(window.innerWidth <= 768) {
                        e.preventDefault();
                        this.nextElementSibling.classList.toggle('expanded');
                    }
                });
            }
        });
    </script>
    
    <script src="<?= BASE_URL ?>js/main.js"></script>
</body>
</html>