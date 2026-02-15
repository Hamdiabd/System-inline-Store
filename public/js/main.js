// انتظار تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('موقع MVC جاهز');
    
    // إضافة تأثيرات للروابط
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });
    
    // تأكيد الحذف
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if(!confirm('هل أنت متأكد من الحذف؟')) {
                e.preventDefault();
            }
        });
    });
    
    // إخفاء رسائل التنبيه بعد 5 ثوان
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
    
    // إضافة كلاس active للرابط الحالي
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-menu a');
    navLinks.forEach(link => {
        if(link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
});

// دالة للبحث المباشر (اختياري)
function searchProducts() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.toLowerCase();
    const productRows = document.querySelectorAll('.product-row');
    
    productRows.forEach(row => {
        const productName = row.querySelector('.product-name').textContent.toLowerCase();
        if(productName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}