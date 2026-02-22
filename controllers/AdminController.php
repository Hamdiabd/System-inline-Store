<!-- <?php
require_once APP_PATH . 'core/Auth.php'; 
class AdminController extends Controller {
    
    public function __construct() {
        Auth::requireAdmin();
    }
    
    public function dashboard() {
        $data = ['title' => 'لوحة التحكم'];
        $this->view('admin/dashboard', $data);
    }
    
    public function products() {
        $data = ['title' => 'إدارة المنتجات'];
        $this->view('admin/products', $data);
    }
}

?> -->