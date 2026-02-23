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

    public function view($view, $data = []) {
        $viewPath = APP_PATH . 'views/' . $view . '.php';
        if(file_exists($viewPath)) {
            extract($data);
            include APP_PATH.'views/layouts/main.php';
    
        } else {
            die("الملف $view غير موجود");
        }
    }

    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
        }
        protected function post($key ,$default=null) {
            return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
        }
        protected function get($key ,$default=null) {
            return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
        }
        protected function isPost() {
            return $_SERVER['REQUEST_METHOD'] =='POST';
        }
        protected function isGet() {
            return $_SERVER['REQUEST_METHOD'] =='GET';
        }

}