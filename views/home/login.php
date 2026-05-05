<!-- يعتمد هذا الملف على وجود theme.css و main.css مسبقاً في header.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - <?= $app_name ?? 'لوحة التحكم' ?></title>
    <!-- الملفات المركزية -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/theme.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <!-- تنسيقات صفحة تسجيل الدخول -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
    <!-- أيقونات Font Awesome (اختياري) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="login-page">

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h1 class="login-title">تسجيل الدخول</h1>
            <p class="login-subtitle">مرحباً بعودتك! قم بتسجيل الدخول للمتابعة</p>
        </div>

        <!-- منطقة عرض الأخطاء (في حال إرسال خاطئ) -->
        <?php if (!empty($error)): ?>
            <div class="alert-message alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- نموذج تسجيل الدخول التقليدي -->
        <form method="POST" action="<?= BASE_URL ?>auth/login">
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" name="email" placeholder="البريد الإلكتروني" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" placeholder="كلمة المرور" required>
            </div>

            <div class="login-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> تذكرني
                </label>
                <a href="<?= BASE_URL ?>auth/forgot-password" class="forgot-link">نسيت كلمة المرور؟</a>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </button>
        </form>

        <!-- فاصل -->
        <div class="divider">
            <span>أو</span>
        </div>

        <!-- زر تسجيل الدخول بحساب Google -->
        <a href="<?= BASE_URL ?>auth/google" class="btn-google">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" 
                 alt="Google logo">
            تسجيل الدخول بحساب Google
        </a>

        <!-- رابط إنشاء حساب جديد -->
        <div class="signup-prompt">
            ليس لديك حساب؟ <a href="<?= BASE_URL ?>auth/register">إنشاء حساب جديد</a>
        </div>
    </div>

</body>
</html>