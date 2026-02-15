<?php $latest_products = $data['latest_products'] ?? []; ?>
<?php $products = $data['products']; ?>

<h1><?= $title ?></h1>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="action-bar">
    <a href="<?= BASE_URL ?>product/create" class="btn btn-success">إضافة منتج جديد</a>
    <a href="<?= BASE_URL ?>product/search" class="btn">بحث متقدم</a>
</div>

<?php if(empty($products)): ?>
    <p class="no-data">لا توجد منتجات حالياً</p>
<?php else: ?>
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
                <td class="actions">
                    <a href="<?= BASE_URL ?>product/show/<?= $product->id ?>" class="btn-small">عرض</a>
                    <a href="<?= BASE_URL ?>product/edit/<?= $product->id ?>" class="btn-small btn-edit">تعديل</a>
                    <a href="<?= BASE_URL ?>product/delete/<?= $product->id ?>" class="btn-small btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">حذف</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>