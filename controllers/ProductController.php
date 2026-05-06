<?php
class ProductController extends Controller
{

    public function index()
    {
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


    // عرض نموذج إضافة منتج
    public function create()
    {
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
    public function store()
    {
        $productModel = $this->model('Product');
        $file = $productModel->AddFile("Product");
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                "name" => $_POST["product_name"],
                "description" => $_POST["description"],
                'brand_id' => $_POST["brand_id"],
                "image" => $file,
                "is_active" => empty($_POST["is_active"]) ? $_POST["is_active"] : 1,
            ];
            $file = $productModel->save("Product", $data);
        }
    }
}
