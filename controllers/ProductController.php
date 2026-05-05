<?php
class ProductController extends Controller {
    
    // عرض جميع المنتجات
    public function index() {
        $productModel = $this->model('Product');
        $products = $productModel->getAllProducts();
        $latestProducts = $productModel->getLatestProducts();

        $data = [
            'title' => 'المنتجات',
            'products' => $products,
            'latest_products' => $latestProducts
        ];
        $this->view('product/index', $data);
    }

    // عرض منتج واحد
    public function show($id) {
        $productModel = $this->model('Product');
        $product = $productModel->getProductById($id);
        $latestProducts = $productModel->getLatestProducts();

        $data = [
            'title' => 'تفاصيل المنتج',
            'product' => $product,
            'latest_products' => $latestProducts
        ];
        $this->view('product/show', $data);
    }

    // عرض نموذج إضافة منتج
    public function create() {
        $productModel = $this->model('Product');
        $brand = $productModel->getAll("brand");
        $categories = $productModel->getAll("category");
        $data = [
            'title' => 'إضافة منتج جديد',
            'brands' =>  $brand,
            'categories' =>  $categories,
        ];
        $this->view('product/create', $data);
    }

    // حفظ منتج جديد
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // التحقق من صحة البيانات
            $errors = [];
            
            if(empty($_POST['name'])) {
                $errors[] = 'اسم المنتج مطلوب';
            }
            
            if(empty($_POST['price']) || !is_numeric($_POST['price'])) {
                $errors[] = 'السعر مطلوب ويجب أن يكون رقماً';
            }
            
            if(empty($_POST['description'])) {
                $errors[] = 'الوصف مطلوب';
            }

            if(empty($errors)) {
                $data = [
                    'name' => trim($_POST['name']),
                    'price' => trim($_POST['price']),
                    'description' => trim($_POST['description'])
                ];

                $productModel = $this->model('Product');
                if($productModel->addProduct($data)) {
                    $_SESSION['success'] = 'تم إضافة المنتج بنجاح';
                    $this->redirect('product/index');
                } else {
                    $_SESSION['error'] = 'حدث خطأ أثناء الإضافة';
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
            }
            
            $this->redirect('product/create');
        }
    }

    // عرض نموذج تعديل منتج
    public function edit($id) {
        $productModel = $this->model('Product');
        $product = $productModel->getProductById($id);
        $latestProducts = $productModel->getLatestProducts();

        if(!$product) {
            $_SESSION['error'] = 'المنتج غير موجود';
            $this->redirect('product/index');
        }

        $data = [
            'title' => 'تعديل المنتج',
            'product' => $product,
            'latest_products' => $latestProducts
        ];
        $this->view('product/edit', $data);
    }

    // تحديث المنتج
    public function update($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // التحقق من صحة البيانات
            $errors = [];
            
            if(empty($_POST['name'])) {
                $errors[] = 'اسم المنتج مطلوب';
            }
            
            if(empty($_POST['price']) || !is_numeric($_POST['price'])) {
                $errors[] = 'السعر مطلوب ويجب أن يكون رقماً';
            }
            
            if(empty($_POST['description'])) {
                $errors[] = 'الوصف مطلوب';
            }

            if(empty($errors)) {
                $data = [
                    'name' => trim($_POST['name']),
                    'price' => trim($_POST['price']),
                    'description' => trim($_POST['description'])
                ];

                $productModel = $this->model('Product');
                if($productModel->updateProduct($id, $data)) {
                    $_SESSION['success'] = 'تم تحديث المنتج بنجاح';
                    $this->redirect('product/index');
                } else {
                    $_SESSION['error'] = 'حدث خطأ أثناء التحديث';
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
            }
            
            $this->redirect('product/edit/' . $id);
        }
    }

    // حذف منتج
    public function delete($id) {
        $productModel = $this->model('Product');
        if($productModel->deleteProduct($id)) {
            $_SESSION['success'] = 'تم حذف المنتج بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء الحذف';
        }
        $this->redirect('product/index');
    }

    // البحث عن منتجات
    public function search() {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        $productModel = $this->model('Product');
        
        if(!empty($keyword)) {
            $products = $productModel->searchProducts($keyword);
        } else {
            $products = $productModel->getAllProducts();
        }
        
        $latestProducts = $productModel->getLatestProducts();

        $data = [
            'title' => 'نتائج البحث عن: ' . $keyword,
            'products' => $products,
            'keyword' => $keyword,
            'latest_products' => $latestProducts
        ];
        $this->view('product/search', $data);
    }
}