
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - نظام المتجر</title>
    
    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- ملفات CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <!-- ===== الشريط العلوي الثابت (Fixed Header) ===== -->
    <header class="fixed-header">
        <div class="header-container">
            <!-- زر القائمة للجوال -->
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- الشعار -->
            <div class="header-logo">
                <a href="<?= BASE_URL ?>home/index">
                    <i class="fas fa-store"></i>
                    <span>متجري</span>
                </a>
            </div>
            
            <!-- شريط البحث -->
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="بحث..." id="globalSearch">
            </div>
            
            <!-- أيقونات وأزرار المستخدم -->
            <div class="header-actions">
                <!-- الإشعارات -->
                <div class="notification-dropdown">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    <div class="dropdown-content">
                        <a href="#">
                            <i class="fas fa-info-circle"></i>
                            <span>طلب جديد #1234</span>
                            <small>منذ 5 دقائق</small>
                        </a>
                        <a href="#">
                            <i class="fas fa-check-circle"></i>
                            <span>تم شحن طلبك</span>
                            <small>منذ ساعة</small>
                        </a>
                    </div>
                </div>
                
                <!-- قائمة المستخدم -->
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= BASE_URL ?>uploads/users/<?= $_SESSION['user_avatar'] ?? 'default.jpg' ?>" alt="صورة المستخدم">
                        <span class="user-name"><?= $_SESSION['user_name'] ?? 'زائر' ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="<?= BASE_URL ?>auth/profile">
                            <i class="fas fa-user"></i>
                            <span>الملف الشخصي</span>
                        </a>
                        <a href="<?= BASE_URL ?>auth/settings">
                            <i class="fas fa-cog"></i>
                            <span>الإعدادات</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>auth/logout" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>تسجيل خروج</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
