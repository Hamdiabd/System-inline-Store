<aside class="main-sidebar">
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>home/index" class="nav-link <?= $activePage == 'home' ? 'active' : '' ?>">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/index" class="nav-link <?= $activePage == 'products' ? 'active' : '' ?>">
                            <i class="fas fa-box"></i>
                            <span>المنتجات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/index" class="nav-link <?= $activePage == 'products' ? 'active' : '' ?>">
                            <i class="fas fa-box"></i>
                            <span>الاقسام</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/index" class="nav-link <?= $activePage == 'products' ? 'active' : '' ?>">
                            <i class="fas fa-box"></i>
                            <span>الفواتير</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/index" class="nav-link <?= $activePage == 'products' ? 'active' : '' ?>">
                            <i class="fas fa-box"></i>
                            <span>الموارد البشرية</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/create" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span> الاحصائصات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/create" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span> التقارير</span>
                        </a>
                    </div>
                    
                    <div class="nav-divider"></div>
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>product/create" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span> المرتجعات</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>home/about" class="nav-link <?= $activePage == 'about' ? 'active' : '' ?>">
                            <i class="fas fa-info-circle"></i>
                            <span>من نحن</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>home/services" class="nav-link <?= $activePage == 'services' ? 'active' : '' ?>">
                            <i class="fas fa-cogs"></i>
                            <span>الخدمات</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="<?= BASE_URL ?>home/contact" class="nav-link <?= $activePage == 'contact' ? 'active' : '' ?>">
                            <i class="fas fa-envelope"></i>
                            <span>اتصل بنا</span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <p>© Hamdi Soft </p>
            </div>
        </aside>