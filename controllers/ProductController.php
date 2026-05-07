<?php
class ProductController extends Controller
{
    protected $products;
    protected $Category;
    protected $Brand;
    protected $warehouse;
    protected $productModel;
    function __construct()
    {
        $this->products = $this->model('Product');
        $this->warehouse = $this->model('Warehouse');
        $this->Category = $this->model('Category');
        $this->Brand = $this->model('Brand');
        $this->productModel = $this->products;
    }


    public function index()
    {
        // استقبال معاملات الفلتر والصفحة
        $page   = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $perPage = 10; // عدد العناصر في الصفحة

        // جلب البيانات
        $result   = $this->productModel->getProductsPaginated($page, $perPage, $status, $search);
        $products = $result['products'];
        $pagination = $result['pagination'];

        // جلب الإحصائيات
        $stats = $this->productModel->getProductStats();

        $data = [
            'title'       => 'إدارة المنتجات',
            'breadcrumb'  => 'المنتجات',
            'products'    => $products,
            'pagination'  => $pagination,
            'stats'       => $stats,
            'search'      => $search,
            'status'      => $status,
        ];

        $this->view('product/index', $data);
    }

    // دالة تبديل الحالة (تستقبل POST)
    public function toggleStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $status    = $_POST['is_active'] ?? 0;
            if ($productId) {
                $this->productModel->toggleStatus($productId, $status);
                $_SESSION['message'] = 'تم تحديث حالة المنتج.';
            }
        }
        header('Location: ' . BASE_URL . 'products?' . http_build_query($_GET));
        exit;
    }

    public function delete($productId)
    {
        if ($productId) {
            $this->productModel->deleteProduct($productId);
            $_SESSION['message'] = 'تم حذف المنتج بنجاح.';
        }
        header('Location: ' . BASE_URL . 'products');
        exit;
    }
    public function index()
    {
        $products = $this->products->getAllProducts();
        //$latestProducts = $this->products->getLatestProducts();

        $data = [
            'title' => 'المنتجات',
            'products' => $products,
            //'latest_products' => $latestProducts
        ];
        $this->view('product/index', $data);
    }


    // عرض نموذج إضافة منتج
    public function create()
    {
        $brand = $this->Brand->getAll();
        $categories = $this->Category->getAll();
        $warehouse = $this->warehouse->getAll();
        $data = [
            'title' => 'إضافة منتج جديد',
            'brands' =>  $brand,
            'categories' =>  $categories,
            'warehouse' =>  $warehouse,
        ];
        $this->view('product/create', $data);
    }

    // حفظ منتج جديد
    public function store()
    {
        try {


            $file = $this->products->AddFile("product_image", "Product");
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $data = [
                    "name" => $_POST["product_name"],
                    "description" => $_POST["description"],
                    'brand_id' => $_POST["brand_id"] ?? null,
                    "base_image_url" => $file,
                    "is_active" => empty($_POST["is_active"]) ? $_POST["is_active"] : 1,
                ];
                $productId = $this->products->save("Product", $data);
                //Add Categoryes
                $categoriesId = $_POST['categories'] ?? [];
                if (!empty($categoriesId)) {
                    $this->Category->save($productId, $categoriesId);
                }
                /*
            حفظ المتغيرات 
            */
                $variants = $_POST['variants'] ?? [];
                foreach ($variants as $key => $variant) {
                    if (empty(trim($variant['sku'])) || !isset($variant['price'])) {
                        throw new Exception("  المتغير رقم" . ($key + 1) . "sku والسعر مطلوبان");
                    }

                    $imagevariantPath = false;
                    /*
                */
                    if (!empty($_FILES['variants']['name'][$key]['image'])) {
                        $tmpfile = [
                            'name' => $_FILES['variants']['name'][$key]['image'],
                            'type' => $_FILES['variants']['type'][$key]['image'],
                            'tmp_name' => $_FILES['variants']['tmp_name'][$key]['image'],
                            'error' => $_FILES['variants']['error'][$key]['image'],
                            'size' => $_FILES['variants']['size'][$key]['image'],
                        ];
                        $imagevariantPath = $this->products->AddFile($tmpfile);
                    }
                    $variantData = [
                        'sku' => $variant['sku'],
                        'size_option' => $variant['size_option'],
                        'color_option' => $variant['color_option'],
                        'packaging' => $variant['packaging'],
                        'image_url' => $imagevariantPath,
                    ];

                    $variantID = $this->products->addVariant($productId, $variantData);

                    $variantsId[] = $variantID;
                }
                /*
                اضافة المورديين
                 */


                $suppliers = $_POST['suppliers'] ?? [];
                foreach ($suppliers as $supplier) {
                    if (empty($supplier['supplier_id']) || empty($supplier['supply_price'])) continue;
                    $this->products->attachSupplier($productId,$supplier);
                }
                
                
                $inventoryData = $_POST['inventory'] ?? [];
                if (!empty($inventoryData) && !empty($variantsId));
                $firstvariantid = $variantsId[0];
                foreach ($inventoryData as $inv) {
                    if (empty($inv['warehouse_id'])) continue;
                    $this->products->addInventory($firstvariantid,$inv);
                }
            }
        } catch (Exception $e) {
        }
    }
}
