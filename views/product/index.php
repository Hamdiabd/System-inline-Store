    <link rel="stylesheet" href="<?= BASE_URL ?>css/indexproduct.css">

<?php
$products = $data['products'] ?? [];
$totalProducts = $data['totalProducts'] ?? 0;
$totalStock = $data['totalStock'] ?? 0;
$outOfStock = $data['outOfStock'] ?? 0;
$lowStock = $data['lowStock'] ?? 0;
$title = $data['title'] ?? 'إدارة المنتجات';
$page = $data['page'] ?? '';
?>

<!-- رأس الصفحة مع إحصائيات -->
<div class="admin-header">
    <div class="header-content">
        <div class="title-section">
            <h1>
                <i class="fas fa-boxes"></i>
                <?= htmlspecialchars($title) ?>
            </h1>
            <p class="text-light">إدارة وتحديث المنتجات المتاحة في المتجر</p>
        </div>
        <div class="header-actions">
            <a href="<?= BASE_URL ?>product/create" class="btn-primary">
                <i class="fas fa-plus-circle"></i>
                إضافة منتج جديد
            </a>
        </div>
    </div>
    
    <!-- بطاقات الإحصائيات -->
    <div class="stats-grid">
        <div class="stat-card" onclick="filterBy('all')">
            <div class="stat-icon bg-primary">
                <i class="fas fa-cube"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">إجمالي المنتجات</span>
                <span class="stat-value"><?= $totalProducts ?></span>
            </div>
        </div>
        
        <div class="stat-card" onclick="filterBy('stock')">
            <div class="stat-icon bg-success">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">قطع في المخزون</span>
                <span class="stat-value"><?= $totalStock ?></span>
            </div>
        </div>
        
        <div class="stat-card" onclick="filterBy('low')">
            <div class="stat-icon bg-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">منخفض المخزون</span>
                <span class="stat-value"><?= intval($lowStock) ?></span>
            </div>
        </div>
        
        <div class="stat-card" onclick="filterBy('out')">
            <div class="stat-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">نافد من المخزون</span>
                <span class="stat-value"><?= intval($outOfStock) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- رسائل التنبيه -->
<div class="alert-container">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <span><?= $_SESSION['success'] ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= is_array($_SESSION['error']) ? implode('<br>', $_SESSION['error']) : $_SESSION['error'] ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</div>

<!-- شريط البحث والفلترة -->
<div class="search-bar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="ابحث باسم المنتج..." onkeyup="searchProducts()">
        <button class="clear-search" onclick="clearSearch()" id="clearSearchBtn" style="display: none;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="filter-options">
        <select id="statusFilter" class="filter-select" onchange="filterProducts()">
            <option value="all">📊 جميع الحالات</option>
            <option value="active">✅ نشط</option>
            <option value="out_of_stock">⚠️ نفذ من المخزون</option>
            <option value="discontinued">⏸️ متوقف</option>
        </select>
        
        <select id="stockFilter" class="filter-select" onchange="filterProducts()">
            <option value="all">📦 كل المخزون</option>
            <option value="low">🔴 منخفض (أقل من 10)</option>
            <option value="out">⭕ نافد</option>
            <option value="available">✅ متوفر</option>
        </select>
        
        <button class="btn-reset" onclick="resetFilters()" title="إعادة ضبط الفلاتر">
            <i class="fas fa-undo-alt"></i>
        </button>
    </div>
</div>

<!-- إحصائيات البحث -->
<div class="search-stats" id="searchStats" style="display: none;">
    <span id="visibleCount">0</span> منتج من أصل <span id="totalCount"><?= count($products) ?></span>
    <button class="clear-filters" onclick="resetFilters()">إعادة ضبط</button>
</div>

<!-- جدول المنتجات -->
<div class="table-responsive">
    <table class="admin-table" id="productsTable">
        <thead>
            <tr>
                <th width="80">الصورة</th>
                <th>المنتج</th>
                <th width="100">السعر</th>
                <th width="100">المخزون</th>
                <th>التصنيف</th>
                <th width="100">الحالة</th>
                <th width="220">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($products)): ?>
                <tr>
                    <td colspan="7" class="empty-table">
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3>لا توجد منتجات</h3>
                            <p>ابدأ بإضافة منتج جديد إلى المتجر</p>
                            <a href="<?= BASE_URL ?>product/create" class="btn-primary">
                                <i class="fas fa-plus-circle"></i>
                                إضافة منتج
                            </a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($products as $product): 
                    $image = $product->main_image ?? $product->primary_image ?? 'default.jpg';
                    $stockClass = $product->stock <= 0 ? 'danger' : ($product->stock <= 10 ? 'warning' : 'success');
                ?>
                <tr class="product-row" 
                    data-id="<?= $product->id ?>"
                    data-name="<?= strtolower($product->name) ?>"
                    data-category="<?= strtolower($product->category_name ?? '') ?>"
                    data-status="<?= $product->status ?>"
                    data-stock="<?= $product->stock ?>">
                    
                    <td class="product-image">
                        <img src="<?= BASE_URL ?>uploads/products/<?= $image ?>" 
                             alt="<?= $product->name ?>"
                             loading="lazy"
                             onerror="this.src='<?= BASE_URL ?>uploads/products/default.jpg'">
                    </td>
                    
                    <td class="product-name">
                        <strong><?= htmlspecialchars($product->name) ?></strong>
                        <small>#<?= $product->id ?></small>
                    </td>
                    
                    <td class="product-price">
                        <strong><?= number_format($product->price, 2) ?></strong>
                        <small>ر.س</small>
                    </td>
                    
                    <td class="product-stock">
                        <div class="stock-indicator">
                            <span class="badge badge-<?= $stockClass ?>">
                                <?= $product->stock > 0 ? $product->stock . ' قطع' : 'نافد' ?>
                            </span>
                            <?php if($product->stock > 0 && $product->stock <= 10): ?>
                                <span class="stock-warning">مخزون منخفض</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    
                    <td>
                        <span class="category-tag">
                            <?= htmlspecialchars($product->category_name ?? 'غير مصنف') ?>
                        </span>
                    </td>
                    
                    <td>
                        <span class="status-badge status-<?= $product->status ?>">
                            <?php if($product->status == 'active'): ?>
                                <i class="fas fa-circle"></i> نشط
                            <?php elseif($product->status == 'out_of_stock'): ?>
                                <i class="fas fa-exclamation-triangle"></i> غير متوفر
                            <?php else: ?>
                                <i class="fas fa-pause-circle"></i> متوقف
                            <?php endif; ?>
                        </span>
                    </td>
                    
                    <td class="actions">
                        <div class="action-group">
                          <button class="btn-icon btn-view" 
        title="عرض التفاصيل"
        data-tooltip
        onclick="viewProductDetails(<?= $product->id ?>)">
    <i class="fas fa-eye"></i>
</button>
                            
                            <a href="<?= BASE_URL ?>product/edit/<?= $product->id ?>" 
                               class="btn-icon btn-edit" 
                               title="تعديل المنتج"
                               data-tooltip>
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <button class="btn-icon btn-status" 
                                    title="تغيير الحالة"
                                    data-tooltip
                                    onclick="quickToggleStatus(<?= $product->id ?>, this)">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            
                            <a href="<?= BASE_URL ?>product/delete/<?= $product->id ?>" 
                               class="btn-icon btn-delete" 
                               title="حذف المنتج"
                               data-tooltip
                               onclick="return confirmDelete(<?= $product->id ?>, '<?= addslashes($product->name) ?>')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination إذا كان هناك الكثير من المنتجات -->
<?php if(isset($data['totalPages']) && $data['totalPages'] > 1): ?>
<div class="pagination">
    <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
        <a href="?page=<?= $i ?>" class="page-link <?= ($data['currentPage'] == $i) ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<!-- ===== نافذة عرض تفاصيل المنتج ===== -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-box-open"></i>
                <span id="modalTitle">تفاصيل المنتج</span>
            </h2>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="modalBody">
            <!-- محتوى المنتج سيتم إضافته هنا بواسطة JavaScript -->
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>جاري تحميل البيانات...</p>
            </div>
        </div>
        
        <div class="modal-footer">
            <button class="btn-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
                إغلاق
            </button>
            <a href="#" id="modalEditBtn" class="btn-edit-product">
                <i class="fas fa-edit"></i>
                تعديل المنتج
            </a>
        </div>
    </div>
</div>
<!-- تضمين SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ===== المتغيرات العامة =====
const BASE_URL = '<?= BASE_URL ?>';
let debounceTimer;
// ===== عرض تفاصيل المنتج في نافذة منبثقة =====
function viewProductDetails(id) {
    // إظهار الـ Modal مع شاشة التحميل
    const modal = document.getElementById('productModal');
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.style.display = 'flex';
    modalBody.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>جاري تحميل بيانات المنتج...</p>
        </div>
    `;
    
    // جلب بيانات المنتج عبر AJAX
    fetch(BASE_URL + 'product/getDetails/' + id, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            displayProductDetails(data.product);
            // تحديث رابط التعديل
            document.getElementById('modalEditBtn').href = BASE_URL + 'product/edit/' + id;
        } else {
            modalBody.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>حدث خطأ في تحميل بيانات المنتج</p>
                </div>
            `;
        }
    })
    .catch(error => {
        modalBody.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <p>فشل الاتصال بالخادم</p>
            </div>
        `;
    });
}

// ===== عرض بيانات المنتج في الـ Modal =====
function displayProductDetails(product) {
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');
    
    modalTitle.textContent = product.name;
    
    // تحديد حالة المخزون
    let stockStatus = '';
    let stockClass = '';
    if(product.stock <= 0) {
        stockStatus = 'نافد من المخزون';
        stockClass = 'danger';
    } else if(product.stock <= 10) {
        stockStatus = 'مخزون منخفض';
        stockClass = 'warning';
    } else {
        stockStatus = 'متوفر';
        stockClass = 'success';
    }
    
    // تحديد حالة المنتج
    let statusText = '';
    let statusClass = '';
    if(product.status == 'active') {
        statusText = 'نشط';
        statusClass = 'success';
    } else if(product.status == 'out_of_stock') {
        statusText = 'غير متوفر';
        statusClass = 'warning';
    } else {
        statusText = 'متوقف';
        statusClass = 'secondary';
    }
    
    // صياغة التاريخ
    const createdDate = product.created_at ? new Date(product.created_at).toLocaleDateString('ar-SA') : 'غير محدد';
    
    modalBody.innerHTML = `
        <div class="product-details">
            <!-- صورة المنتج -->
            <div class="detail-image">
                <img src="${BASE_URL}uploads/products/${product.main_image || product.primary_image || 'default.jpg'}" 
                     alt="${product.name}"
                     onerror="this.src='${BASE_URL}uploads/products/default.jpg'">
            </div>
            
            <!-- معلومات أساسية -->
            <div class="detail-section">
                <h3><i class="fas fa-info-circle"></i> معلومات أساسية</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">المنتج:</span>
                        <span class="detail-value">${product.name}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">السعر:</span>
                        <span class="detail-value price">${Number(product.price).toFixed(2)} ر.س</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">المخزون:</span>
                        <span class="detail-value">
                            <span class="badge-${stockClass}">${product.stock} قطع - ${stockStatus}</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">الحالة:</span>
                        <span class="detail-value">
                            <span class="badge-${statusClass}">${statusText}</span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- التصنيف والعلامة التجارية -->
            <div class="detail-section">
                <h3><i class="fas fa-tags"></i> التصنيف</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">التصنيف:</span>
                        <span class="detail-value">${product.category_name || 'غير مصنف'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">العلامة التجارية:</span>
                        <span class="detail-value">${product.brand_name || 'غير محدد'}</span>
                    </div>
                </div>
            </div>
            
            <!-- الوصف -->
            <div class="detail-section">
                <h3><i class="fas fa-align-left"></i> الوصف</h3>
                <div class="detail-description">
                    ${product.description ? product.description.replace(/\n/g, '<br>') : 'لا يوجد وصف'}
                </div>
            </div>
            
            <!-- معلومات إضافية -->
            <div class="detail-section">
                <h3><i class="fas fa-clock"></i> معلومات إضافية</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">تاريخ الإضافة:</span>
                        <span class="detail-value">${createdDate}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">آخر تحديث:</span>
                        <span class="detail-value">${product.updated_at ? new Date(product.updated_at).toLocaleDateString('ar-SA') : 'لم يتم التحديث'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">عدد المشاهدات:</span>
                        <span class="detail-value">${product.views || 0}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">منتج مميز:</span>
                        <span class="detail-value">${product.featured == 1 ? 'نعم' : 'لا'}</span>
                    </div>
                </div>
            </div>
            
            <!-- الصور الإضافية إذا وجدت -->
            ${product.images && product.images.length > 0 ? `
            <div class="detail-section">
                <h3><i class="fas fa-images"></i> صور إضافية</h3>
                <div class="additional-images">
                    ${product.images.map(img => `
                        <img src="${BASE_URL}uploads/products/${img.image}" 
                             alt="${product.name}" 
                             onclick="viewImage('${BASE_URL}uploads/products/${img.image}')">
                    `).join('')}
                </div>
            </div>
            ` : ''}
        </div>
    `;
}

// ===== إغلاق الـ Modal =====
function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

// ===== عرض الصورة بحجم كبير =====
function viewImage(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: 'صورة المنتج',
        width: 'auto',
        padding: '0',
        showConfirmButton: false,
        showCloseButton: true,
        background: 'transparent'
    });
}

// ===== إغلاق الـ Modal عند الضغط خارجها =====
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeModal();
    }
}
// ===== البحث في الجدول مع تحسين الأداء =====
function searchProducts() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.product-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.dataset.name;
            const category = row.dataset.category;
            const matches = name.includes(filter) || category.includes(filter);
            
            if(matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // تحديث إحصائيات البحث
        updateSearchStats(visibleCount, rows.length);
        
        // إظهار/إخفاء زر المسح
        document.getElementById('clearSearchBtn').style.display = filter ? 'inline-flex' : 'none';
        
    }, 300);
}

// ===== مسح البحث =====
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').style.display = 'none';
    searchProducts();
}

// ===== تحديث إحصائيات البحث =====
function updateSearchStats(visible, total) {
    const statsDiv = document.getElementById('searchStats');
    if(visible < total) {
        document.getElementById('visibleCount').textContent = visible;
        statsDiv.style.display = 'flex';
    } else {
        statsDiv.style.display = 'none';
    }
}

// ===== فلترة المنتجات =====
function filterProducts() {
    const statusFilter = document.getElementById('statusFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    const rows = document.querySelectorAll('.product-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let show = true;
        const status = row.dataset.status;
        const stock = parseInt(row.dataset.stock);
        
        // فلترة حسب الحالة
        if(statusFilter !== 'all' && status !== statusFilter) {
            show = false;
        }
        
        // فلترة حسب المخزون
        if(show && stockFilter !== 'all') {
            if(stockFilter === 'low' && (stock >= 10 || stock <= 0)) show = false;
            else if(stockFilter === 'out' && stock > 0) show = false;
            else if(stockFilter === 'available' && stock <= 0) show = false;
        }
        
        row.style.display = show ? '' : 'none';
        if(show) visibleCount++;
    });
    
    updateSearchStats(visibleCount, rows.length);
}

// ===== إعادة ضبط جميع الفلاتر =====
function resetFilters() {
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('stockFilter').value = 'all';
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').style.display = 'none';
    
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(row => row.style.display = '');
    
    document.getElementById('searchStats').style.display = 'none';
}

// ===== الفلترة بالنقر على الإحصائيات =====
function filterBy(type) {
    const statusFilter = document.getElementById('statusFilter');
    const stockFilter = document.getElementById('stockFilter');
    
    switch(type) {
        case 'all':
            resetFilters();
            break;
        case 'stock':
            stockFilter.value = 'available';
            break;
        case 'low':
            stockFilter.value = 'low';
            break;
        case 'out':
            stockFilter.value = 'out';
            break;
    }
    
    filterProducts();
}

// ✅ ===== دالة تأكيد الحذف مع SweetAlert =====
function confirmDelete(id, name) {
    Swal.fire({
        title: 'حذف المنتج؟',
        html: `
            <div style="text-align: center;">
                <p>هل أنت متأكد من حذف المنتج:</p>
                <strong style="color: #ff4757; font-size: 18px;">${name}</strong>
                <p style="margin-top: 15px; color: #666;">⚠️ هذا الإجراء لا يمكن التراجع عنه</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4757',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = BASE_URL + 'product/delete/' + id;
        }
    });
    return false; // منع السلوك الافتراضي للرابط
}

// ===== تغيير الحالة السريع =====
function quickToggleStatus(id, btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    fetch(BASE_URL + 'product/toggleStatus/' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const row = btn.closest('tr');
            const statusCell = row.querySelector('.status-badge');
            
            if(data.newStatus === 'active') {
                statusCell.className = 'status-badge status-active';
                statusCell.innerHTML = '<i class="fas fa-circle"></i> نشط';
                row.dataset.status = 'active';
            } else {
                statusCell.className = 'status-badge status-inactive';
                statusCell.innerHTML = '<i class="fas fa-pause-circle"></i> متوقف';
                row.dataset.status = 'inactive';
            }
            
            // Toast message
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث',
                text: 'تم تغيير حالة المنتج بنجاح',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ في تغيير الحالة',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    })
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}

// إخفاء التنبيهات بعد 5 ثواني
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// ===== اختصارات لوحة المفاتيح =====
document.addEventListener('keydown', (e) => {
    // Ctrl + F للبحث
    if(e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    // Esc لإلغاء البحث
    if(e.key === 'Escape') {
        clearSearch();
    }
});
</script>

