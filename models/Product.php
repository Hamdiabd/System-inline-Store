<?php
class Product extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table = "product";
    }

    // ============================================
    // دوال المعاملات (تحل مشكلة protected $db)
    // ============================================
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollBack()
    {
        return $this->db->rollback();
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

    // ============================================
    // جلب منتج مع متغيراته (لحذف الصور)
    // ============================================
    public function getProductById($productId)
    {
        $this->db->query("SELECT * FROM product WHERE product_id = :id");
        $this->db->bind(':id', $productId);
        $product = $this->db->single();

        if ($product) {
            $this->db->query("SELECT image_url FROM product_variant WHERE product_id = :id");
            $this->db->bind(':id', $productId);
            $product->variant_images = $this->db->resultSet();
        }

        return $product;
    }
}