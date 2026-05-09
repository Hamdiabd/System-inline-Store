<?php
class App {
    protected $controller = "HomeController";
    protected $method = "index";
    protected $params = [];
    public function __construct()
    {
        $url = $this->parseUrl();
    //التحقق من وجود الملف
        if(isset($url[0]))
            {
                $controllerName = ucfirst($url[0]).'Controller';
                $controllerPath = APP_PATH . 'controllers/'. $controllerName .'.php';
                if(file_exists($controllerPath))
                    {
                        $this->controller = $controllerName;
                        unset($url[0]);
                    }
            }
        require_once APP_PATH ."controllers/". $this->controller . '.php';
        $this->controller = new $this->controller;
        if(isset($url[1]))
            {
               $found = $this->dashToCamelCase($url[1]);
                
                if(method_exists($this->controller , $found))
                {
                	$this->method = $found;
                     unset($url[1]);
                }
            }
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller,$this->method],$this->params);
    }
    private function parseUrl(){
        if(isset($_GET['url']))
            {
                return explode('/', filter_var(rtrim($_GET['url'],'/'),FILTER_SANITIZE_URL));
            }
        else{
            return [];
        }
    }
    private function dashToCamelCase($string)
    {
        if (strpos($string, '-') === false) {
            return $string;
        }

        // إزالة الشرطات وجعل الحروف التالية كبيرة
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $string))));
    }
}



