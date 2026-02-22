<?php

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $brandModel;
    

    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->brandModel = $this->model('Brand');
    }

    public function index() {
        $products = $this->productModel->getAllForAdmin();
        $count = $this->brandModel->getProductCount(5);
        $data = [
            'title' => 'جميع المنتجات',
            'products' => $products,
            'totalStock' => $count,
            'outOfStock' => $products,
            'lowStock' => $products
        ];
        $this->view('product/index', $data);
    }
    public function show($id) {
        $product = $this->productModel->getWithDetails($id);
        
        if (!$product) {
            $this->redirect('product/index');
            return;
        }

        $images = $this->productModel->getImages($id);
        
        $data = [
            'title' => $product->name,
            'product' => $product,
            'images' => $images
        ];
        
        $this->view('product/show', $data);
    }
public function delete($id) {
    // التحقق من وجود المنتج
    $product = $this->productModel->getProductById($id);
    
    if(!$product) {
        $_SESSION['error'] = 'المنتج غير موجود';
        $this->redirect('product/index');
        return;
    }
    
    // =============================================
    // حذف الصورة من السيرفر
    // =============================================
    if($product->main_image && $product->main_image != 'default.jpg') {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/app/app/public/uploads/products/' . $product->main_image;
        if(file_exists($imagePath)) {
            unlink($imagePath); // حذف الملف
        }
    }
    
    // =============================================
    // حذف من قاعدة البيانات
    // =============================================
    if($this->productModel->delete($id)) {
        $_SESSION['success'] = 'تم حذف المنتج بنجاح';
    } else {
        $_SESSION['error'] = 'حدث خطأ في حذف المنتج';
    }
    
    $this->redirect('product/index');
}
    public function category($categoryId) {
        $products = $this->productModel->getByCategory($categoryId);
        $data = [
            'title' => 'منتجات التصنيف',
            'products' => $products
        ];
        $this->view('products/category', $data);
    }

    public function search() {
        $keyword = $_GET['q'] ?? '';
        $products = $this->productModel->search($keyword);
        
        $data = [
            'title' => 'نتائج البحث',
            'products' => $products,
            'keyword' => $keyword
        ];
        
        $this->view('product/search', $data);
    }
    

    public function create() 
        {
        $categories = $this->categoryModel->getCategories();
        $brands = $this->brandModel->getBrands();
        
        $data = [
            'title' => 'تعديل المنتج',
            'categories' => $categories,
            'brands' => $brands,
        ];
        
        $this->view('product/create', $data);
    }
   public function store() {

        // التأكد من أن الطلب POST
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('product/create');
        }
        
        $errors = [];
        
        // التحقق من اسم المنتج
        if(empty($_POST['name'])) {
            $errors['name'] = 'اسم المنتج مطلوب';
        } elseif(strlen($_POST['name']) < 3) {
            $errors['name'] = 'اسم المنتج يجب أن يكون 3 أحرف على الأقل';
        }
        
        // التحقق من السعر
        if(empty($_POST['price'])) {
            $errors['price'] = 'السعر مطلوب';
        } elseif(!is_numeric($_POST['price'])) {
            $errors['price'] = 'السعر يجب أن يكون رقماً';
        } elseif($_POST['price'] <= 0) {
            $errors['price'] = 'السعر يجب أن يكون أكبر من صفر';
        }
        
        // التحقق من المخزون
        if(isset($_POST['stock']) && !is_numeric($_POST['stock'])) {
            $errors['stock'] = 'المخزون يجب أن يكون رقماً';
        } elseif($_POST['stock'] < 0) {
            $errors['stock'] = 'المخزون لا يمكن أن يكون سالباً';
        }
        
        // إذا كان هناك أخطاء
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('product/create');
            return;
        }
        $mainImage = 'default.jpg';
        
        if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            
            // التحقق من حجم الصورة (2MB كحد أقصى)
            if($_FILES['main_image']['size'] > 2 * 1024 * 1024) {
                $_SESSION['errors']['image'] = 'حجم الصورة كبير جداً. الحد الأقصى 2MB';
                $_SESSION['old'] = $_POST;
                $this->redirect('product/create');
                return;
            }
            
            // التحقق من نوع الصورة
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $_FILES['main_image']['type'];
            
            if(!in_array($fileType, $allowedTypes)) {
                $_SESSION['errors']['image'] = 'نوع الملف غير مسموح. الأنواع المسموحة: JPG, PNG, GIF, WEBP';
                $_SESSION['old'] = $_POST;
                $this->redirect('product/create');
                return;
            }
            
            // التحقق من الامتداد
            $extension = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(!in_array($extension, $allowedExtensions)) {
                $_SESSION['errors']['image'] = 'امتداد الملف غير مسموح';
                $_SESSION['old'] = $_POST;
                $this->redirect('product/create');
                return;
            }
            
            // إنشاء اسم فريد للصورة (آمن)
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            
            // مسار رفع الصورة (للهاتف)
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/mvc/App/public/uploads/products/';
            
            // إنشاء المجلد إذا لم يكن موجوداً
            if(!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // التأكد من أن المجلد قابل للكتابة
            if(!is_writable($uploadDir)) {
                error_log("مجلد الرفع غير قابل للكتابة: " . $uploadDir);
                $_SESSION['errors']['image'] = 'خطأ في نظام الملفات';
                $_SESSION['old'] = $_POST;
                $this->redirect('product/create');
                return;
            }
            
            $uploadPath = $uploadDir . $imageName;
            
            // نقل الصورة من المجلد المؤقت إلى المجلد الدائم
            if(move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadPath)) {
                $mainImage = $imageName;
                
                // تغيير صلاحيات الملف
                chmod($uploadPath, 0644);
                
            } else {
                error_log("فشل نقل الصورة. خطأ: " . $_FILES['main_image']['error']);
                $_SESSION['errors']['image'] = 'فشل في رفع الصورة';
                $_SESSION['old'] = $_POST;
                $this->redirect('product/create');
                return;
            }
        }
        $data = [
            'name' => trim($_POST['name']),
            'price' => floatval($_POST['price']),
            'category_id' => !empty($_POST['category_id']) ? intval($_POST['category_id']) : null,
            'brand_id' => !empty($_POST['brand_id']) ? intval($_POST['brand_id']) : null,
            'stock' => intval($_POST['stock'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'main_image' => $mainImage,
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'status' => $_POST['status'] ?? 'active'
        ];
        $productId = $this->productModel->create($data);
        
        if($productId) {
            $_SESSION['success'] = 'تم إضافة المنتج بنجاح';
            
            unset($_SESSION['old']);
            
            if($mainImage != 'default.jpg') {
                $savedPath = $uploadDir . $mainImage;
                if(file_exists($savedPath)) {
                    $_SESSION['success'] .= ' وتم رفع الصورة بنجاح';
                }
            }
            
            $this->redirect('product/index');
        } else {
            // فشل الحفظ - نحذف الصورة إن وجدت
            if($mainImage != 'default.jpg' && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            
            $_SESSION['errors']['database'] = 'حدث خطأ في قاعدة البيانات. الرجاء المحاولة مرة أخرى.';
            $_SESSION['old'] = $_POST;
            $this->redirect('product/create');
        }
    }

/**
 * عرض صفحة تعديل المنتج
 * @param int $id معرف المنتج
 */
public function edit($id) {
    // جلب بيانات المنتج
    $product = $this->productModel->getProductById($id);
    
    if(!$product) {
        $_SESSION['error'] = 'المنتج غير موجود';
        $this->redirect('product/index');
        return;
    }
    
    // جلب التصنيفات والعلامات التجارية
    $categories = $this->categoryModel->getAllActive();
    $brands = $this->brandModel->getAllActive();
    
    // جلب صور المنتج
    $images = $this->productModel->getProductImages($id);
    
    $data = [
        'title' => 'تعديل المنتج',
        'page' => 'products-edit',
        'product' => $product,
        'categories' => $categories,
        'brands' => $brands,
        'images' => $images
    ];
    
    $this->view('product/edit', $data);
}

/**
 * تحديث بيانات المنتج
 * @param int $id معرف المنتج
 */
public function update($id) {
    // التأكد من أن الطلب POST
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('product/index');
    }
    
    // التحقق من وجود المنتج
    $product = $this->productModel->getProductById($id);
    if(!$product) {
        $_SESSION['error'] = 'المنتج غير موجود';
        $this->redirect('product/index');
        return;
    }
    
    // =============================================
    // 1️⃣ التحقق من صحة البيانات
    // =============================================
    $errors = [];
    
    if(empty($_POST['name'])) {
        $errors[] = 'اسم المنتج مطلوب';
    }
    
    if(empty($_POST['price'])) {
        $errors[] = 'السعر مطلوب';
    } elseif(!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors[] = 'السعر يجب أن يكون رقماً موجباً';
    }
    
    if(isset($_POST['stock']) && !empty($_POST['stock']) && !is_numeric($_POST['stock'])) {
        $errors[] = 'المخزون يجب أن يكون رقماً';
    }
    
    if(!empty($errors)) {
        $_SESSION['error'] = $errors;
        $this->redirect('product/edit/' . $id);
        return;
    }
    
    // =============================================
    // 2️⃣ معالجة رفع الصورة الجديدة (إذا وجدت)
    // =============================================
    $mainImage = $product->main_image; // الصورة القديمة
    
    if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        
        // التحقق من حجم الصورة
        if($_FILES['main_image']['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = ['حجم الصورة كبير جداً. الحد الأقصى 2MB'];
            $this->redirect('product/edit/' . $id);
            return;
        }
        
        // التحقق من نوع الصورة
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['main_image']['type'];
        
        if(!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = ['نوع الملف غير مسموح'];
            $this->redirect('product/edit/' . $id);
            return;
        }
        
        // إنشاء اسم فريد للصورة
        $extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $extension;
        
        // مسار رفع الصورة
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/app/app/public/uploads/products/';
        
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uploadPath = $uploadDir . $imageName;
        
        // نقل الصورة
        if(move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadPath)) {
            // حذف الصورة القديمة إذا لم تكن default
            if($product->main_image != 'default.jpg') {
                $oldImagePath = $uploadDir . $product->main_image;
                if(file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $mainImage = $imageName;
        }
    }
    
    // =============================================
    // 3️⃣ تجهيز البيانات للتحديث
    // =============================================
    $data = [
        'name' => trim($_POST['name']),
        'price' => floatval($_POST['price']),
        'category_id' => !empty($_POST['category_id']) ? intval($_POST['category_id']) : null,
        'brand_id' => !empty($_POST['brand_id']) ? intval($_POST['brand_id']) : null,
        'stock' => intval($_POST['stock'] ?? 0),
        'description' => trim($_POST['description'] ?? ''),
        'main_image' => $mainImage,
        'featured' => isset($_POST['featured']) ? 1 : 0,
        'status' => $_POST['status'] ?? 'active'
    ];
    
    // =============================================
    // 4️⃣ تحديث في قاعدة البيانات
    // =============================================
    if($this->productModel->update($id, $data)) {
        $_SESSION['success'] = 'تم تحديث المنتج بنجاح';
        $this->redirect('product/index');
    } else {
        $_SESSION['error'] = ['حدث خطأ في تحديث المنتج'];
        $this->redirect('product/edit/' . $id);
    }
}
/**
 * حذف منتج
 * @param int $id معرف المنتج
 */
public function delete($id) {
    // التحقق من وجود المنتج
    $product = $this->productModel->getProductById($id);
    
    if(!$product) {
        $_SESSION['error'] = 'المنتج غير موجود';
        $this->redirect('product/index');
        return;
    }
    
    // =============================================
    // حذف الصورة من السيرفر (إذا كانت موجودة)
    // =============================================
    if($product->main_image && $product->main_image != 'default.jpg') {
        // المسار في البيئة المحلية (XAMPP)
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/mvc/public/uploads/products/' . $product->main_image;
        
        if(file_exists($imagePath)) {
            unlink($imagePath); // حذف الملف
        }
    }
    
    // =============================================
    // حذف من قاعدة البيانات
    // =============================================
    if($this->productModel->delete($id)) {
        $_SESSION['success'] = 'تم حذف المنتج بنجاح';
    } else {
        $_SESSION['error'] = 'حدث خطأ في حذف المنتج';
    }
    
    $this->redirect('product/index');
}
    }