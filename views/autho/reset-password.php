<?php $errors = $data; ?>
<?php include APP_PATH . 'views/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>🔄 إعادة تعيين كلمة المرور</h2>
            <p>أدخل كلمة المرور الجديدة</p>
        </div>

        <form action="<?= BASE_URL ?>auth/updatePassword" method="POST" class="auth-form">
            <input type="hidden" name="token" value="<?= $errors['token'] ?>">

            <div class="form-group">
                <label for="password">كلمة المرور الجديدة</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control <?= !empty($errors['password_err']) ? 'is-invalid' : '' ?>" 
                           placeholder="********"
                           required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small>6 أحرف على الأقل</small>
                <?php if(!empty($errors['password_err'])): ?>
                    <small class="error-message"><?= $errors['password_err'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">تأكيد كلمة المرور</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           class="form-control <?= !empty($errors['confirm_password_err']) ? 'is-invalid' : '' ?>" 
                           placeholder="********"
                           required>
                </div>
                <?php if(!empty($errors['confirm_password_err'])): ?>
                    <small class="error-message"><?= $errors['confirm_password_err'] ?></small>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i>
                حفظ كلمة المرور الجديدة
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = event.currentTarget.querySelector('i');
    
    if(field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include APP_PATH . 'views/layout/footer.php'; ?>