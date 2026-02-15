<?php $product = $data['product']; ?>

<?php if($product): ?>
    <h1><?= htmlspecialchars($product->name) ?></h1>
    
    <div class="product-details-card">
        <div class="product-info">
            <p><strong>السعر:</strong> <span class="price-large">$<?= htmlspecialchars($product->price) ?></span></p>
            
            <div class="product-description">
                <h3>الوصف:</h3>
                <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
            </div>
            
            <p class="product-date">
                <small>تاريخ الإضافة: <?= date('Y-m-d', strtotime($product->created_at)) ?></small>
            </p>
        </div>
        
        <div class="product-actions">
            <a href="<?= BASE_URL ?>product/edit/<?= $product->id ?>" class="btn btn-edit">تعديل المنتج</a>
            <a href="<?= BASE_URL ?>product/delete/<?= $product->id ?>" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">حذف المنتج</a>
            <a href="<?= BASE_URL ?>product/index" class="btn">العودة إلى القائمة</a>
        </div>
    </div>
<?php else: ?>
    <div class="not-found">
        <h2>المنتج غير موجود</h2>
        <p>عذراً، المنتج الذي تبحث عنه غير موجود.</p>
        <a href="<?= BASE_URL ?>product/index" class="btn">العودة إلى قائمة المنتجات</a>
    </div>
<?php endif; ?>