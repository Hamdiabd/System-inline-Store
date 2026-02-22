<!-- رأس الصفحة -->
<div class="page-header">
    <h1>
        <i class="fas fa-plus-circle"></i>
        إضافة منتج جديد
    </h1>
    <a href="<?= BASE_URL ?>product/index" class="btn btn-secondary">
        <i class="fas fa-arrow-right"></i>
        عودة
    </a>
</div>

<!-- عرض الأخطاء إن وجدت -->
<?php if(isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <div class="alert-content">
            <strong>الرجاء تصحيح الأخطاء التالية:</strong>
            <ul>
                <?php foreach($_SESSION['error'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- نموذج إضافة المنتج -->
<form action="<?= BASE_URL ?>product/store" 
      method="POST" 
      enctype="multipart/form-data" 
      class="product-form"
      id="productForm">
    
    <div class="form-grid">
        <!-- العمود الأول -->
        <div class="form-column">
            <!-- اسم المنتج -->
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-tag"></i>
                    اسم المنتج <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control <?= isset($_SESSION['old']['name']) ? 'is-valid' : '' ?>" 
                       value="<?= $_SESSION['old']['name'] ?? '' ?>" 
                       placeholder="أدخل اسم المنتج"
                       required
                       autofocus>
                <small class="form-text">أدخل اسماً واضحاً للمنتج</small>
            </div>

            <!-- السعر -->
            <div class="form-group">
                <label for="price">
                    <i class="fas fa-dollar-sign"></i>
                    السعر <span class="required">*</span>
                </label>
                <input type="number" 
                       id="price" 
                       name="price" 
                       step="0.01" 
                       min="0" 
                       class="form-control" 
                       value="<?= $_SESSION['old']['price'] ?? '' ?>" 
                       placeholder="0.00"
                       required>
            </div>

            <!-- المخزون -->
            <div class="form-group">
                <label for="stock">
                    <i class="fas fa-boxes"></i>
                    المخزون
                </label>
                <input type="number" 
                       id="stock" 
                       name="stock" 
                       min="0" 
                       class="form-control" 
                       value="<?= $_SESSION['old']['stock'] ?? 0 ?>" 
                       placeholder="0">
            </div>
        </div>

        <!-- العمود الثاني -->
        <div class="form-column">
            <!-- التصنيف -->
            <div class="form-group">
                <label for="category_id">
                    <i class="fas fa-folder"></i>
                    التصنيف
                </label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">-- اختر تصنيف --</option>
                    <?php foreach($data['categories'] as $category): ?>
                        <option value="<?= $category->id ?>" 
                            <?= (isset($_SESSION['old']['category_id']) && $_SESSION['old']['category_id'] == $category->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- العلامة التجارية -->
            <div class="form-group">
                <label for="brand_id">
                    <i class="fas fa-copyright"></i>
                    العلامة التجارية
                </label>
                <select id="brand_id" name="brand_id" class="form-control">
                    <option value="">-- اختر علامة تجارية --</option>
                    <?php foreach($data['brands'] as $brand): ?>
                        <option value="<?= $brand->id ?>" 
                            <?= (isset($_SESSION['old']['brand_id']) && $_SESSION['old']['brand_id'] == $brand->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- الحالة -->
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-circle"></i>
                    الحالة
                </label>
                <select id="status" name="status" class="form-control">
                    <option value="active" selected>نشط</option>
                    <option value="out_of_stock">غير متوفر</option>
                    <option value="discontinued">متوقف</option>
                </select>
            </div>
        </div>
    </div>

    <!-- الوصف (يمتد على عمودين) -->
    <div class="form-group full-width">
        <label for="description">
            <i class="fas fa-align-left"></i>
            الوصف
        </label>
        <textarea id="description" 
                  name="description" 
                  class="form-control" 
                  rows="5" 
                  placeholder="أدخل وصفاً تفصيلياً للمنتج"><?= $_SESSION['old']['description'] ?? '' ?></textarea>
    </div>

    <!-- الصورة الرئيسية -->
    <div class="form-group full-width">
        <label for="main_image">
            <i class="fas fa-image"></i>
            الصورة الرئيسية
        </label>
        
        <div class="image-upload-container" id="imageUploadContainer">
            <div class="image-upload-box" id="uploadBox">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>اسحب الصورة هنا أو اضغط للاختيار</p>
                <span class="upload-hint">يدعم: JPG, PNG, GIF - الحد الأقصى 2MB</span>
                <input type="file" 
                        id="main_image" 
                        name="main_image" 
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="file-input"
                        onchange="previewImage(this)">
            </div>
            
            <div id="imagePreview" class="image-preview"></div>
        </div>
    </div>

    <!-- خيارات إضافية -->
    <div class="form-group full-width">
        <label class="checkbox-label">
            <input type="checkbox" name="featured" value="1" 
                <?= isset($_SESSION['old']['featured']) ? 'checked' : '' ?>>
            <span class="checkbox-custom"></span>
            <span class="checkbox-text">
                <i class="fas fa-star"></i>
                منتج مميز (يظهر في الصفحة الرئيسية)
            </span>
        </label>
    </div>

    <!-- أزرار الإرسال -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-large" id="submitBtn">
            <i class="fas fa-save"></i>
            <span>حفظ المنتج</span>
        </button>
        
        <button type="reset" class="btn btn-secondary">
            <i class="fas fa-undo"></i>
            إعادة تعيين
        </button>
        
        <a href="<?= BASE_URL ?>product/index" class="btn btn-outline">
            <i class="fas fa-times"></i>
            إلغاء
        </a>
    </div>
</form>

<!-- JavaScript للصفحة -->
<script>
// معاينة الصورة قبل الرفع
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const uploadBox = document.getElementById('uploadBox');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const file = input.files[0];
        
        // التحقق من حجم الملف (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('حجم الصورة كبير جداً. الحد الأقصى 2MB');
            input.value = '';
            return;
        }
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="preview-item">
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-image" onclick="removeImage()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            uploadBox.style.display = 'none';
        }
        
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('uploadBox').style.display = 'flex';
    document.getElementById('main_image').value = '';
}

// التحقق من صحة النموذج قبل الإرسال
document.getElementById('productForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const price = document.getElementById('price').value.trim();
    const submitBtn = document.getElementById('submitBtn');
    
    let errors = [];
    
    if (!name) {
        errors.push('اسم المنتج مطلوب');
        document.getElementById('name').classList.add('is-invalid');
    }
    
    if (!price) {
        errors.push('السعر مطلوب');
        document.getElementById('price').classList.add('is-invalid');
    } else if (isNaN(price) || price <= 0) {
        errors.push('السعر يجب أن يكون رقماً موجباً');
        document.getElementById('price').classList.add('is-invalid');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
    } else {
        // تغيير زر الإرسال إلى حالة التحميل
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
        submitBtn.disabled = true;
    }
});

// إزالة class is-invalid عند الكتابة
document.getElementById('name').addEventListener('input', function() {
    this.classList.remove('is-invalid');
    
});

document.getElementById('price').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});
</script>

<style>
/* تنسيقات إضافية للنموذج */
.required {
    color: #dc3545;
    margin-right: 3px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark);
}

.form-group label i {
    color: var(--primary);
    margin-left: 5px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: 'Cairo', sans-serif;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: var(--gray);
}

/* صندوق رفع الصور */
.image-upload-container {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    flex-wrap: wrap;
}

.image-upload-box {
    width: 300px;
    height: 200px;
    border: 2px dashed var(--border);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    background: var(--light);
}

.image-upload-box:hover {
    border-color: var(--primary);
    background: rgba(67, 97, 238, 0.05);
}

.image-upload-box i {
    font-size: 40px;
    color: var(--gray);
    margin-bottom: 10px;
}

.image-upload-box p {
    color: var(--dark);
    font-weight: 600;
    margin-bottom: 5px;
}

.upload-hint {
    font-size: 11px;
    color: var(--gray);
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.image-preview {
    flex: 1;
    min-width: 200px;
}

.preview-item {
    position: relative;
    width: 200px;
    height: 200px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.9);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.remove-image:hover {
    background: #dc3545;
    transform: scale(1.1);
}

/* خيارات إضافية */
.checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.checkbox-label input {
    display: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-radius: 5px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label input:checked + .checkbox-custom {
    background: var(--primary);
    border-color: var(--primary);
}

.checkbox-label input:checked + .checkbox-custom::after {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
}

.checkbox-text {
    color: var(--dark);
}

.checkbox-text i {
    color: #ffd700;
}

/* أزرار الإرسال */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid var(--border);
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-secondary {
    background: var(--light);
    color: var(--dark);
    border: 2px solid var(--border);
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.btn-outline {
    background: transparent;
    color: var(--danger);
    border: 2px solid var(--danger);
}

.btn-outline:hover {
    background: var(--danger);
    color: white;
}

.btn-large {
    padding: 12px 40px;
}

/* التنبيهات */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    gap: 15px;
    animation: slideDown 0.3s ease;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-right: 4px solid #dc3545;
}

.alert i {
    font-size: 20px;
}

.alert-content strong {
    display: block;
    margin-bottom: 5px;
}

.alert-content ul {
    margin: 0;
    padding-right: 20px;
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

/* رأس الصفحة */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border);
}

.page-header h1 {
    font-size: 24px;
    color: var(--dark);
}

.page-header h1 i {
    color: var(--primary);
    margin-left: 10px;
}

/* للجوال */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .image-upload-box {
        width: 100%;
    }
}
</style>