<!-- ================================================
     views/product/index.php
     صفحة عرض المنتجات - تصميم احترافي تفاعلي
     ================================================ -->

<div class="content-header">
    <div>
        <h1>
            <span class="header-icon">📦</span>
            إدارة المنتجات
        </h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>">
                <span>🏠</span> الرئيسية
            </a>
            <span class="separator">›</span>
            <span class="current">المنتجات</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>product/add" class="btn btn-primary btn-lg pulse-animation">
        <span>➕</span> إضافة منتج جديد
    </a>
</div>

<div class="content-body">
    
    <!-- ========== الإحصائيات العلوية ========== -->
    <div class="stats-grid">
        <div class="stat-card gradient-blue">
            <div class="stat-icon-wrapper">
                <span class="stat-icon">📦</span>
            </div>
            <div class="stat-content">
                <span class="stat-value" id="totalProducts">0</span>
                <span class="stat-label">إجمالي المنتجات</span>
            </div>
            <div class="stat-bg-icon">📦</div>
        </div>
        
        <div class="stat-card gradient-green">
            <div class="stat-icon-wrapper">
                <span class="stat-icon">✅</span>
            </div>
            <div class="stat-content">
                <span class="stat-value" id="activeProducts">0</span>
                <span class="stat-label">منتجات نشطة</span>
            </div>
            <div class="stat-bg-icon">✅</div>
        </div>
        
        <div class="stat-card gradient-orange">
            <div class="stat-icon-wrapper">
                <span class="stat-icon">📊</span>
            </div>
            <div class="stat-content">
                <span class="stat-value" id="totalStock">0</span>
                <span class="stat-label">إجمالي المخزون</span>
            </div>
            <div class="stat-bg-icon">📊</div>
        </div>
        
        <div class="stat-card gradient-purple">
            <div class="stat-icon-wrapper">
                <span class="stat-icon">🏷️</span>
            </div>
            <div class="stat-content">
                <span class="stat-value" id="totalVariants">0</span>
                <span class="stat-label">إجمالي المتغيرات</span>
            </div>
            <div class="stat-bg-icon">🏷️</div>
        </div>
    </div>

    <!-- ========== جدول المنتجات مع DataTables ========== -->
    <div class="card product-table-card">
        <div class="card-header-modern">
            <div class="header-left">
                <h2>
                    <span>📋</span> قائمة المنتجات
                </h2>
                <div class="table-info-badge" id="tableInfo">جاري التحميل...</div>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" 
                           id="searchInput" 
                           placeholder="ابحث عن منتج..." 
                           class="search-input-modern">
                </div>
                <select id="statusFilter" class="filter-select-modern">
                    <option value="">📋 كل الحالات</option>
                    <option value="1">✅ نشط</option>
                    <option value="0">❌ غير نشط</option>
                </select>
                <button id="refreshTable" class="btn-icon" title="تحديث">
                    🔄
                </button>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table id="productsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="80">الصورة</th>
                        <th>المنتج</th>
                        <th>العلامة</th>
                        <th>الأقسام</th>
                        <th>المتغيرات</th>
                        <th>نطاق السعر</th>
                        <th>المخزون</th>
                        <th>الحالة</th>
                        <th width="120">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <!-- سيتم ملؤها عبر AJAX -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination محسنة -->
        <div class="pagination-wrapper">
            <div class="showing-info" id="showingInfo"></div>
            <div class="pagination-buttons" id="paginationButtons"></div>
        </div>
    </div>
</div>

<!-- ================================================
     JavaScript Libraries & Custom Code
     ================================================ -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
// ============================================
// إعدادات عامة
// ============================================
const BASE_URL = '<?= BASE_URL ?>';
let currentPage = 1;
let currentStatus = '';
let currentSearch = '';
let productsData = [];

// ============================================
// عند تحميل الصفحة
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadStats();
    
    // حدث البحث
    document.getElementById('searchInput').addEventListener('input', debounce(function() {
        currentSearch = this.value;
        currentPage = 1;
        loadProducts();
    }, 300));
    
    // حدث الفلتر
    document.getElementById('statusFilter').addEventListener('change', function() {
        currentStatus = this.value;
        currentPage = 1;
        loadProducts();
    });
    
    // حدث التحديث
    document.getElementById('refreshTable').addEventListener('click', function() {
        this.style.transform = 'rotate(360deg)';
        setTimeout(() => {
            this.style.transform = 'rotate(0deg)';
        }, 600);
        loadProducts();
        loadStats();
    });
});

// ============================================
// دالة جلب المنتجات عبر AJAX
// ============================================
async function loadProducts() {
    showLoader();
    
    try {
        const params = new URLSearchParams({
            page: currentPage,
            status: currentStatus,
            search: currentSearch
        });
        
        const response = await fetch(`${BASE_URL}api/products?${params}`);
        const data = await response.json();
        
        if (data.success) {
            productsData = data.products;
            renderProducts(data.products);
            renderPagination(data.pagination);
            updateShowingInfo(data.pagination);
        } else {
            showError('فشل تحميل المنتجات');
        }
    } catch (error) {
        showError('حدث خطأ في الاتصال');
    } finally {
        hideLoader();
    }
}

// ============================================
// دالة جلب الإحصائيات
// ============================================
async function loadStats() {
    try {
        const response = await fetch(`${BASE_URL}api/stats/products`);
        const data = await response.json();
        
        if (data.success) {
            animateValue('totalProducts', 0, data.total_products, 1000);
            animateValue('activeProducts', 0, data.active_products, 1000);
            animateValue('totalStock', 0, data.total_stock, 1000);
            animateValue('totalVariants', 0, data.total_variants, 1000);
        }
    } catch (error) {
        console.error('خطأ في تحميل الإحصائيات:', error);
    }
}

// ============================================
// عرض المنتجات في الجدول
// ============================================
function renderProducts(products) {
    const tbody = document.getElementById('productsTableBody');
    
    if (products.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10">
                    <div class="empty-state-modern">
                        <span class="empty-icon">📭</span>
                        <h3>لا توجد منتجات</h3>
                        <p>ابدأ بإضافة منتجك الأول إلى المتجر</p>
                        <a href="${BASE_URL}product/add" class="btn btn-primary">
                            <span>➕</span> إضافة منتج جديد
                        </a>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = products.map((product, index) => `
        <tr class="product-row" data-id="${product.product_id}">
            <td class="row-number">${(currentPage - 1) * 10 + index + 1}</td>
            <td>
                <div class="product-thumb-modern">
                    ${product.base_image_url 
                        ? `<img src="${BASE_URL}${product.base_image_url}" alt="${product.product_name}" loading="lazy">`
                        : `<span class="no-image">📷</span>`
                    }
                </div>
            </td>
            <td>
                <div class="product-name-cell">
                    <span class="product-name-text">${product.product_name}</span>
                    ${product.description 
                        ? `<span class="product-desc-text">${product.description.substring(0, 50)}...</span>`
                        : ''
                    }
                </div>
            </td>
            <td>
                <span class="brand-badge">
                    ${product.brand_name || 'بدون'}
                </span>
            </td>
            <td>
                <div class="categories-cell">
                    ${product.categories_names 
                        ? product.categories_names.split(',').map(cat => 
                            `<span class="category-chip">${cat.trim()}</span>`
                        ).join('')
                        : '<span class="no-data">—</span>'
                    }
                </div>
            </td>
            <td>
                <span class="variant-count-badge">
                    ${product.variant_count} متغير
                </span>
            </td>
            <td>
                <div class="price-range">
                    ${product.min_price === product.max_price
                        ? `<span class="single-price">${formatMoney(product.min_price)}</span>`
                        : `<span class="min-price">${formatMoney(product.min_price)}</span>
                           <span class="price-separator">-</span>
                           <span class="max-price">${formatMoney(product.max_price)}</span>`
                    }
                </div>
            </td>
            <td>
                <div class="stock-indicator">
                    <div class="stock-bar-wrapper">
                        <div class="stock-bar ${getStockClass(product.total_stock)}" 
                             style="width: ${getStockPercentage(product.total_stock)}%"></div>
                    </div>
                    <span class="stock-text ${getStockClass(product.total_stock)}">
                        ${product.total_stock > 0 ? product.total_stock : 'نفذ'}
                    </span>
                </div>
            </td>
            <td>
                <button onclick="toggleProductStatus(${product.product_id}, this)" 
                        class="toggle-btn-modern ${product.is_active == 1 ? 'active' : ''}"
                        data-status="${product.is_active}">
                    <span class="toggle-circle"></span>
                </button>
            </td>
            <td>
                <div class="action-buttons-modern">
                    <button onclick="viewProduct(${product.product_id})" 
                            class="btn-action" title="عرض">
                        <span>👁️</span>
                    </button>
                    <button onclick="editProduct(${product.product_id})" 
                            class="btn-action edit" title="تعديل">
                        <span>✏️</span>
                    </button>
                    <button onclick="deleteProduct(${product.product_id})" 
                            class="btn-action delete" title="حذف">
                        <span>🗑️</span>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// ============================================
// Pagination
// ============================================
function renderPagination(pagination) {
    const container = document.getElementById('paginationButtons');
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Previous
    html += `
        <button onclick="goToPage(${currentPage - 1})" 
                class="page-btn" ${currentPage === 1 ? 'disabled' : ''}>
            ⬅️
        </button>
    `;
    
    // Pages
    for (let i = 1; i <= pagination.total_pages; i++) {
        if (i === 1 || i === pagination.total_pages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `
                <button onclick="goToPage(${i})" 
                        class="page-btn ${i === currentPage ? 'active' : ''}">
                    ${i}
                </button>
            `;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += '<span class="page-dots">...</span>';
        }
    }
    
    // Next
    html += `
        <button onclick="goToPage(${currentPage + 1})" 
                class="page-btn" ${currentPage === pagination.total_pages ? 'disabled' : ''}>
            ➡️
        </button>
    `;
    
    container.innerHTML = html;
}

// ============================================
// تغيير الصفحة
// ============================================
function goToPage(page) {
    currentPage = page;
    loadProducts();
    document.querySelector('.product-table-card').scrollIntoView({ behavior: 'smooth' });
}

// ============================================
// تبديل حالة المنتج
// ============================================
async function toggleProductStatus(productId, button) {
    const currentStatus = button.dataset.status;
    const newStatus = currentStatus == 1 ? 0 : 1;
    
    // تأثير بصري فوري
    button.classList.toggle('active');
    button.dataset.status = newStatus;
    
    try {
        const response = await fetch(`${BASE_URL}api/product/toggle-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, is_active: newStatus })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(
                newStatus == 1 ? '✅ تم تنشيط المنتج' : '⏸️ تم تعطيل المنتج',
                'success'
            );
            loadStats();
        } else {
            // ارجاع التغيير
            button.classList.toggle('active');
            button.dataset.status = currentStatus;
            showToast('❌ فشل تحديث الحالة', 'error');
        }
    } catch (error) {
        button.classList.toggle('active');
        button.dataset.status = currentStatus;
        showToast('❌ حدث خطأ', 'error');
    }
}

// ============================================
// حذف منتج
// ============================================
async function deleteProduct(productId) {
    const confirmed = await showConfirm(
        '🗑️ تأكيد الحذف',
        'هل أنت متأكد من حذف هذا المنتج؟\nلا يمكن التراجع عن هذا الإجراء!'
    );
    
    if (!confirmed) return;
    
    try {
        const response = await fetch(`${BASE_URL}api/product/delete/${productId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ تم حذف المنتج بنجاح', 'success');
            loadProducts();
            loadStats();
        } else {
            showToast('❌ فشل حذف المنتج', 'error');
        }
    } catch (error) {
        showToast('❌ حدث خطأ', 'error');
    }
}

// ============================================
// إجراءات أخرى
// ============================================
function viewProduct(id) {
    window.location.href = `${BASE_URL}product/view/${id}`;
}

function editProduct(id) {
    window.location.href = `${BASE_URL}product/edit/${id}`;
}

// ============================================
// دوال مساعدة
// ============================================
function formatMoney(amount) {
    return new Intl.NumberFormat('ar-SA').format(amount) + ' ريال';
}

function getStockClass(stock) {
    if (stock > 20) return 'stock-high';
    if (stock > 5) return 'stock-medium';
    return 'stock-low';
}

function getStockPercentage(stock) {
    return Math.min(stock / 50 * 100, 100);
}

function animateValue(id, start, end, duration) {
    const element = document.getElementById(id);
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            element.textContent = Math.round(end);
            clearInterval(timer);
        } else {
            element.textContent = Math.round(current);
        }
    }, 16);
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function showLoader() {
    document.getElementById('productsTableBody').innerHTML = `
        <tr>
            <td colspan="10">
                <div class="loader-wrapper">
                    <div class="loader"></div>
                    <p>جاري تحميل المنتجات...</p>
                </div>
            </td>
        </tr>
    `;
}

function hideLoader() {}

function showError(message) {
    document.getElementById('productsTableBody').innerHTML = `
        <tr>
            <td colspan="10">
                <div class="error-state">
                    <span>⚠️</span>
                    <p>${message}</p>
                    <button onclick="loadProducts()" class="btn btn-primary">🔄 إعادة المحاولة</button>
                </div>
            </td>
        </tr>
    `;
}

// ============================================
// Toast Notifications
// ============================================
function showToast(message, type = 'success') {
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "bottom",
        position: "left",
        stopOnFocus: true,
        style: {
            background: type === 'success' 
                ? 'linear-gradient(135deg, #10b981, #059669)'
                : 'linear-gradient(135deg, #ef4444, #dc2626)',
            borderRadius: '12px',
            fontFamily: "'Cairo', sans-serif",
            fontSize: '16px',
            padding: '16px 24px',
        }
    }).showToast();
}

// ============================================
// Confirm Dialog محسن
// ============================================
function showConfirm(title, message) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        
        overlay.innerHTML = `
            <div class="confirm-dialog">
                <div class="confirm-icon">🗑️</div>
                <h3>${title}</h3>
                <p>${message.replace(/\n/g, '<br>')}</p>
                <div class="confirm-actions">
                    <button class="btn btn-secondary confirm-cancel">إلغاء</button>
                    <button class="btn btn-danger confirm-ok">تأكيد الحذف</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        overlay.querySelector('.confirm-cancel').onclick = () => {
            overlay.remove();
            resolve(false);
        };
        
        overlay.querySelector('.confirm-ok').onclick = () => {
            overlay.remove();
            resolve(true);
        };
        
        overlay.onclick = (e) => {
            if (e.target === overlay) {
                overlay.remove();
                resolve(false);
            }
        };
    });
}

function updateShowingInfo(pagination) {
    const start = (pagination.current_page - 1) * pagination.per_page + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    document.getElementById('showingInfo').textContent = 
        `عرض ${start}-${end} من ${pagination.total} منتج`;
}
</script>