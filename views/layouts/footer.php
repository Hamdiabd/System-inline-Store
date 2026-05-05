</main><!-- نهاية main-content -->
</div><!-- نهاية app-container -->

<!-- سكريبت بسيط للتحكم بالسايدبار على الجوال مع مراعاة الوصول -->
<script>
(function() {
    const sidebar = document.getElementById('mainSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        toggleBtn.setAttribute('aria-expanded', 'true');
        toggleBtn.setAttribute('aria-label', 'إغلاق القائمة الجانبية');
        // حفظ التركيز على أول عنصر قائمة للوصول
        const firstLink = sidebar.querySelector('a');
        if(firstLink) firstLink.focus();
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.setAttribute('aria-label', 'فتح القائمة الجانبية');
        toggleBtn.focus(); // إعادة التركيز للزر
    }

    toggleBtn.addEventListener('click', function() {
        if (sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    // إغلاق بالسكيب من لوحة المفاتيح (Escape)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });
})();
</script>
</body>
</html>