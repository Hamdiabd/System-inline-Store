<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-store"></i>
        </div>
        <h3>لوحة التحكم</h3>
        <p>مرحباً، <?= $_SESSION['user_name'] ?></p>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/dashboard" class="nav-link <?= $activePage == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>لوحة التحكم</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">المتجر</div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/product/index" class="nav-link <?= $activePage == 'products' ? 'active' : '' ?>">
                    <i class="fas fa-boxes"></i>
                    <span>المنتجات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/category/index" class="nav-link">
                    <i class="fas fa-tags"></i>
                    <span>التصنيفات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/brand/index" class="nav-link">
                    <i class="fas fa-copyright"></i>
                    <span>العلامات التجارية</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">الطلبات</div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/order/index" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>الطلبات</span>
                    <span class="badge">12</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/return/index" class="nav-link">
                    <i class="fas fa-undo-alt"></i>
                    <span>المرتجعات</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">التقارير</div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/report/sales" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>المبيعات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/report/inventory" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>المخزون</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">الإعدادات</div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>admin/setting/index" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>الإعدادات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= BASE_URL ?>auth/logout" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل خروج</span>
                </a>
            </div>
        </div>
    </nav>
</aside>