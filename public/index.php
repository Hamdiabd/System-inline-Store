<?php

require_once dirname(__DIR__) . '/config/config.php';

require_once ROOT_PATH . 'core/App.php';
require_once ROOT_PATH . 'core/Controller.php';
require_once ROOT_PATH . 'core/Database.php';
require_once ROOT_PATH . 'core/Model.php';
require_once ROOT_PATH . 'core/Router.php';

// بدء الجلسة
session_start();

// تحميل الإعدادات

// تشغيل التوجيه
$app = new App();