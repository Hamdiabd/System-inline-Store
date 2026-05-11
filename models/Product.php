<?php
class Product extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table = "product";
    }
    // دالة جلب المنتجات مع تفاصيل إضافية
    public function getAllProductsWithDetails()
    {
        $this->db->query("
        SELECT 
            p.*,
            b.name AS brand_name,
            MIN(pv.price) AS min_price,
            MAX(pv.price) AS max_price,
            SUM(inv.quantity_in_stock) AS total_stock
        FROM product p
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        LEFT JOIN product_variant pv ON p.product_id = pv.product_id
        LEFT JOIN inventory inv ON pv.variant_id = inv.variant_id
        GROUP BY p.product_id
        ORDER BY p.product_id DESC
    ");
        return $this->db->resultSet();
    }

    // دالة جلب منتج واحد مع كامل التفاصيل
    public function getProductById($productId)
    {
        // المنتج الأساسي
        $this->db->query("
        SELECT p.*, b.name AS brand_name 
        FROM product p 
        LEFT JOIN brand b ON p.brand_id = b.brand_id 
        WHERE p.product_id = :id
    ");
        $this->db->bind(':id', $productId);
        $product = $this->db->single();

        if ($product) {
            // الأقسام
            $this->db->query("
            SELECT c.category_id, c.name 
            FROM product_category pc 
            JOIN category c ON pc.category_id = c.category_id 
            WHERE pc.product_id = :id
        ");
            $this->db->bind(':id', $productId);
            $product->categories = $this->db->resultSet();

            // المتغيرات
            $this->db->query("SELECT * FROM product_variant WHERE product_id = :id");
            $this->db->bind(':id', $productId);
            $product->variants = $this->db->resultSet();

            // الموردين
            $this->db->query("
            SELECT ps.*, s.company_name 
            FROM product_supplier ps 
            JOIN supplier s ON ps.supplier_id = s.supplier_id 
            WHERE ps.product_id = :id
        ");
            $this->db->bind(':id', $productId);
            $product->suppliers = $this->db->resultSet();

            // المخزون
            $this->db->query("
            SELECT inv.*, w.name AS warehouse_name, pv.SKU
            FROM inventory inv
            JOIN warehouse w ON inv.warehouse_id = w.warehouse_id
            JOIN product_variant pv ON inv.variant_id = pv.variant_id
            WHERE pv.product_id = :id
        ");
            $this->db->bind(':id', $productId);
            $product->inventory = $this->db->resultSet();

            // صور المتغيرات
            $this->db->query("SELECT image_url FROM product_variant WHERE product_id = :id AND image_url IS NOT NULL");
            $this->db->bind(':id', $productId);
            $product->variant_images = $this->db->resultSet();
        }

        return $product;
    }

    // تحديث منتج
    public function updateProduct($productId, $data)
    {
        $this->table = 'product';

        $setClause = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        $params[':id'] = $productId;

        $sql = "UPDATE product SET " . implode(', ', $setClause) . " WHERE product_id = :id";
        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->execute();
    }
    // ============================================
    // إنشاء منتج جديد
    // ============================================
    public function createProduct($data)
    {
        $this->table = 'product';
        return $this->insert($data);
    }

    // ============================================
    // ربط الأقسام بالمنتج
    // ============================================
    public function attachCategories($productId, $categoryIds)
    {
        $this->table = 'product_category';
        foreach ($categoryIds as $catId) {
            $this->insert([
                'product_id'  => $productId,
                'category_id' => $catId
            ]);
        }
    }

    // ============================================
    // إضافة متغير منتج
    // ============================================
    public function addVariant($productId, $data)
    {
        $this->table = "product_variant";
        $variantData = [
            'product_id'   => $productId,
            'SKU'          => $data['sku'],
            'size_option'  => $data['size_option'] ?? null,
            'color_option' => $data['color_option'] ?? null,
            'packaging'    => $data['packaging'] ?? null,
            'price'        => $data['price'],
            'weight_kg'    => $data['weight_kg'] ?? null,
            'image_url'    => $data['image_url'] ?? null,
        ];
        return $this->insert($variantData);
    }

    // ============================================
    // ربط مورد بالمنتج
    // ============================================
    public function attachSupplier($productId, $supplier)
    {
        $this->table = "product_supplier";
        $this->insert([
            'product_id'     => $productId,
            'supplier_id'    => $supplier['supplier_id'],
            'supply_price'   => $supplier['supply_price'],
            'lead_time_days' => $supplier['lead_time_days'] ?? null,
            'minimum_order'  => $supplier['minimum_order'] ?? 1,
        ]);
    }

    // ============================================
    // إضافة مخزون أولي
    // ============================================
    public function addInventory($variantId, $inventory)
    {
        $this->table = "inventory";
        $this->insert([
            'variant_id'        => $variantId,
            'warehouse_id'      => $inventory['warehouse_id'],
            'quantity_in_stock' => $inventory['quantity'],
            'reorder_level'     => $inventory['reorder_level'] ?? 10,
            'reorder_quantity'  => $inventory['reorder_quantity'] ?? 50,
        ]);
    }

    // ============================================
    // حذف منتج
    // ============================================
    public function deleteProduct($productId)
    {
        $this->table = 'product';
        return $this->delete($productId);
    }

    // ============================================
    // جلب جميع المنتجات
    // ============================================
    public function getAllProducts()
    {
        $this->db->query("SELECT * FROM product ORDER BY created_at DESC");
        return $this->db->resultSet();
    }
    // في models/Product.php - أضف هذه الدوال:

    // ============================================
    // منتجات مع pagination للـ API
    // ============================================
    public function getProductsPaginated($page = 1, $perPage = 10, $status = '', $search = '')
    {
        $where = [];
        $params = [];

        if ($status !== '') {
            $where[] = "p.is_active = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $where[] = "(p.name LIKE :search OR p.description LIKE :search2)";
            $params[':search'] = "%$search%";
            $params[':search2'] = "%$search%";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // العدد الكلي
        $this->db->query("SELECT COUNT(DISTINCT p.product_id) as total FROM product p $whereClause");
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $total = $this->db->single()->total;

        // البيانات
        $offset = ($page - 1) * $perPage;
        $this->db->query("
        SELECT 
            p.*,
            b.name AS brand_name,
            COUNT(DISTINCT pv.variant_id) AS variant_count,
            MIN(pv.price) AS min_price,
            MAX(pv.price) AS max_price,
            SUM(inv.quantity_in_stock) AS total_stock,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories_names
        FROM product p
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        LEFT JOIN product_variant pv ON p.product_id = pv.product_id
        LEFT JOIN inventory inv ON pv.variant_id = inv.variant_id
        LEFT JOIN product_category pc ON p.product_id = pc.product_id
        LEFT JOIN category c ON pc.category_id = c.category_id
        $whereClause
        GROUP BY p.product_id
        ORDER BY p.product_id DESC
        LIMIT $offset, $perPage
    ");
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return [
            'products' => $this->db->resultSet(),
            'pagination' => [
                'total'        => (int)$total,
                'per_page'     => (int)$perPage,
                'current_page' => (int)$page,
                'total_pages'  => (int)ceil($total / $perPage)
            ]
        ];
    }

    // ============================================
    // إحصائيات المنتجات
    // ============================================
    public function getProductStats()
    {
        $this->db->query("
        SELECT 
            COUNT(DISTINCT p.product_id) as total_products,
            COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.product_id END) as active_products,
            COALESCE(SUM(inv.quantity_in_stock), 0) as total_stock,
            COUNT(DISTINCT pv.variant_id) as total_variants
        FROM product p
        LEFT JOIN product_variant pv ON p.product_id = pv.product_id
        LEFT JOIN inventory inv ON pv.variant_id = inv.variant_id
    ");
        return $this->db->single();
    }

    // ============================================
    // تبديل حالة المنتج
    // ============================================
    public function toggleStatus($productId, $status)
    {
        $this->db->query("UPDATE product SET is_active = :status WHERE product_id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $productId);
        return $this->db->execute();
    }
}
