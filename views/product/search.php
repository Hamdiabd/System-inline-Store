<?php $products = $data['products']; ?>
<?php $keyword = $data['keyword']; ?>

<h1><?= $title ?></h1>

<div class="search-box-large">
    <form action="<?= BASE_URL ?>product/search" method="GET" class="search-form-large">
        <input type="text" name="q" placeholder="ابحث عن منتج..." value="<?= htmlspecialchars($keyword) ?>" class="search-input-large">
        <button type="submit" class="btn btn-primary">بحث</button>
    </form>
</div>

<?php if(empty($products)): ?>
    <p class="no-results">لا توجد نتائج للبحث عن "<?= htmlspecialchars($keyword) ?>"</p>
<?php else: ?>
    <p class="results-count">تم العثور على <?= count($products) ?> نتائج</p>
    
    <table class="table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>السعر</th>
                <th>الوصف</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product->name) ?></td>
                <td>$<?= htmlspecialchars($product->price) ?></td>
                <td><?= htmlspecialchars(substr($product->description, 0, 50)) ?>...</td>
                <td>
                    <a href="<?= BASE_URL ?>product/show/<?= $product->id ?>" class="btn-small">عرض</a>
                    <a href="<?= BASE_URL ?>product/edit/<?= $product->id ?>" class="btn-small btn-edit">تعديل</a>
                    <a href="<?= BASE_URL ?>product/delete/<?= $product->id ?>" class="btn-small btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">حذف</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="action-links">
    <a href="<?= BASE_URL ?>product/index" class="btn">العودة للقائمة</a>
    <a href="<?= BASE_URL ?>product/create" class="btn btn-success">إضافة منتج جديد</a>
</div>