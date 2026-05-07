<!-- views/product/index.php -->
<div class="content-header">
    <div>
        <h1><span class="header-icon">📦</span> إدارة المنتجات</h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>"><span>🏠</span> الرئيسية</a>
            <span class="separator">›</span>
            <span class="current">المنتجات</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>product/add" class="btn btn-primary btn-lg pulse-animation">
        <span>➕</span> إضافة منتج جديد
    </a>
</div>

<div class="content-body">

    <!-- رسائل التنبيه -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- الإحصائيات -->
    <div class="stats-grid">
        <div class="stat-card gradient-blue">
            <div class="stat-icon-wrapper"><span class="stat-icon">📦</span></div>
            <div class="stat-content">
                <span class="stat-value"><?= $data['stats']->total_products ?></span>
                <span class="stat-label">إجمالي المنتجات</span>
            </div>
            <div class="stat-bg-icon">📦</div>
        </div>
        <div class="stat-card gradient-green">
            <div class="stat-icon-wrapper"><span class="stat-icon">✅</span></div>
            <div class="stat-content">
                <span class="stat-value"><?= $data['stats']->active_products ?></span>
                <span class="stat-label">منتجات نشطة</span>
            </div>
            <div class="stat-bg-icon">✅</div>
        </div>
        <div class="stat-card gradient-orange">
            <div class="stat-icon-wrapper"><span class="stat-icon">📊</span></div>
            <div class="stat-content">
                <span class="stat-value"><?= $data['stats']->total_stock ?></span>
                <span class="stat-label">إجمالي المخزون</span>
            </div>
            <div class="stat-bg-icon">📊</div>
        </div>
        <div class="stat-card gradient-purple">
            <div class="stat-icon-wrapper"><span class="stat-icon">🏷️</span></div>
            <div class="stat-content">
                <span class="stat-value"><?= $data['stats']->total_variants ?></span>
                <span class="stat-label">إجمالي المتغيرات</span>
            </div>
            <div class="stat-bg-icon">🏷️</div>
        </div>
    </div>

    <!-- فلتر وبحث -->
    <div class="card">
        <form method="GET" action="<?= BASE_URL ?>products" class="filter-form">
            <input type="text" name="search" placeholder="🔍 ابحث عن منتج..." value="<?= htmlspecialchars($data['search']) ?>" class="filter-input">
            <select name="status" class="filter-select">
                <option value="">📋 كل الحالات</option>
                <option value="1" <?= $data['status'] === '1' ? 'selected' : '' ?>>✅ نشط</option>
                <option value="0" <?= $data['status'] === '0' ? 'selected' : '' ?>>❌ غير نشط</option>
            </select>
            <button type="submit" class="btn btn-secondary">تصفية</button>
            <?php if (!empty($data['search']) || $data['status'] !== ''): ?>
                <a href="<?= BASE_URL ?>products" class="btn btn-sm">إعادة ضبط</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- جدول المنتجات -->
    <div class="card product-table-card">
        <div class="card-header-modern">
            <h2><span>📋</span> قائمة المنتجات (<?= $data['pagination']['total'] ?>)</h2>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>صورة</th>
                        <th>المنتج</th>
                        <th>العلامة</th>
                        <th>الأقسام</th>
                        <th>المتغيرات</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['products'])): ?>
                        <tr>
                            <td colspan="10" class="empty-state-modern">
                                <span class="empty-icon">📭</span>
                                <h3>لا توجد منتجات</h3>
                                <a href="<?= BASE_URL ?>product/add" class="btn btn-primary">➕ إضافة منتج</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $start = ($data['pagination']['current_page'] - 1) * $data['pagination']['per_page'];
                        foreach ($data['products'] as $index => $product): 
                            $rowNumber = $start + $index + 1;
                        ?>
                            <tr>
                                <td><?= $rowNumber ?></td>
                                <td>
                                    <div class="product-thumb-modern">
                                        <?php if ($product->base_image_url): ?>
                                            <img src="<?= BASE_URL . $product->base_image_url ?>" alt="<?= htmlspecialchars($product->product_name) ?>">
                                        <?php else: ?>
                                            <span class="no-image">📷</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product->product_name) ?></strong>
                                    <?php if ($product->description): ?>
                                        <br><small><?= mb_substr(htmlspecialchars($product->description), 0, 50) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= $product->brand_name ?: '—' ?></td>
                                <td>
                                    <?php if ($product->categories_names): ?>
                                        <?php foreach (explode(',', $product->categories_names) as $cat): ?>
                                            <span class="category-chip"><?= trim($cat) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td><?= $product->variant_count ?></td>
                                <td>
                                    <?php if ($product->min_price == $product->max_price): ?>
                                        <?= number_format($product->min_price, 2) ?> ريال
                                    <?php else: ?>
                                        <?= number_format($product->min_price, 2) ?> - <?= number_format($product->max_price, 2) ?> ريال
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $product->total_stock > 10 ? 'badge-success' : ($product->total_stock > 0 ? 'badge-warning' : 'badge-danger') ?>">
                                        <?= $product->total_stock ?: 'نفذ' ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- نموذج لتغيير الحالة -->
                                    <form method="POST" action="<?= BASE_URL ?>product/toggleStatus?<?= http_build_query($_GET) ?>" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?= $product->product_id ?>">
                                        <input type="hidden" name="is_active" value="<?= $product->is_active ? 0 : 1 ?>">
                                        <button type="submit" class="toggle-btn-modern <?= $product->is_active ? 'active' : '' ?>" title="تبديل الحالة">
                                            <span class="toggle-circle"></span>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>product/view/<?= $product->product_id ?>" class="btn-action" title="عرض">👁️</a>
                                    <a href="<?= BASE_URL ?>product/edit/<?= $product->product_id ?>" class="btn-action edit" title="تعديل">✏️</a>
                                    <a href="<?= BASE_URL ?>product/delete/<?= $product->product_id ?>" 
                                       onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')" 
                                       class="btn-action delete" title="حذف">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($data['pagination']['total_pages'] > 1): ?>
            <div class="pagination-wrapper">
                <span>صفحة <?= $data['pagination']['current_page'] ?> من <?= $data['pagination']['total_pages'] ?></span>
                <div>
                    <?php if ($data['pagination']['current_page'] > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $data['pagination']['current_page'] - 1])) ?>" class="page-btn">⬅️</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="page-btn <?= $i === $data['pagination']['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $data['pagination']['current_page'] + 1])) ?>" class="page-btn">➡️</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>