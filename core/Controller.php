<?php
class Controller {
    protected function model($model)
    {
        $modelPath = APP_PATH . 'models/'.$model .'.php';
        if(file_exists($modelPath))
            {
                
                require_once $modelPath;
                return new $model();
            }
        die( " ألموديل $model غير موجود ");
    }
      public function view($view, $data = []) {
        $viewPath = APP_PATH . 'views/' . $view . '.php';
        if(file_exists($viewPath)) {
            extract($data);
            require_once APP_PATH.'views/layouts/main.php';
    
        } else {
            die("الملف $view غير موجود");
        }
    }
    protected function redirect($url)
    {
        header( 'Location: '. BASE_URL. $url);
        exit();
        }
        protected function get($key, $default =null)
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
        }
        protected function post($key, $default =null)
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
        }
        protected function isGet($key)
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
        }
        protected function isPost($key)
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
        }
        protected function json($data, $statusCode =200)
    {
    	http_response_code($statusCode); 
        header('Content-Type: application/ json; charset=utf-8');
        echo json_encode($data, JSO_UNESCAPEC_UNICODR);
        exit();
        }

}