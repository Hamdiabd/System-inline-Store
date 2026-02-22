<!-- <?php
class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    public function login() {
        // إذا كان المستخدم مسجل دخوله بالفعل
        if(isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
        }
        
        $data = [
            'title' => 'تسجيل الدخول',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];
        
        $this->view('auth/login', $data);
    }
    
    /**
     * معالجة تسجيل الدخول
     * URL: /auth/authenticate (POST)
     */
    public function authenticate() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // تنظيف البيانات
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];
            
            // التحقق من البريد الإلكتروني
            if(empty($data['email'])) {
                $data['email_err'] = 'الرجاء إدخال البريد الإلكتروني';
            }
            
            // التحقق من كلمة المرور
            if(empty($data['password'])) {
                $data['password_err'] = 'الرجاء إدخال كلمة المرور';
            }
            
            // التحقق من وجود المستخدم
            if(empty($data['email_err']) && empty($data['password_err'])) {
                $user = $this->userModel->findByEmail($data['email']);
                
                if(!$user) {
                    $data['email_err'] = 'لا يوجد حساب بهذا البريد الإلكتروني';
                }
            }
            
            // إذا مافي أخطاء، حاول تسجيل الدخول
            if(empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                
                if($loggedInUser) {
                    // إنشاء جلسة للمستخدم
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'كلمة المرور غير صحيحة';
                    $this->view('auth/login', $data);
                }
            } else {
                $this->view('auth/login', $data);
            }
            
        } else {
            $this->login();
        }
    }
    
    /**
     * إنشاء جلسة المستخدم
     */
    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_avatar'] = $user->avatar ?? 'default.jpg';
        
        // تحديث آخر دخول
        $this->userModel->updateLastLogin($user->id);
        
        // التوجيه حسب نوع المستخدم
        $this->redirectBasedOnRole();
    }
    
    /**
     * التوجيه حسب نوع المستخدم
     */
    private function redirectBasedOnRole() {
        if($_SESSION['user_role'] == 'admin') {
            $this->redirect('admin/dashboard');
        } elseif($_SESSION['user_role'] == 'manager') {
            $this->redirect('manager/dashboard');
        } else {
            $this->redirect('home/index');
        }
    }
    
    /**
     * تسجيل الخروج
     * URL: /auth/logout
     */
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_avatar']);
        session_destroy();
        
        $this->redirect('auth/login');
    }
    
    /**
     * عرض صفحة إنشاء حساب جديد (للعملاء)
     * URL: /auth/register
     */
    public function register() {
        // إذا كان المستخدم مسجل دخوله بالفعل
        if(isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
        }
        
        $data = [
            'title' => 'إنشاء حساب جديد',
            'name' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'phone_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];
        
        $this->view('auth/register', $data);
    }
    
    /**
     * معالجة إنشاء حساب جديد
     * URL: /auth/store (POST)
     */
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // تنظيف البيانات
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'phone_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            
            // التحقق من الاسم
            if(empty($data['name'])) {
                $data['name_err'] = 'الرجاء إدخال الاسم';
            }
            
            // التحقق من البريد الإلكتروني
            if(empty($data['email'])) {
                $data['email_err'] = 'الرجاء إدخال البريد الإلكتروني';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'صيغة البريد الإلكتروني غير صحيحة';
            } else {
                // التحقق من عدم وجود البريد مسبقاً
                if($this->userModel->findByEmail($data['email'])) {
                    $data['email_err'] = 'البريد الإلكتروني مستخدم بالفعل';
                }
            }
            
            // التحقق من رقم الجوال
            if(empty($data['phone'])) {
                $data['phone_err'] = 'الرجاء إدخال رقم الجوال';
            } elseif(!preg_match('/^05[0-9]{8}$/', $data['phone'])) {
                $data['phone_err'] = 'رقم الجوال غير صحيح (يجب أن يبدأ بـ 05)';
            }
            
            // التحقق من كلمة المرور
            if(empty($data['password'])) {
                $data['password_err'] = 'الرجاء إدخال كلمة المرور';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            }
            
            // التحقق من تأكيد كلمة المرور
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'الرجاء تأكيد كلمة المرور';
            } elseif($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'كلمة المرور غير متطابقة';
            }
            
            // إذا مافي أخطاء، أنشئ الحساب
            if(empty($data['name_err']) && empty($data['email_err']) && empty($data['phone_err']) 
                && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                
                $userId = $this->userModel->register($data);
                
                if($userId) {
                    $_SESSION['success'] = 'تم إنشاء الحساب بنجاح، يمكنك تسجيل الدخول الآن';
                    $this->redirect('auth/login');
                } else {
                    $_SESSION['error'] = 'حدث خطأ في إنشاء الحساب';
                    $this->view('auth/register', $data);
                }
                
            } else {
                $this->view('auth/register', $data);
            }
            
        } else {
            $this->register();
        }
    }
}

?> -->