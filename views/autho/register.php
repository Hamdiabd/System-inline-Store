<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - نظام المتجر</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* نفس تنسيقات صفحة تسجيل الدخول مع بعض الإضافات */
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

        .register-container {
            width: 100%;
            max-width: 550px;
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

        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .register-header .icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
        }

        .register-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .register-body {
            padding: 40px;
            background: white;
        }

        /* نفس باقي التنسيقات من صفحة login مع إضافة: */

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.half {
            margin-bottom: 0;
        }

        .password-hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .btn-register {
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
            margin-top: 20px;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .terms {
            margin: 20px 0;
        }

        .terms a {
            color: #667eea;
            text-decoration: none;
        }

        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>إنشاء حساب جديد</h1>
                <p>أدخل بياناتك للتسجيل في المتجر</p>
            </div>

            <div class="register-body">
                <!-- عرض الأخطاء إن وجدت -->
                <?php if(isset($data['name_err']) || isset($data['email_err']) || isset($data['phone_err']) || isset($data['password_err']) || isset($data['confirm_password_err'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>الرجاء تصحيح الأخطاء في النموذج</span>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/store" method="POST">
                    <!-- الاسم الكامل -->
                    <div class="form-group">
                        <label for="name">الاسم الكامل *</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control <?= !empty($data['name_err']) ? 'is-invalid' : '' ?>" 
                                   value="<?= $data['name'] ?? '' ?>" 
                                   placeholder="أحمد محمد"
                                   required>
                        </div>
                        <?php if(!empty($data['name_err'])): ?>
                            <small class="error-message"><?= $data['name_err'] ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني *</label>
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

                    <!-- رقم الجوال -->
                    <div class="form-group">
                        <label for="phone">رقم الجوال *</label>
                        <div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control <?= !empty($data['phone_err']) ? 'is-invalid' : '' ?>" 
                                   value="<?= $data['phone'] ?? '' ?>" 
                                   placeholder="05xxxxxxxx"
                                   required>
                        </div>
                        <?php if(!empty($data['phone_err'])): ?>
                            <small class="error-message"><?= $data['phone_err'] ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- كلمة المرور وتأكيدها -->
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="password">كلمة المرور *</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control <?= !empty($data['password_err']) ? 'is-invalid' : '' ?>" 
                                       placeholder="********"
                                       required>
                            </div>
                            <small class="password-hint">6 أحرف على الأقل</small>
                            <?php if(!empty($data['password_err'])): ?>
                                <small class="error-message"><?= $data['password_err'] ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group half">
                            <label for="confirm_password">تأكيد كلمة المرور *</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       class="form-control <?= !empty($data['confirm_password_err']) ? 'is-invalid' : '' ?>" 
                                       placeholder="********"
                                       required>
                            </div>
                            <?php if(!empty($data['confirm_password_err'])): ?>
                                <small class="error-message"><?= $data['confirm_password_err'] ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- الموافقة على الشروط -->
                    <div class="form-group terms">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>أوافق على <a href="#">الشروط والأحكام</a> و <a href="#">سياسة الخصوصية</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i>
                        <span>إنشاء حساب</span>
                    </button>
                </form>

                <div class="auth-footer">
                    <p>لديك حساب بالفعل؟ <a href="<?= BASE_URL ?>auth/login">تسجيل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>