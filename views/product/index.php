<div class="content-header">
    <div>
        <h1>
            <span class="header-icon">📦</span>
            إدارة المنتجات
        </h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>"><span>🏠</span> الرئيسية</a>
            <span class="separator">›</span>
            <span>المنتجات</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>product/create" class="btn btn-primary">
        <span>➕</span> إضافة منتج جديد
    </a>
</div>

<div class="content-body">

    <!-- ========== رسائل التنبيه ========== -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['message'] ?>
            <?php unset($_SESSION['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']) ?>
        </div>
    <?php endif; ?>

    <!-- ========== شريط البحث والفلترة ========== -->
    <div class="card">
        <div class="card-header">
            <h2>📋 قائمة المنتجات</h2>
            <div class="filter-bar">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="ابحث عن منتج..." class="form-control">
                </div>
                <select id="statusFilter" class="form-control" style="width: auto;">
                    <option value="">📋 كل الحالات</option>
                    <option value="1">✅ نشط</option>
                    <option value="0">❌ غير نشط</option>
                </select>
            </div>
        </div>

        <!-- ========== جدول المنتجات ========== -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="80">الصورة</th>
                        <th>اسم المنتج</th>
                        <th>العلامة</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>الحالة</th>
                        <th width="150">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $index => $product): ?>
                            <tr class="product-row" data-status="<?= $product->is_active ?>">
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="product-thumb">
                                        <?php if (!empty($product->base_image_url)): ?>
                                            <img src="<?= BASE_URL . $product->base_image_url ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                        <?php else: ?>
                                            <span class="no-image">📷</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="product-name-text"><?= htmlspecialchars($product->name) ?></span>
                                    <?php if (!empty($product->description)): ?>
                                        <small class="product-desc-text"><?= mb_substr(htmlspecialchars($product->description), 0, 40) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-light"><?= htmlspecialchars($product->brand_name ?? 'بدون') ?></span>
                                </td>
                                <td>
                                    <span class="price-text">
                                        <?= !empty($product->min_price) ? number_format($product->min_price, 2) . ' ريال' : '—' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= ($product->total_stock ?? 0) > 10 ? 'badge-success' : (($product->total_stock ?? 0) > 0 ? 'badge-warning' : 'badge-danger') ?>">
                                        <?= $product->total_stock ?? 'نفذ' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $product->is_active ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $product->is_active ? 'نشط' : 'غير نشط' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>product/edit/<?= $product->product_id ?>" class="btn btn-sm btn-warning" title="تعديل">
                                            ✏️
                                        </a>
                                        <a href="<?= BASE_URL ?>product/delete/<?= $product->product_id ?>"
                                            class="btn btn-sm btn-danger"
                                            title="حذف"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            🗑️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <span>📭</span>
                                    <h3>لا توجد منتجات</h3>
                                    <p>ابدأ بإضافة منتجك الأول</p>
                                    <a href="<?= BASE_URL ?>product/create" class="btn btn-primary">➕ إضافة منتج</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========== JavaScript للبحث والفلترة ========== -->
<link rel="stylesheet" href="<?= BASE_URL ?>css/product-index.css">
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            if (!status || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>