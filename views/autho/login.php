<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام المتجر</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header .icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px;
            background: white;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-right: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-right: 4px solid #dc3545;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i {
            position: absolute;
            right: 15px;
            color: #999;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            padding: 15px 45px 15px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .error-message {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #666;
            font-size: 14px;
        }

        .checkbox-label input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }

        .auth-footer p {
            color: #666;
            font-size: 14px;
        }

        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .auth-divider {
            position: relative;
            text-align: center;
            margin: 25px 0;
        }

        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }

        .auth-divider span {
            position: relative;
            background: white;
            padding: 0 15px;
            color: #999;
            font-size: 14px;
        }

        .demo-accounts {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .demo-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .demo-btn:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }

        .demo-btn.admin {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .demo-btn .role {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .demo-btn .email {
            font-size: 12px;
            color: #666;
        }

        .demo-btn .password {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <h1>تسجيل الدخول</h1>
                <p>أهلاً بعودتك! أدخل بياناتك للدخول</p>
            </div>

            <div class="login-body">
                <!-- رسائل التنبيه -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?= $_SESSION['success'] ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= $_SESSION['error'] ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- نموذج تسجيل الدخول -->
                <form action="<?= BASE_URL ?>auth/authenticate" method="POST">
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control <?= !empty($data['email_err']) ? 'is-invalid' : '' ?>" 
                                   value="<?= $data['email'] ?? '' ?>" 
                                   placeholder="example@email.com"
                                   required>
                        </div>
                        <?php if(!empty($data['email_err'])): ?>
                            <small class="error-message"><?= $data['email_err'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control <?= !empty($data['password_err']) ? 'is-invalid' : '' ?>" 
                                   placeholder="********"
                                   required>
                        </div>
                        <?php if(!empty($data['password_err'])): ?>
                            <small class="error-message"><?= $data['password_err'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span>تذكرني</span>
                        </label>
                        <a href="<?= BASE_URL ?>auth/forgot-password" class="forgot-link">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>تسجيل الدخول</span>
                    </button>
                </form>

                <!-- حسابات تجريبية -->
                <div class="auth-divider">
                    <span>حسابات تجريبية</span>
                </div>

                <div class="demo-accounts">
                    <!-- حساب مدير -->
                    <div class="demo-btn admin" onclick="fillLogin('admin@example.com', '123456')">
                        <div class="role">👑 مدير</div>
                        <div class="email">admin@example.com</div>
                        <div class="password">كلمة المرور: 123456</div>
                    </div>

                    <!-- حساب عميل -->
                    <div class="demo-btn" onclick="fillLogin('ahmed@example.com', '123456')">
                        <div class="role">👤 عميل</div>
                        <div class="email">ahmed@example.com</div>
                        <div class="password">كلمة المرور: 123456</div>
                    </div>
                </div>

                <div class="auth-footer">
                    <p>ليس لديك حساب؟ <a href="<?= BASE_URL ?>auth/register">إنشاء حساب جديد</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // تعبئة بيانات تسجيل الدخول تلقائياً عند النقر على حساب تجريبي
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>