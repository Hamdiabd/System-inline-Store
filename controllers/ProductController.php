<?php
class ProductController
{
    protected $products;
    protected $category;
    protected $brand;
    protected $warehouse;
    protected $supplier;

    function __construct()
    {
        $this->products  = new Product();
        $this->category  = new Category();
        $this->brand     = new Brand();
        $this->warehouse = new Warehouse();
        $this->supplier  = new Supplier();
    }

    // ============================================
    // عرض قائمة المنتجات
    // ============================================
    public function index()
    {
        $products = $this->products->getAllProducts();
        $data = [
            'title'    => 'المنتجات',
            'products' => $products,
        ];
        $this->view('product/index', $data);
    }

    // ============================================
    // عرض نموذج إضافة منتج
    // ============================================
    public function create()
    {
        $data = [
            'title'      => 'إضافة منتج جديد',
            'brands'     => $this->brand->getAll(),
            'categories' => $this->category->getAll(),
            'suppliers'  => $this->supplier->getAll(),
            'warehouse'  => $this->warehouse->getAll(),
        ];
        $this->view('product/create', $data);
    }

    // ============================================
    // حفظ منتج جديد
    // ============================================
    public function store()
    {
        // ✅ استخدام الدالة العامة
        $this->products->beginTransaction();

        try {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                // ✅ 1. رفع الصورة الأساسية
                $baseImagePath = $this->products->AddFile("product_image");

                // ✅ 2. تجهيز بيانات المنتج
                $productData = [
                    "name"           => $_POST["product_name"],
                    "description"    => $_POST["description"] ?? null,
                    "brand_id"       => !empty($_POST["brand_id"]) ? $_POST["brand_id"] : null,
                    "base_image_url" => $baseImagePath ?: null,
                    "is_active"      => $_POST["is_active"] ?? 1,
                ];

                // ✅ 3. إدراج المنتج
                $productId = $this->products->createProduct($productData);

                // ✅ 4. ربط الأقسام
                $categoriesId = $_POST['categories'] ?? [];
                if (!empty($categoriesId)) {
                    $this->products->attachCategories($productId, $categoriesId);
                }

                // ✅ 5. معالجة المتغيرات
                $variants = $_POST['variants'] ?? [];
                if (empty($variants)) {
                    throw new Exception('يجب إضافة متغير واحد على الأقل.');
                }

                $variantsId = [];
                foreach ($variants as $key => $variant) {
                    if (empty(trim($variant['sku'])) || !isset($variant['price'])) {
                        throw new Exception("المتغير رقم " . ($key + 1) . ": SKU والسعر مطلوبان");
                    }

                    $variantData = [
                        'sku'          => $variant['sku'],
                        'size_option'  => $variant['size_option'] ?? null,
                        'color_option' => $variant['color_option'] ?? null,
                        'packaging'    => $variant['packaging'] ?? null,
                        'price'        => $variant['price'],
                        'weight_kg'    => $variant['weight_kg'] ?? null,
                        'image_url'    => null,
                    ];

                    $variantID = $this->products->addVariant($productId, $variantData);
                    $variantsId[] = $variantID;
                }

                // ✅ 6. معالجة الموردين
                $suppliers = $_POST['suppliers'] ?? [];
                foreach ($suppliers as $supplier) {
                    if (empty($supplier['supplier_id']) || empty($supplier['supply_price'])) continue;
                    $this->products->attachSupplier($productId, $supplier);
                }

                // ✅ 7. المخزون الأولي
                $inventoryData = $_POST['inventory'] ?? [];
                if (!empty($inventoryData) && !empty($variantsId)) {
                    $firstVariantId = $variantsId[0];
                    foreach ($inventoryData as $inv) {
                        if (empty($inv['warehouse_id'])) continue;
                        $this->products->addInventory($firstVariantId, $inv);
                    }
                }

                // ✅ 8. نجاح - حفظ المعاملة
                $this->products->commit();
                $_SESSION['message'] = '✅ تم إضافة المنتج بنجاح';
                header('Location: ' . BASE_URL . 'products');
                exit;
            }
            
        } catch (Exception $e) {
            // ✅ 9. فشل - تراجع
            $this->products->rollBack();
            $_SESSION['error'] = '❌ ' . $e->getMessage();
            header('Location: ' . BASE_URL . 'product/create');
            exit;
        }
    }

    // ============================================
    // حذف منتج
    // ============================================
    public function delete($productId)
    {
        if ($productId) {
            // حذف الصور
            $product = $this->products->getProductById($productId);
            if ($product) {
                if (!empty($product->base_image_url) && file_exists($product->base_image_url)) {
                    unlink($product->base_image_url);
                }
                if (!empty($product->variant_images)) {
                    foreach ($product->variant_images as $img) {
                        if (!empty($img->image_url) && file_exists($img->image_url)) {
                            unlink($img->image_url);
                        }
                    }
                }
            }
            $this->products->deleteProduct($productId);
            $_SESSION['message'] = '✅ تم حذف المنتج بنجاح';
        }
        header('Location: ' . BASE_URL . 'products');
        exit;
    }

    // ============================================
    // دالة عرض الواجهات
    // ============================================
    private function view($view, $data = [])
    {
        extract($data);
        $viewPath = APP_PATH . 'views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("الملف $view غير موجود");
        }
    }
}