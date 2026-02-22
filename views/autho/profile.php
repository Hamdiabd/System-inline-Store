<?php $user = $data['user']; ?>
<?php $errors = $_SESSION['profile_errors'] ?? []; ?>
<?php include APP_PATH . 'views/layout/header.php'; ?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-cover">
            <div class="profile-avatar">
                <img src="<?= BASE_URL ?>uploads/users/<?= $_SESSION['user_image'] ?? 'default.jpg' ?>" 
                     alt="<?= $_SESSION['user_name'] ?>"
                     id="profileImage">
                <button class="change-avatar-btn" onclick="document.getElementById('avatarInput').click()">
                    <i class="fas fa-camera"></i>
                </button>
                <input type="file" id="avatarInput" accept="image/*" style="display: none;">
            </div>
            <h2><?= $_SESSION['user_name'] ?></h2>
            <p class="user-role"><?= $_SESSION['user_role'] == 'admin' ? 'مدير النظام' : 'عميل' ?></p>
        </div>
    </div>

    <div class="profile-content">
        <?php if(isset($data['success']) && !empty($data['success'])): ?>
            <div class="alert alert-success"><?= $data['success'] ?></div>
        <?php endif; ?>

        <?php if(isset($data['error']) && !empty($data['error'])): ?>
            <div class="alert alert-error"><?= $data['error'] ?></div>
        <?php endif; ?>

        <div class="profile-tabs">
            <button class="tab-btn active" onclick="openTab('info')">📋 المعلومات الشخصية</button>
            <button class="tab-btn" onclick="openTab('orders')">📦 الطلبات</button>
            <button class="tab-btn" onclick="openTab('wishlist')">❤️ المفضلة</button>
            <button class="tab-btn" onclick="openTab('addresses')">📍 العناوين</button>
            <button class="tab-btn" onclick="openTab('security')">🔒 الأمان</button>
        </div>

        <!-- المعلومات الشخصية -->
        <div id="info" class="tab-content active">
            <form action="<?= BASE_URL ?>auth/updateProfile" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="name">الاسم الكامل</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control <?= !empty($errors['name_err']) ? 'is-invalid' : '' ?>" 
                           value="<?= $errors['name'] ?? $user->name ?>">
                    <?php if(!empty($errors['name_err'])): ?>
                        <small class="error-message"><?= $errors['name_err'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" 
                           id="email" 
                           value="<?= $user->email ?>" 
                           class="form-control" 
                           readonly disabled>
                    <small>لا يمكن تغيير البريد الإلكتروني</small>
                </div>

                <div class="form-group">
                    <label for="phone">رقم الجوال</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control <?= !empty($errors['phone_err']) ? 'is-invalid' : '' ?>" 
                           value="<?= $errors['phone'] ?? $user->phone ?>">
                    <?php if(!empty($errors['phone_err'])): ?>
                        <small class="error-message"><?= $errors['phone_err'] ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            </form>
        </div>

        <!-- الطلبات -->
        <div id="orders" class="tab-content">
            <h3>الطلبات السابقة</h3>
            <?php if(empty($user->orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>لا توجد طلبات سابقة</p>
                    <a href="<?= BASE_URL ?>product/index" class="btn btn-primary">تسوق الآن</a>
                </div>
            <?php else: ?>
                <!-- جدول الطلبات -->
            <?php endif; ?>
        </div>

        <!-- المفضلة -->
        <div id="wishlist" class="tab-content">
            <h3>المنتجات المفضلة</h3>
            <?php if(empty($user->wishlist)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart"></i>
                    <p>لا توجد منتجات في المفضلة</p>
                    <a href="<?= BASE_URL ?>product/index" class="btn btn-primary">تصفح المنتجات</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- الأمان -->
        <div id="security" class="tab-content">
            <h3>تغيير كلمة المرور</h3>
            <form action="<?= BASE_URL ?>auth/changePassword" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="current_password">كلمة المرور الحالية</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="new_password">كلمة المرور الجديدة</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="confirm_new_password">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
            </form>
        </div>
    </div>
</div>

<script>
function openTab(tabName) {
    // إخفاء كل التبويبات
    var tabs = document.getElementsByClassName('tab-content');
    for(var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    
    // إزالة active من كل الأزرار
    var btns = document.getElementsByClassName('tab-btn');
    for(var i = 0; i < btns.length; i++) {
        btns[i].classList.remove('active');
    }
    
    // إظهار التبويب المحدد
    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
}

// معاينة الصورة قبل الرفع
document.getElementById('avatarInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var reader = new FileReader();
    
    reader.onload = function(e) {
        document.getElementById('profileImage').src = e.target.result;
    }
    
    reader.readAsDataURL(file);
    
    // رفع الصورة تلقائياً
    var formData = new FormData();
    formData.append('avatar', file);
    
    fetch('<?= BASE_URL ?>auth/updateAvatar', {
        method: 'POST',
        body: formData
    });
});
</script>

<?php 
unset($_SESSION['profile_errors']);
include APP_PATH . 'views/layout/footer.php'; 
?>