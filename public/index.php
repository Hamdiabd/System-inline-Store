<?php
// تظمين الملفات الرئيسية
session_start();
require '../config/config.php';
require APP_PATH . 'core/Database.php';
require APP_PATH . 'core/Model.php';
require APP_PATH . 'core/Controller.php';
require APP_PATH . 'core/App.php';

new App();