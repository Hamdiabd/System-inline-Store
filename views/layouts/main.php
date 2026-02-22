<?php require_once APP_PATH .'views/layouts/header.php';?>
<?php require_once APP_PATH .'views/layouts/sidebar.php';?>

    <div class="app-container">

        <main class="main-content" id="mainContent">
            <div class="content-wrapper">
                <div class="content-padding">
                    <?php if(isset($viewPath) && file_exists($viewPath)): ?>
                        <?php include $viewPath; ?>
                    <?php else: ?>
                        <div class="error-container">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h2>عذراً، الصفحة المطلوبة غير موجودة</h2>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
<?php require_once APP_PATH .'views/layouts/footer.php';?>

    </div>













