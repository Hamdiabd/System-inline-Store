<?php
class Auth {
    

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function isAdmin() {
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin');
    }
    
    public static function isManager() {
        return (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'manager']));
    }
    public static function requireLogin() {
        if(!self::isLoggedIn()) {
            $_SESSION['error'] = 'الرجاء تسجيل الدخول أولاً';
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
    }
    
    public static function requireAdmin() {
        self::requireLogin();
        
        if(!self::isAdmin()) {
            $_SESSION['error'] = 'ليس لديك صلاحية الوصول لهذه الصفحة';
            header('Location: ' . BASE_URL . 'home/index');
            exit();
        }
    }
    public static function requireManager() {
        self::requireLogin();
        
        if(!self::isManager()) {
            $_SESSION['error'] = 'ليس لديك صلاحية الوصول لهذه الصفحة';
            header('Location: ' . BASE_URL . 'home/index');
            exit();
        }
    }
    
    /**
     * منع الوصول إذا كان مسجل دخوله (لصفحات التسجيل)
     */
    public static function requireGuest() {
        if(self::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit();
        }
    }
    public static function userId() {
        return $_SESSION['user_id'] ?? null;
    }
    public static function userName() {
        return $_SESSION['user_name'] ?? 'زائر';
    }
    public static function userRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    public static function user() {
        if(self::isLoggedIn()) {
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            return $userModel->getById($_SESSION['user_id']);
        }
        return null;
    }
}