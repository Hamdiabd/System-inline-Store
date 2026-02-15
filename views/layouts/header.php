<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?> System Market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="header-logo">
                <h2>
                <img src="../../public/images/hamdisoft.png"style="width: 100px;height: 93px;border-radius: 49%;">
                </h2>
            </div>
            
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="بحث عن منتجات..." id="searchInput">
            </div>
            
            <div class="header-user">
                <i class="fas fa-user-circle"></i>
                <span>User </span>
                <div class="user-dropdown">
                    <a href="#"><i class="fas fa-user"></i> الملف الشخصي</a>
                    <a href="#"><i class="fas fa-cog"></i> الإعدادات</a>
                    <a href="#"><i class="fas fa-sign-out-alt"></i> تسجيل خروج</a>
                </div>
            </div>
        </div>
    </header>