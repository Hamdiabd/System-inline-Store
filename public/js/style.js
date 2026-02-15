


// تأكيد الحذف
document.addEventListener('DOMContentLoaded', function() {
    // إضافة رسالة تأكيد للحذف
    const deleteForms = document.querySelectorAll('.delete-form');
   
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
                e.preventDefault();
            }
        });
    });
   
    // رسائل تنبيه تختفي تلقائياً
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
   
    // التحقق من المدخلات في النماذج
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredInputs = form.querySelectorAll('[required]');
            let isValid = true;
           
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#f56565';
                    input.focus();
                }
            });
           
            if (!isValid) {
                e.preventDefault();
                alert('الرجاء تعبئة جميع الحقول المطلوبة');
            }
        });
    });
});


