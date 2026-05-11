<!-- الشريط الجانبي (يحتوي على role و aria-label) -->
<aside class="main-sidebar" id="mainSidebar" role="navigation" aria-label="القائمة الرئيسية">
    <ul class="sidebar-nav">
        <li><a href="<?= BASE_URL ?>" class="btn btn-sidebar active">الرئيسية</a></li>
        <li><a href="<?= BASE_URL ?>home/about" class="btn btn-sidebar">الطلبات</a></li>
        <li><a href="<?= BASE_URL ?>product/index" class="btn btn-sidebar">المنتجات</a></li>
        <li><a href="#" class="btn btn-sidebar">التقارير</a></li>
        <li><a href="#" class="btn btn-sidebar">الموارد البشرية</a></li>
        <li><a href="<?= BASE_URL ?>user" class="btn btn-sidebar">المستخدمين</a></li>
        <li><a href="#" class="btn btn-sidebar">العملاء</a></li>
        <li><a href="#" class="btn btn-sidebar">الفواتير</a></li>
        <li><a href="#" class="btn btn-sidebar">الإعدادات</a></li>
    </ul>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<main class="main-content" id="mainContent" role="main">
    <!-- هنا سيتم وضع المحتوى الخاص بكل صفحة -->