<?php
// استدعاء الهيدر
include APP_PATH . 'views/admin/layouts/admin-header.php';

// بيانات الصفحة
$page_title = 'إضافة منتج جديد';
$isEdit = false;
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>إضافة منتج جديد</h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>admin/dashboard">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <a href="<?= BASE_URL ?>admin/products">المنتجات</a>
            <i class="fas fa-chevron-left"></i>
            <span>إضافة منتج جديد</span>
        </div>
    </div>
</div>

<!-- Product Form -->
<div class="product-form-container">
    <!-- Tabs -->
    <div class="form-tabs">
        <button class="tab-btn active" data-tab="basic-info">
            <i class="fas fa-info-circle"></i>
            المعلومات الأساسية
        </button>
        <button class="tab-btn" data-tab="images">
            <i class="fas fa-images"></i>
            الصور
        </button>
        <button class="tab-btn" data-tab="variations">
            <i class="fas fa-list-ul"></i>
            الأصناف
        </button>
        <button class="tab-btn" data-tab="tags">
            <i class="fas fa-tags"></i>
            الوسوم
        </button>
        <button class="tab-btn" data-tab="seo">
            <i class="fas fa-chart-line"></i>
            تحسين محركات البحث
        </button>
    </div>
    
    <!-- Form -->
    <form action="<?= BASE_URL ?>admin/products/store" method="POST" id="productForm" enctype="multipart/form-data">
        <div class="form-content">
            <!-- Basic Info Tab -->
            <div id="basic-info" class="tab-pane active">
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-tag"></i>
                            اسم المنتج <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="productName"
                               class="form-control" 
                               placeholder="أدخل اسم المنتج"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-link"></i>
                            الرابط (Slug)
                        </label>
                        <input type="text" 
                               name="slug" 
                               id="productSlug"
                               class="form-control" 
                               placeholder="product-name">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-dollar-sign"></i>
                            السعر <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="price" 
                               class="form-control" 
                               step="0.01"
                               placeholder="0.00"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-boxes"></i>
                            المخزون
                        </label>
                        <input type="number" 
                               name="stock" 
                               class="form-control" 
                               value="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-folder"></i>
                            التصنيف
                        </label>
                        <select name="category_id" class="form-control select2">
                            <option value="">اختر تصنيف</option>
                            <option value="1">رجالي</option>
                            <option value="2">نسائي</option>
                            <option value="3">أطفال</option>
                            <option value="4">إلكترونيات</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-copyright"></i>
                            العلامة التجارية
                        </label>
                        <select name="brand_id" class="form-control select2">
                            <option value="">اختر علامة تجارية</option>
                            <option value="1">نايك</option>
                            <option value="2">أديداس</option>
                            <option value="3">زارا</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-align-left"></i>
                        الوصف
                    </label>
                    <textarea name="description" 
                              class="form-control" 
                              rows="5" 
                              placeholder="أدخل وصف المنتج"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="switch-label">
                            <i class="fas fa-star"></i>
                            منتج مميز
                        </label>
                        <label class="switch">
                            <input type="checkbox" name="featured">
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="switch-label">
                            <i class="fas fa-eye"></i>
                            حالة المنتج
                        </label>
                        <select name="status" class="form-control">
                            <option value="active">نشط</option>
                            <option value="out_of_stock">غير متوفر</option>
                            <option value="discontinued">متوقف</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Images Tab -->
            <div id="images" class="tab-pane">
                <div class="form-group">
                    <label>صور المنتج</label>
                    <div class="image-upload-container" id="imageContainer">
                        <div class="image-upload-box">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>اضغط أو اسحب الصور هنا</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        يمكنك رفع عدة صور. أول صورة ستكون الصورة الرئيسية
                    </small>
                </div>
            </div>
            
            <!-- Variations Tab -->
            <div id="variations" class="tab-pane">
                <div class="form-group">
                    <label>الأصناف (مقاس - لون)</label>
                    <div id="variationsContainer" class="variations-container">
                        <!-- سيتم إضافة الأصناف هنا ديناميكياً -->
                    </div>
                    
                    <button type="button" class="btn-add-variation" id="addVariation">
                        <i class="fas fa-plus"></i>
                        إضافة صنف جديد
                    </button>
                </div>
            </div>
            
            <!-- Tags Tab -->
            <div id="tags" class="tab-pane">
                <div class="form-group">
                    <label>الوسوم</label>
                    <div class="tags-input-container">
                        <div class="tags-list" id="tagsList">
                            <!-- الوسوم تضاف هنا -->
                        </div>
                        <input type="text" 
                               id="tagsInput" 
                               class="tags-input" 
                               placeholder="اكتب واضغط Enter لإضافة وسم...">
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        كل وسم منفصل بفاصلة أو بالضغط على Enter
                    </small>
                </div>
            </div>
            
            <!-- SEO Tab -->
            <div id="seo" class="tab-pane">
                <div class="form-group">
                    <label>
                        <i class="fas fa-file-signature"></i>
                        عنوان SEO
                    </label>
                    <input type="text" name="meta_title" class="form-control" placeholder="عنوان لنتائج البحث">
                    <small class="form-text text-muted">الافتراضي: اسم المنتج</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-paragraph"></i>
                        وصف SEO
                    </label>
                    <textarea name="meta_description" 
                              class="form-control" 
                              rows="3" 
                              placeholder="وصف لنتائج البحث"></textarea>
                    <small class="form-text text-muted">الافتراضي: وصف المنتج</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-hashtag"></i>
                        كلمات مفتاحية
                    </label>
                    <input type="text" 
                           name="meta_keywords" 
                           class="form-control" 
                           placeholder="منتج, تسوق, تخفيضات">
                    <small class="form-text text-muted">افصل بين الكلمات بفاصلة</small>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    حفظ المنتج
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                    إعادة تعيين
                </button>
                <a href="<?= BASE_URL ?>admin/products" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</div>

<?php
// استدعاء الفوتر
include APP_PATH . 'views/admin/layouts/admin-footer.php';
?>