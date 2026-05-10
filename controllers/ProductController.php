<?php
class ProductController extends Controller
{
    protected $products;
    protected $category;
    protected $brand;
    protected $warehouse;
    protected $supplier;

    function __construct()
    {
        $this->products  = $this->model("Product");
        $this->category  = $this->model("Category");
        $this->brand     = $this->model("Brand");
        $this->warehouse = $this->model("Warehouse");
        $this->supplier  = $this->model("Supplier");
    }

    // ============================================
    // عرض قائمة المنتجات
    // ============================================
    public function index()
    {
        $products = $this->products->getAllProductsWithDetails();
        $data = [
            'title'    => 'إدارة المنتجات',
            'products' => $products,
        ];
        $this->view('product/index', $data);
    }

    // ============================================
    // عرض صفحة التعديل
    // ============================================
    public function edit($productId = null)
    {
        if (!$productId) {
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        $product = $this->products->getProductById($productId);
        
        if (!$product) {
            $_SESSION['error'] = 'المنتج غير موجود';
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        $data = [
            'title'      => 'تعديل: ' . $product->name,
            'product'    => $product,
            'brands'     => $this->brand->getAll(),
            'categories' => $this->category->getAll(),
            'suppliers'  => $this->supplier->getAll(),
            'warehouse'  => $this->warehouse->getAll(),
        ];
        $this->view('product/edit', $data);
    }

    // ============================================
    // تحديث منتج
    // ============================================
    public function update($productId = null)
    {
        if (!$productId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        $this->products->db->beginTransaction();

        try {
            // تحديث المنتج الأساسي
            $productData = [
                'name'        => $_POST['product_name'],
                'description' => $_POST['description'] ?? null,
                'brand_id'    => !empty($_POST['brand_id']) ? $_POST['brand_id'] : null,
                'is_active'   => $_POST['is_active'] ?? 1,
            ];

            // رفع صورة جديدة إن وجدت
            $newImage = $this->products->AddFile("product_image");
            if ($newImage) {
                // حذف الصورة القديمة
                $oldProduct = $this->products->getProductById($productId);
                if (!empty($oldProduct->base_image_url) && file_exists($oldProduct->base_image_url)) {
                    unlink($oldProduct->base_image_url);
                }
                $productData['base_image_url'] = $newImage;
            }

            // تحديث المنتج
            $this->products->updateProduct($productId, $productData);

            $this->products->commit();
            $_SESSION['message'] = '✅ تم تحديث المنتج بنجاح';
            header('Location: ' . BASE_URL . 'products');
            exit;

        } catch (Exception $e) {
            $this->products->rollBack();
            $_SESSION['error'] = '❌ ' . $e->getMessage();
            header('Location: ' . BASE_URL . 'product/edit/' . $productId);
            exit;
        }
    }

    // ============================================
    // حذف منتج
    // ============================================
    public function delete($productId = null)
    {
        if (!$productId) {
            header('Location: ' . BASE_URL . 'products');
            exit;
        }

        try {
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
        } catch (Exception $e) {
            $_SESSION['error'] = '❌ فشل الحذف: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'products');
        exit;
    }

    // ============================================
    // عرض صفحة الإضافة
    // ============================================

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
        $this->products->db->beginTransaction();

        try {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                // ✅ 1. رفع الصورة الأساسية
                $baseImagePath = $this->products->AddFile("product_image");

                // ✅ 2. تجهيز بيانات المنتج
                $productData = [
                    "name"           => $_POST["product_name"],
                    "description"    => $_POST["description"] ?? null,
                    "brand_id"       => !empty($_POST["brand_id"]) ? $_POST["brand_id"] : null,
                    "base_image_url" => $baseImagePath ?? null,
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
                $variantsIdImagePath = "false";

                foreach ($variants as $key => $variant) {
                    if (empty(trim($variant['sku'])) || !isset($variant['price'])) {
                        throw new Exception("المتغير رقم " . ($key + 1) . ": SKU والسعر مطلوبان");
                    }

                    $variantsIdImagePath = $this->uploadimages($key);
                }
                $variantData = [
                    'sku'          => $variant['sku'],
                    'size_option'  => $variant['size_option'] ?? null,
                    'color_option' => $variant['color_option'] ?? null,
                    'packaging'    => $variant['packaging'] ?? null,
                    'price'        => $variant['price'],
                    'weight_kg'    => $variant['weight_kg'] ?? null,
                    'image_url'    => $variantsIdImagePath ?? null,
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
            $this->products->db->commit();

            $_SESSION['message'] = '✅ تم إضافة المنتج بنجاح';
            header('Location: ' . BASE_URL . 'products');
            exit;
        } catch (Exception $e) {
            // ✅ 9. فشل - تراجع
            $this->products->db->rollBack();
            $_SESSION['error'] = '❌ ' . $e->getMessage();
            // header('Location: ' . BASE_URL . 'product/create');
            // exit;
        }
    }
    public function uploadimages($key)
    {
        if (empty($_FILES["variants"]['name'][$key]["image"]) || $_FILES["variants"]['error'][$key]["image"] !== 0) {
            return null;
        }
        $_FILES["variant_tmp"] =
            [
                'name' => $_FILES["variants"]['name'][$key]["image"],
                'type' => $_FILES["variants"]['type'][$key]["image"],
                'tmp_name' => $_FILES["variants"]['tmp_name'][$key]["image"],
                'error' => $_FILES["variants"]['error'][$key]["image"],
                'size' => $_FILES["variants"]['size'][$key]["image"],
            ];

        $variantsIdImagePath = $this->products->AddFile("variant_tmp");
        return $variantsIdImagePath;
    }

    // ============================================
    // دالة عرض الواجهات
    // ============================================
}
