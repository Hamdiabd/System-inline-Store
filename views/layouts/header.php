<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'لوحة التحكم' ?></title>
    <!-- ملف المتغيرات والتصميم المركزي -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/theme.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/product-form.css">
    <!-- التخطيط الرئيسي -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <!-- أي ملفات CSS إضافية -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/images.css">
</head>
<body>

<!-- زر القائمة للجوال (للوصول: aria-label و aria-expanded) -->
<button class="sidebar-toggle" id="sidebarToggle" 
        aria-label="فتح القائمة الجانبية" aria-expanded="false" 
        aria-controls="mainSidebar">
    ☰
</button>

<!-- Header ثابت -->
<header class="main-header" role="banner">
    <div class="header-logo">
        <img src="<?= BASE_URL ?>images/settings/slogan.png" alt="شعار الموقع">
        <span class="site-name">التسوق الإلكتروني</span>
    </div>

    <nav class="header-nav" aria-label="التنقل العلوي">
        <a href="#" class="btn btn-header">
            الطلبات <span class="badge"><?= $_SERVER["ordercount"] ?? 0 ?></span>
        </a>
        <a href="#" class="btn btn-header">
            الإشعارات <span class="badge">5</span>
        </a>
        <a href="#" class="btn btn-header">
            العملاء <span class="badge">5</span>
        </a>
        <a href="#" class="btn btn-header">
            الكميات <span class="badge">5</span>
        </a>

        <div class="user-info">
            <img src="<?= BASE_URL ?>images/settings/slogan.png" alt="صورة المستخدم">
            <span><?= $data['user'][0]->full_name ?? 'مستخدم' ?></span>
        </div>
    </nav>
</header>

<!-- الحاوية الرئيسية: تبدأ بعد الهيدر مباشرة -->
<div class="app-container">