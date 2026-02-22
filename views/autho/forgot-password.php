<?php $errors = $data; ?>
<?php include APP_PATH . 'views/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>🔑 استعادة كلمة المرور</h2>
            <p>أدخل بريدك الإلكتروني وسنرسل لك رابط استعادة كلمة المرور</p>
        </div>

        <?php if(!empty($errors['message'])): ?>
            <div class="alert alert-success"><?= $errors['message'] ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/sendResetLink" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control <?= !empty($errors['email_err']) ? 'is-invalid' : '' ?>" 
                           value="<?= $errors['email'] ?? '' ?>" 
                           placeholder="example@email.com"
                           required>
                </div>
                <?php if(!empty($errors['email_err'])): ?>
                    <small class="error-message"><?= $errors['email_err'] ?></small>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-paper-plane"></i>
                إرسال رابط الاستعادة
            </button>
        </form>

        <div class="auth-footer">
            <p><a href="<?= BASE_URL ?>auth/login">← العودة لتسجيل الدخول</a></p>
        </div>
    </div>
</div>

<?php include APP_PATH . 'views/layout/footer.php'; ?>