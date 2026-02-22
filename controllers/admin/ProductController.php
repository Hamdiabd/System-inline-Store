<!-- <?php
class ProductController extends Controller {
    
    private $productModel;
    private $categoryModel;
    private $brandModel;
    
    public function __construct() {
        // التحقق من صلاحية المدير
        // if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
        //     header('Location: ' . BASE_URL . 'auth/login');
        //     exit();
        // }
        
        // تحميل الموديلات
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->brandModel = $this->model('Brand');
    }
    
    /**
     * عرض جميع المنتجات
     */
    public function index() {
        $products = $this->productModel->getAllForAdmin();
        
        // جلب الإحصائيات
        $totalProducts = count($products);
        $totalStock = 0;
        $outOfStock = 0;
        $lowStock = 0;
        
        foreach($products as $product) {
            $totalStock += $product->stock;
            if($product->stock <= 0) {
                $outOfStock++;
            } elseif($product->stock <= 10) {
                $lowStock++;
            }
        }
        
        $data = [
            'title' => 'إدارة المنتجات',
            'page' => 'products',
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock
        ];
        
        $this->view('admin/products/index', $data);
    }
    
    public function create() {
        $categories = $this->categoryModel->getAllActive();
        $brands = $this->brandModel->getAllActive();
        
        $data = [
            'title' => 'إضافة منتج جديد',
            'page' => 'products',
            'categories' => $categories,
            'brands' => $brands
        ];
        
        $this->view('admin/products/create', $data);
    }
    
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $errors = [];
            
            if(empty($_POST['name'])) {
                $errors[] = 'اسم المنتج مطلوب';
            }
            
            if(empty($_POST['price']) || !is_numeric($_POST['price'])) {
                $errors[] = 'السعر مطلوب ويجب أن يكون رقماً';
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                $this->redirect('admin/product/create');
                return;
            }
            
            // معالجة الصورة
            $mainImage = 'default.jpg';
            if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/mvc/public/uploads/products/';
                
                if(!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                $imageExt = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
                $imageName = time() . '_' . uniqid() . '.' . $imageExt;
                $targetFile = $targetDir . $imageName;
                
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if(in_array(strtolower($imageExt), $allowedTypes)) {
                    if(move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFile)) {
                        $mainImage = $imageName;
                    }
                }
            }
            
            $data = [
                'name' => trim($_POST['name']),
                'price' => trim($_POST['price']),
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'brand_id' => !empty($_POST['brand_id']) ? $_POST['brand_id'] : null,
                'stock' => trim($_POST['stock'] ?? 0),
                'description' => trim($_POST['description'] ?? ''),
                'main_image' => $mainImage,
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $productId = $this->productModel->create($data);
            
            if($productId) {
                $_SESSION['success'] = 'تم إضافة المنتج بنجاح';
                $this->redirect('admin/product/index');
            } else {
                $_SESSION['error'] = 'حدث خطأ أثناء إضافة المنتج';
                $this->redirect('admin/product/create');
            }
        } else {
            $this->redirect('admin/product/create');
        }
    }
    
    /**
     * عرض صفحة تعديل منتج
     */
    public function edit($id) {
        $product = $this->productModel->getProductById($id);
        
        if(!$product) {
            $_SESSION['error'] = 'المنتج غير موجود';
            $this->redirect('admin/product/index');
        }
        
        $images = $this->productModel->getProductImages($id);
        $categories = $this->categoryModel->getAllActive();
        $brands = $this->brandModel->getAllActive();
        
        $data = [
            'title' => 'تعديل المنتج',
            'page' => 'products',
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'images' => $images
        ];
        
        $this->view('admin/products/edit', $data);
    }
    
    /**
     * تحديث منتج
     */
    public function update($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $product = $this->productModel->getProductById($id);
            
            if(!$product) {
                $_SESSION['error'] = 'المنتج غير موجود';
                $this->redirect('admin/product/index');
            }
            
            $errors = [];
            
            if(empty($_POST['name'])) {
                $errors[] = 'اسم المنتج مطلوب';
            }
            
            if(empty($_POST['price']) || !is_numeric($_POST['price'])) {
                $errors[] = 'السعر مطلوب ويجب أن يكون رقماً';
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                $this->redirect('admin/product/edit/' . $id);
                return;
            }
            
            // معالجة الصورة
            $mainImage = $product->main_image;
            if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/mvc/public/uploads/products/';
                
                // حذف الصورة القديمة
                if($product->main_image != 'default.jpg' && file_exists($targetDir . $product->main_image)) {
                    unlink($targetDir . $product->main_image);
                }
                
                $imageExt = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
                $imageName = time() . '_' . uniqid() . '.' . $imageExt;
                $targetFile = $targetDir . $imageName;
                
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if(in_array(strtolower($imageExt), $allowedTypes)) {
                    if(move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFile)) {
                        $mainImage = $imageName;
                    }
                }
            }
            
            $data = [
                'name' => trim($_POST['name']),
                'price' => trim($_POST['price']),
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'brand_id' => !empty($_POST['brand_id']) ? $_POST['brand_id'] : null,
                'stock' => trim($_POST['stock'] ?? 0),
                'description' => trim($_POST['description'] ?? ''),
                'main_image' => $mainImage,
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            if($this->productModel->update($id, $data)) {
                $_SESSION['success'] = 'تم تحديث المنتج بنجاح';
            } else {
                $_SESSION['error'] = 'حدث خطأ أثناء تحديث المنتج';
            }
            
            $this->redirect('admin/product/index');
        } else {
            $this->redirect('admin/product/index');
        }
    }
    
    /**
     * حذف منتج
     */
    public function delete($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $product = $this->productModel->getProductById($id);
            
            if($product) {
                // حذف الصورة
                if($product->main_image != 'default.jpg') {
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/mvc/public/uploads/products/' . $product->main_image;
                    if(file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                if($this->productModel->delete($id)) {
                    $_SESSION['success'] = 'تم حذف المنتج بنجاح';
                } else {
                    $_SESSION['error'] = 'حدث خطأ أثناء حذف المنتج';
                }
            } else {
                $_SESSION['error'] = 'المنتج غير موجود';
            }
            
            $this->redirect('admin/product/index');
        } else {
            $this->redirect('admin/product/index');
        }
    }
    
    /**
     * تغيير حالة المنتج - AJAX
     */
    public function toggleStatus($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $product = $this->productModel->getProductById($id);
            
            if($product) {
                $newStatus = ($product->status == 'active') ? 'discontinued' : 'active';
                
                // تحديث الحالة باستخدام الموديل
                $this->productModel->db->query("UPDATE products SET status = :status WHERE id = :id");
                $this->productModel->db->bind(':status', $newStatus);
                $this->productModel->db->bind(':id', $id);
                
                if($this->productModel->db->execute()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'newStatus' => $newStatus,
                        'message' => 'تم تغيير حالة المنتج بنجاح'
                    ]);
                    exit();
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة المنتج'
            ]);
            exit();
        }
    }
    
    /**
     * عرض تفاصيل منتج
     */
    public function view1($id) {
        $product = $this->productModel->getProductById($id);
        
        if(!$product) {
            $_SESSION['error'] = 'المنتج غير موجود';
            $this->redirect('admin/product/index');
        }
        
        $images = $this->productModel->getProductImages($id);
        
        $data = [
            'title' => 'تفاصيل المنتج',
            'page' => 'products',
            'product' => $product,
            'images' => $images
        ];
        
        $this->view('admin/products/view', $data);
    }
    
    /**
     * البحث عن منتجات - AJAX
     */
    public function search() {
        if(isset($_GET['q'])) {
            $keyword = trim($_GET['q']);
            
            // استخدام الموديل للبحث
            $results = $this->productModel->search($keyword);
            
            header('Content-Type: application/json');
            echo json_encode($results);
            exit();
        }
    }
    
    /**
     * تحديث المخزون بكميات متعددة
     */
    public function bulkUpdateStock() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stocks'])) {
            $updates = $_POST['stocks'];
            $success = true;
            
            foreach($updates as $productId => $stock) {
                // استخدام الموديل للتحديث
                $this->productModel->db->query("UPDATE products SET stock = :stock WHERE id = :id");
                $this->productModel->db->bind(':stock', $stock);
                $this->productModel->db->bind(':id', $productId);
                
                if(!$this->productModel->db->execute()) {
                    $success = false;
                }
            }
            
            if($success) {
                $_SESSION['success'] = 'تم تحديث المخزون بنجاح';
            } else {
                $_SESSION['error'] = 'حدث خطأ أثناء تحديث المخزون';
            }
            
            $this->redirect('admin/product/index');
        }
    }
    
    /**
     * تكرار منتج (نسخ)
     */
    public function duplicate($id) {
        $original = $this->productModel->getProductById($id);
        
        if($original) {
            $data = [
                'name' => $original->name . ' (نسخة)',
                'price' => $original->price,
                'category_id' => $original->category_id,
                'brand_id' => $original->brand_id,
                'stock' => 0,
                'description' => $original->description,
                'main_image' => 'default.jpg',
                'featured' => 0,
                'status' => 'draft'
            ];
            
            $newId = $this->productModel->create($data);
            
            if($newId) {
                $_SESSION['success'] = 'تم نسخ المنتج بنجاح';
            } else {
                $_SESSION['error'] = 'حدث خطأ أثناء نسخ المنتج';
            }
        }
        
        $this->redirect('admin/product/index');
    }
}
?> -->