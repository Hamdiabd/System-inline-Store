<?php
// استقبال المتغيرات من الكنترولر
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
        <h1>
            <i class="fas fa-boxes"></i>
            <?= htmlspecialchars($title) ?>
        </h1>
        <div class="header-actions">
            <a href="<?= BASE_URL ?>admin/product/create" class="btn-primary">
                <i class="fas fa-plus-circle"></i>
                إضافة منتج جديد
            </a>
        </div>
    </div>
    
    <!-- بطاقات الإحصائيات -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-cube"></i>
            </div>
            <div class="stat-info">
                <h3>إجمالي المنتجات</h3>
                <p><?= $totalProducts ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <h3>قطع في المخزون</h3>
                <p><?= $totalStock ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>منخفض المخزون</h3>
                <p><?= $lowStock ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>نافد من المخزون</h3>
                <p><?= $outOfStock ?></p>
            </div>
        </div>
    </div>
</div>


<!-- رسائل التنبيه -->
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
        <span><?= $_SESSION['error'] ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- شريط البحث والفلترة -->
<div class="search-bar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="بحث عن منتج..." onkeyup="searchProducts()">
    </div>
    
    <div class="filter-options">
        <select id="statusFilter" onchange="filterProducts()">
            <option value="all">جميع الحالات</option>
            <option value="active">نشط</option>
            <option value="out_of_stock">نفذ من المخزون</option>
            <option value="discontinued">متوقف</option>
        </select>
        
        <select id="stockFilter" onchange="filterProducts()">
            <option value="all">كل المخزون</option>
            <option value="low">منخفض (أقل من 10)</option>
            <option value="out">نافد</option>
            <option value="available">متوفر</option>
        </select>
    </div>
</div>

<!-- جدول المنتجات -->
<div class="table-responsive">
    <table class="admin-table" id="productsTable">
        <thead>
            <tr>
                <th>الصورة</th>
                <th>المنتج</th>
                <th>السعر</th>
                <th>المخزون</th>
                <th>التصنيف</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($products)): ?>
                <tr>
                    <td colspan="7" class="empty-table">
                        <i class="fas fa-box-open"></i>
                        <p>لا توجد منتجات</p>
                        <a href="<?= BASE_URL ?>admin/product/create" class="btn-small">إضافة منتج</a>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                <tr class="product-row" 
                    data-name="<?= strtolower($product->name) ?>"
                    data-status="<?= $product->status ?>"
                    data-stock="<?= $product->stock ?>">
                    
                    <td class="product-image">
                        <img src="<?= BASE_URL ?>uploads/products/<?= $product->primary_image ?? 'default.jpg' ?>" 
                             alt="<?= $product->name ?>">
                    </td>
                    
                    <td class="product-name">
                        <strong><?= htmlspecialchars($product->name) ?></strong>
                        <small>ID: <?= $product->id ?></small>
                    </td>
                    
                    <td class="product-price">
                        $<?= number_format($product->price, 2) ?>
                    </td>
                    
                    <td class="product-stock">
                        <?php if($product->stock <= 0): ?>
                            <span class="badge badge-danger">نافد</span>
                        <?php elseif($product->stock <= 10): ?>
                            <span class="badge badge-warning"><?= $product->stock ?> قطع</span>
                        <?php else: ?>
                            <span class="badge badge-success"><?= $product->stock ?> قطع</span>
                        <?php endif; ?>
                    </td>
                    
                    <td><?= $product->category_name ?? 'غير مصنف' ?></td>
                    
                    <td>
                        <?php if($product->status == 'active'): ?>
                            <span class="badge badge-success">نشط</span>
                        <?php elseif($product->status == 'out_of_stock'): ?>
                            <span class="badge badge-warning">غير متوفر</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">متوقف</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="actions">
                        <a href="<?= BASE_URL ?>admin/product/edit/<?= $product->id ?>" 
                           class="btn-action btn-edit" 
                           title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <button class="btn-action btn-status" 
                                title="تغيير الحالة"
                                onclick="toggleStatus(<?= $product->id ?>, this)">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        
                        <button class="btn-action btn-delete" 
                                title="حذف"
                                onclick="confirmDelete(<?= $product->id ?>, '<?= $product->name ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <a href="<?= BASE_URL ?>admin/product/view/<?= $product->id ?>" 
                           class="btn-action btn-view" 
                           title="عرض">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- نموذج حذف مخفي (لإرسال POST) -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
// ===== البحث في الجدول =====
function searchProducts() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.product-row');
    
    rows.forEach(row => {
        const name = row.dataset.name;
        if(name.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ===== فلترة المنتجات =====
function filterProducts() {
    const statusFilter = document.getElementById('statusFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    const rows = document.querySelectorAll('.product-row');
    
    rows.forEach(row => {
        let show = true;
        
        // فلترة حسب الحالة
        if(statusFilter !== 'all') {
            if(row.dataset.status !== statusFilter) {
                show = false;
            }
        }
        
        // فلترة حسب المخزون
        if(stockFilter !== 'all') {
            const stock = parseInt(row.dataset.stock);
            if(stockFilter === 'low' && (stock >= 10 || stock <= 0)) show = false;
            if(stockFilter === 'out' && stock > 0) show = false;
            if(stockFilter === 'available' && stock <= 0) show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

// ===== تأكيد الحذف (مع SweetAlert) =====
function confirmDelete(id, name) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        html: `سيتم حذف المنتج <strong>${name}</strong> بشكل نهائي`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        background: document.body.classList.contains('dark-theme') ? '#2f3542' : '#fff',
        color: document.body.classList.contains('dark-theme') ? '#fff' : '#333'
    }).then((result) => {
        if (result.isConfirmed) {
            // إنشاء نموذج وإرسال POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= BASE_URL ?>admin/product/delete/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ===== تغيير حالة المنتج (AJAX) =====
function toggleStatus(id, btn) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('<?= BASE_URL ?>admin/product/toggleStatus/' + id, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // تحديث واجهة المستخدم
            const row = btn.closest('tr');
            const statusCell = row.querySelector('td:nth-child(6)');
            
            if(data.newStatus === 'active') {
                statusCell.innerHTML = '<span class="badge badge-success">نشط</span>';
            } else {
                statusCell.innerHTML = '<span class="badge badge-secondary">متوقف</span>';
            }
            
            // تحديث dataset
            row.dataset.status = data.newStatus;
            
            // رسالة نجاح
            Swal.fire({
                icon: 'success',
                title: 'تم التحديث',
                text: 'تم تغيير حالة المنتج بنجاح',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// إخفاء التنبيهات بعد 5 ثواني
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    });
}, 5000);
</script>

<style>
/* ===== تنسيقات صفحة المنتجات للمدير ===== */
.admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    color: white;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header-content h1 {
    font-size: 28px;
    margin: 0;
}

.header-content h1 i {
    margin-left: 10px;
}

.btn-primary {
    background: white;
    color: #667eea;
    padding: 12px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-icon.bg-primary { background: #667eea; }
.stat-icon.bg-success { background: #2ed573; }
.stat-icon.bg-warning { background: #ffa502; }

.stat-info h3 {
    font-size: 14px;
    margin-bottom: 5px;
    opacity: 0.9;
}

.stat-info p {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

/* ===== التنبيهات ===== */
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease;
    position: relative;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-right: 4px solid #28a745;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-right: 4px solid #dc3545;
}

.alert-close {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    opacity: 0.5;
}

.alert-close:hover {
    opacity: 1;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== شريط البحث ===== */
.search-bar {
    background: var(--card-bg);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
    box-shadow: var(--card-shadow);
}

.search-box {
    flex: 1;
    position: relative;
    min-width: 250px;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

.search-box input {
    width: 100%;
    padding: 12px 45px 12px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: 14px;
}

.search-box input:focus {
    outline: none;
    border-color: #667eea;
}

.filter-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-options select {
    padding: 12px 25px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-primary);
    color: var(--text-primary);
    cursor: pointer;
    font-family: 'Cairo', sans-serif;
}

/* ===== الجدول ===== */
.table-responsive {
    overflow-x: auto;
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: var(--card-shadow);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.admin-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.admin-table th {
    padding: 15px;
    text-align: right;
    font-weight: 600;
    font-size: 14px;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

.admin-table tbody tr {
    transition: all 0.3s ease;
}

.admin-table tbody tr:hover {
    background: var(--bg-primary);
}

.product-image img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.product-name {
    font-weight: 600;
}

.product-name small {
    display: block;
    color: var(--text-secondary);
    font-size: 12px;
    margin-top: 5px;
}

.product-price {
    font-weight: 700;
    color: #667eea;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
}

/* ===== أزرار الإجراءات ===== */
.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-action {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 14px;
}

.btn-edit {
    background: #ffa502;
}

.btn-delete {
    background: #ff4757;
}

.btn-view {
    background: #2ed573;
}

.btn-status {
    background: #667eea;
}

.btn-action:hover {
    transform: translateY(-3px);
    filter: brightness(1.1);
}

/* ===== الحالة الفارغة ===== */
.empty-table {
    text-align: center;
    padding: 60px !important;
}

.empty-table i {
    font-size: 60px;
    color: var(--text-secondary);
    margin-bottom: 20px;
    display: block;
}

.empty-table p {
    font-size: 18px;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.btn-small {
    background: #667eea;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
}

/* ===== الوضع الليلي ===== */
.dark-theme .btn-primary {
    background: #2f3542;
    color: white;
}

.dark-theme .badge-success {
    background: #1e2b1e;
    color: #2ed573;
}

.dark-theme .badge-warning {
    background: #2b211b;
    color: #ffa502;
}

.dark-theme .badge-danger {
    background: #2b1b1b;
    color: #ff4757;
}
</style>