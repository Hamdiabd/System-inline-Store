   <aside class="fixed-sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>القائمة الرئيسية</h3>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <!-- قسم الرئيسية -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>" class="nav-link <?= $activePage == 'home' ? 'active' : '' ?>">
                            <i class="fas fa-home"></i>
                            <span class="nav-text">الرئيسية</span>
                        </a>
                    </li>
                    
                    <!-- قسم المنتجات -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>product/index" class="nav-link <?= strpos($activePage, 'product') !== false ? 'active' : '' ?>">
                            <i class="fas fa-box"></i>
                            <span class="nav-text">المنتجات</span>
                        </a>
                        <ul class="nav-submenu <?= strpos($activePage, 'product') !== false ? 'expanded' : '' ?>">
                            <li>
                                <a href="<?= BASE_URL ?>product/index" class="<?= $activePage == 'product-index' ? 'active' : '' ?>">
                                    <i class="fas fa-list"></i>
                                    <span>كل المنتجات</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>product/create" class="<?= $activePage == 'product-create' ? 'active' : '' ?>">
                                    <i class="fas fa-plus"></i>
                                    <span>إضافة منتج</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- قسم الفئات (للمدير فقط) -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>admin/categories" class="nav-link">
                            <i class="fas fa-tags"></i>
                            <span class="nav-text">التصنيفات</span>
                        </a>
                    </li>
                    
                    <!-- قسم الطلبات -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>order/index" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-text">الطلبات</span>
                            <span class="badge">12</span>
                        </a>
                    </li>
                    
                    <!-- الفاصل -->
                    <li class="nav-divider"></li>
                    
                    <!-- قسم الصفحات -->
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>home/about" class="nav-link">
                            <i class="fas fa-info-circle"></i>
                            <span class="nav-text">من نحن</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>home/contact" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span class="nav-text">اتصل بنا</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <p>© <?= date('Y') ?> جميع الحقوق محفوظة</p>
            </div>
        </aside>