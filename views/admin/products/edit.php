<?php
$product = $data['product'];
$categories = $data['categories'];
$brands = $data['brands'];
$images = $data['images'];
?>

<div class="form-container">
    <div class="form-header">
        <h2>
            <i class="fas fa-edit"></i>
            تعديل المنتج: <?= htmlspecialchars($product->name) ?>
        </h2>
        <a href="<?= BASE_URL ?>admin/product/index" class="btn-secondary">
            <i class="fas fa-arrow-right"></i>
            عودة
        </a>
    </div>
    
    <form action="<?= BASE_URL ?>admin/product/update/<?= $product->id ?>" 
          method="POST" 
          enctype="multipart/form-data"
          class="product-form">
        
        <div class="form-grid">
            <!-- العمود الأول -->
            <div class="form-column">
                <div class="form-group">
                    <label for="name">اسم المنتج *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($product->name) ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="price">السعر *</label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           step="0.01" 
                           class="form-control" 
                           value="<?= $product->price ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="stock">المخزون</label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           class="form-control" 
                           value="<?= $product->stock ?>">
                </div>
            </div>
            
            <!-- العمود الثاني -->
            <div class="form-column">
                <div class="form-group">
                    <label for="category_id">التصنيف</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">-- اختر تصنيف --</option>
                        <?php foreach($categories as $category): ?>
                        <option value="<?= $category->id ?>" 
                            <?= $product->category_id == $category->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="brand_id">العلامة التجارية</label>
                    <select id="brand_id" name="brand_id" class="form-control">
                        <option value="">-- اختر علامة تجارية --</option>
                        <?php foreach($brands as $brand): ?>
                        <option value="<?= $brand->id ?>" 
                            <?= $product->brand_id == $brand->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">الحالة</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?= $product->status == 'active' ? 'selected' : '' ?>>نشط</option>
                        <option value="out_of_stock" <?= $product->status == 'out_of_stock' ? 'selected' : '' ?>>غير متوفر</option>
                        <option value="discontinued" <?= $product->status == 'discontinued' ? 'selected' : '' ?>>متوقف</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- صف الوصف -->
        <div class="form-group full-width">
            <label for="description">الوصف</label>
            <textarea id="description" 
                      name="description" 
                      class="form-control" 
                      rows="5"><?= htmlspecialchars($product->description) ?></textarea>
        </div>
        
        <!-- صف الصورة -->
        <div class="form-group full-width">
            <label>الصورة الرئيسية</label>
            
            <div class="image-preview-container">
                <?php if($product->main_image && $product->main_image != 'default.jpg'): ?>
                <div class="current-image">
                    <img src="<?= BASE_URL ?>uploads/products/<?= $product->main_image ?>" alt="<?= $product->name ?>">
                    <p>الصورة الحالية</p>
                </div>
                <?php endif; ?>
                
                <div class="image-upload-box">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>اضغط لاختيار صورة جديدة</p>
                    <input type="file" id="main_image" name="main_image" accept="image/*" onchange="previewImage(this)">
                </div>
                
                <div id="imagePreview" class="image-preview"></div>
            </div>
        </div>
        
        <!-- خيارات إضافية -->
        <div class="form-group full-width">
            <label class="checkbox-label">
                <input type="checkbox" name="featured" value="1" <?= $product->featured ? 'checked' : '' ?>>
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">منتج مميز</span>
            </label>
        </div>
        
        <!-- أزرار الإرسال -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                حفظ التغييرات
            </button>
            
            <button type="button" class="btn-delete" onclick="confirmDelete(<?= $product->id ?>, '<?= $product->name ?>')">
                <i class="fas fa-trash"></i>
                حذف
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="preview-item">
                    <img src="${e.target.result}" alt="Preview">
                    <span>الصورة الجديدة</span>
                </div>
            `;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        html: `سيتم حذف المنتج <strong>${name}</strong> بشكل نهائي`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= BASE_URL ?>admin/product/delete/' + id;
        }
    });
}
</script>

<style>
.form-container {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 30px;
    box-shadow: var(--card-shadow);
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--border-color);
}

.form-header h2 {
    color: var(--text-primary);
    font-size: 24px;
}

.form-header h2 i {
    color: #667eea;
    margin-left: 10px;
}

.btn-secondary {
    background: var(--bg-primary);
    color: var(--text-primary);
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    border-color: #667eea;
    color: #667eea;
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

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-primary);
    color: var(--text-primary);
    font-family: 'Cairo', sans-serif;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

.full-width {
    grid-column: 1 / -1;
}

/* ===== رفع الصور ===== */
.image-preview-container {
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 10px;
}

.current-image {
    text-align: center;
}

.current-image img {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid var(--border-color);
}

.current-image p {
    margin-top: 5px;
    font-size: 12px;
    color: var(--text-secondary);
}

.image-upload-box {
    width: 150px;
    height: 150px;
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.image-upload-box:hover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.image-upload-box i {
    font-size: 30px;
    color: var(--text-secondary);
    margin-bottom: 10px;
}

.image-upload-box p {
    color: var(--text-secondary);
    font-size: 12px;
}

.image-upload-box input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.image-preview {
    display: flex;
    gap: 10px;
}

.preview-item {
    text-align: center;
}

.preview-item img {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #667eea;
}

.preview-item span {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #667eea;
}

/* ===== خيارات إضافية ===== */
.checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input {
    display: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border-color);
    border-radius: 5px;
    position: relative;
}

.checkbox-label input:checked + .checkbox-custom {
    background: #667eea;
    border-color: #667eea;
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
    color: var(--text-primary);
}

/* ===== أزرار الإجراءات ===== */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid var(--border-color);
}

.btn-save, .btn-delete {
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.btn-save {
    background: #667eea;
    color: white;
}

.btn-save:hover {
    background: #5a67d8;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.btn-delete {
    background: #ff4757;
    color: white;
}

.btn-delete:hover {
    background: #ee3a4a;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255, 71, 87, 0.3);
}
</style>