
<?php require_once APP_PATH."views/layouts/header.php"; ?>
    <div class="app-container">
        
<?php require_once APP_PATH."views/layouts/sidebar.php"; ?>

        <main class="main-content">
            <div class="content-header">
                <h1><?= $title ?? 'لوحة التحكم' ?></h1>
                <?php if(isset($breadcrumb)): ?>
                <div class="breadcrumb">
                    <?= $breadcrumb ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="content-body">
                <?php require $view_file; ?>
            </div>
        </main>
    </div>

    <script src="<?= BASE_URL ?>js/main.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
</body>
</html>