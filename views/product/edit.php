<?php $product = $data['product']; ?>
<?php $errors = $_SESSION['errors'] ?? []; ?>
<?php $old = $_SESSION['old'] ?? []; ?>

<h1><?= $title ?></h1>

<?php if(!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= BASE_URL ?>product/update/<?= $product->id ?>" method="POST" class="form">
    <div class="form-group">
        <label for="name">اسم المنتج:</label>
        <input type="text" id="name" name="name" value="<?= $old['name'] ?? $product->name ?>" required>
    </div>

    <div class="form-group">
        <label for="price">السعر:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= $old['price'] ?? $product->price ?>" required>
    </div>

    <div class="form-group">
        <label for="description">الوصف:</label>
        <textarea id="description" name="description" rows="5" required><?= $old['description'] ?? $product->description ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success">تحديث</button>
        <a href="<?= BASE_URL ?>product/index" class="btn">إلغاء</a>
    </div>
</form>

<?php 
unset($_SESSION['errors']);
unset($_SESSION['old']);
?>