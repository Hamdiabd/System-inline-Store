<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['welcome'] ?> | <?= $data['title'] ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>"><?= $data['welcome'] ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= BASE_URL ?>">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/product">المنتجات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/home/about">من نحن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/home/contact">اتصل بنا</a>
                    </li>
                </ul>
                
                <!-- نموذج البحث -->
                <form class="d-flex mx-3" action="<?= BASE_URL ?>/product/search" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="ابحث عن منتج...">
                    <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
                </form>

                <!-- أيقونات المستخدم والسلة -->
                <div class="d-flex">
                    <a href="<?= BASE_URL ?>/cart" class="btn btn-outline-light me-2 position-relative">
                        <i class="bi bi-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>
                    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline-light">
                        <i class="bi bi-person"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- محتوى الصفحة الرئيسية -->
    <main>
        <!-- Banner Section -->
        <section class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1 class="display-4">مرحباً بكم في <?= $data['welcome'] ?></h1>
                <p class="lead">أفضل المنتجات بأفضل الأسعار</p>
                <a href="<?= BASE_URL ?>/product" class="btn btn-light btn-lg">تسوق الآن</a>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">منتجات مميزة</h2>
                <div class="row">
                    <?php foreach ($data['featuredProducts'] as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="<?= BASE_URL ?>/uploads/<?= $product->main_image ?? 'default.jpg' ?>" 
                                 class="card-img-top" alt="<?= $product->name ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $product->name ?></h5>
                                <p class="card-text text-muted"><?= $product->category_name ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0"><?= number_format($product->price, 2) ?> ريال</span>
                                    <a href="<?= BASE_URL ?>/product/show/<?= $product->id ?>" 
                                       class="btn btn-primary">عرض</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">التصنيفات</h2>
                <div class="row">
                    <?php foreach ($data['categories'] as $category): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-folder display-4"></i>
                                <h5 class="card-title mt-3"><?= $category->name ?></h5>
                                <a href="<?= BASE_URL ?>/product/category/<?= $category->id ?>" 
                                   class="btn btn-outline-primary btn-sm">تصفح</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">جميع الحقوق محفوظة &copy; <?= date('Y') ?> <?= SITE_NAME ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>