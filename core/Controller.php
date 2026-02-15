<?php
class Controller {
    
    protected function model($model) {
        $modelPath = APP_PATH . 'models/' . $model . '.php';
        if(file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        die("الموديل $model غير موجود");
    }

protected function view($view, $data = []) {
    $viewPath = APP_PATH . 'views/' . $view . '.php';
    if(file_exists($viewPath)) {
        extract($data);
        $view_file = $viewPath; // هذا المتغير سيستخدم في main.php
        require APP_PATH . 'views/layouts/main.php';
    } else {
        die("الملف $view غير موجود");
    }
}

    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
    }
}