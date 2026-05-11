<?php

class ApiController extends Controller
{
    private $productModel;

    public function __construct()
    {
        // تحميل الموديل الخاص بالمنتجات
        $this->productModel = $this->model("Product");
    }

    /**
     * جلب قائمة المنتجات مع الترقيم والبحث
     * GET /api/products
     */
    public function products()
    {
        $page    = (int) $this->get('page', 1);
        $status  = $this->get('status', '');
        $search  = $this->get('search', '');
        $perPage = 10;

        try {
            $result = $this->productModel->getProductsPaginated($page, $perPage, $status, $search);

            $this->json([
                'success'    => true,
                'products'   => $result['products'],
                'pagination' => $result['pagination']
            ]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'حدث خطأ أثناء جلب البيانات'], 500);
        }
    }

    /**
     * جلب إحصائيات المنتجات للوحة التحكم
     * GET /api/stats-products
     */
    public function statsProducts()
    {
        try {
            $stats = $this->productModel->getProductStats();

            $this->json([
                'success'         => true,
                'total_products'  => (int) ($stats->total_products ?? 0),
                'active_products' => (int) ($stats->active_products ?? 0),
                'total_stock'     => (int) ($stats->total_stock ?? 0),
                'total_variants'  => (int) ($stats->total_variants ?? 0)
            ]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'فشل تحميل الإحصائيات'], 500);
        }
    }

    /**
     * جلب بيانات منتج واحد للتعديل
     * GET /api/product-edit/{id}
     */
    public function productEdit($productId = null)
    {
        $productId = $this->resolveId($productId);

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'معرف المنتج غير صحيح'], 400);
        }

        $product = $this->productModel->getProductById($productId);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        $this->json([
            'success' => true,
            'product' => $product
        ]);
    }

    /**
     * تحديث بيانات المنتج
     * POST /api/product-update/{id}
     */
    public function productUpdate($productId = null)
    {
        $productId = $this->resolveId($productId);

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'معرف المنتج مطلوب'], 400);
        }

        try {
            // التحقق من البيانات المرسلة (Validation بسيط)
            if (empty($_POST['product_name'])) {
                throw new Exception("اسم المنتج مطلوب");
            }

            $productData = [
                'name'        => filter_var($_POST['product_name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'description' => $_POST['description'] ?? null,
                'brand_id'    => !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null,
                'is_active'   => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1,
            ];

            $updated = $this->productModel->updateProduct($productId, $productData);

            if ($updated) {
                $this->json(['success' => true, 'message' => 'تم تحديث المنتج بنجاح']);
            } else {
                $this->json(['success' => false, 'message' => 'لم يتم إجراء أي تغييرات']);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * تبديل حالة المنتج (نشط / غير نشط)
     * POST /api/product-toggle-status
     */
    public function productToggleStatus()
    {

        // قراءة البيانات المرسلة كـ JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        $status = $input['is_active'] ?? null;
        if ($productId === null || $status === null) {
            $this->json(['success' => false, 'message' => 'بيانات غير مكتملة'], 400);
        }

        try {
            $this->productModel->toggleStatus($productId, $status);
            $this->json([
                'success' => true,
                'message' => $status == 1 ? 'تم تنشيط المنتج' : 'تم تعطيل المنتج'
            ]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'فشل تغيير الحالة'], 500);
        }
    }

    /**
     * حذف المنتج وصوره المرتبطة
     * DELETE /api/product-delete/{id}
     */
    public function productDelete($productId = null)
    {
        $productId = $this->resolveId($productId);

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'معرف المنتج غير صحيح'], 400);
        }

        try {
            // 1. جلب بيانات المنتج لحذف الصور فيزيائياً من السيرفر
            $product = $this->productModel->getProductById($productId);

            if ($product) {
                // حذف الصورة الأساسية
                $this->deleteFile($product->base_image_url);

                // حذف صور المتغيرات إذا وجدت
                if (!empty($product->variant_images)) {
                    // إذا كان النص JSON (حسب هيكل دبابيزك)، نقوم بفك التشفير
                    $images = is_string($product->variant_images) ? json_decode($product->variant_images) : $product->variant_images;

                    if (is_array($images) || is_object($images)) {
                        foreach ($images as $img) {
                            $path = is_object($img) ? $img->image_url : ($img['image_url'] ?? null);
                            $this->deleteFile($path);
                        }
                    }
                }
            }

            // 2. الحذف من قاعدة البيانات (سيتكفل ON DELETE CASCADE بحذف السجلات المرتبطة)
            $result = $this->productModel->deleteProduct($productId);

            if ($result) {
                $this->json(['success' => true, 'message' => 'تم حذف المنتج وكافة بياناته بنجاح']);
            } else {
                $this->json(['success' => false, 'message' => 'لا يمكن حذف المنتج (ربما لديه طلبات مرتبطة)'], 409);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'خطأ في النظام: ' . $e->getMessage()], 500);
        }
    }

    // --- وظائف مساعدة (Helper Methods) ---

    /**
     * التأكد من استخراج المعرف سواء من الروتر أو الرابط
     */
    private function resolveId($id)
    {
        if ($id !== null && is_numeric($id)) return (int)$id;

        $urlParts = explode('/', $_GET['url'] ?? '');
        $lastPart = end($urlParts);
        return is_numeric($lastPart) ? (int)$lastPart : null;
    }

    /**
     * حذف ملف من السيرفر بأمان
     */
    private function deleteFile($path)
    {
        if (!empty($path) && file_exists($path) && is_file($path)) {
            @unlink($path);
        }
    }
}
