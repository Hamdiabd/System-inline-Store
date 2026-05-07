<?php
class Product extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table = "product";
    }
    // دالة جلب المنتجات مع pagination والبحث والفلترة
public function getProductsPaginated($page = 1, $perPage = 10, $status = '', $search = '')
{
    $where = [];
    $params = [];

    if ($status !== '') {
        $where[] = "p.is_active = :status";
        $params[':status'] = $status;
    }

    if ($search !== '') {
        $where[] = "(p.name LIKE :search OR p.description LIKE :search2)";
        $params[':search'] = "%$search%";
        $params[':search2'] = "%$search%";
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // إجمالي المنتجات
    $this->db->query("SELECT COUNT(DISTINCT p.product_id) as total FROM product p $whereClause");
    foreach ($params as $key => $val) {
        $this->db->bind($key, $val);
    }
    $total = $this->db->fetch()->total;

    // حساب الإزاحة
    $offset = ($page - 1) * $perPage;

    // جلب البيانات
    $this->db->query("
        SELECT 
            p.product_id,
            p.name AS product_name,
            p.description,
            p.base_image_url,
            p.is_active,
            p.created_at,
            b.name AS brand_name,
            COUNT(DISTINCT pv.variant_id) AS variant_count,
            MIN(pv.price) AS min_price,
            MAX(pv.price) AS max_price,
            COALESCE(SUM(inv.quantity_in_stock), 0) AS total_stock,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories_names
        FROM product p
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        LEFT JOIN product_variant pv ON p.product_id = pv.product_id
        LEFT JOIN inventory inv ON pv.variant_id = inv.variant_id
        LEFT JOIN product_category pc ON p.product_id = pc.product_id
        LEFT JOIN category c ON pc.category_id = c.category_id
        $whereClause
        GROUP BY p.product_id
        ORDER BY p.created_at DESC
        LIMIT $offset, $perPage
    ");
    foreach ($params as $key => $val) {
        $this->db->bind($key, $val);
    }
    $products = $this->db->fetchAll();

    return [
        'products' => $products,
        'pagination' => [
            'total'        => (int)$total,
            'per_page'     => $perPage,
            'current_page' => (int)$page,
            'total_pages'  => (int)ceil($total / $perPage)
        ]
    ];
}

// دالة إحصائيات سريعة
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
    return $this->db->fetch();
}

// تبديل حالة المنتج (نشط/غير نشط)
public function toggleStatus($productId, $status)
{
    $this->db->query("UPDATE product SET is_active = :status WHERE product_id = :id");
    $this->db->bind(':status', $status);
    $this->db->bind(':id', $productId);
    return $this->db->execute();
}

// حذف منتج
public function deleteProduct($productId)
{
    $this->table = 'product';
    return $this->delete($productId);
}
    public function getOrder($table)
    {
        $this->db->query("SELECT * FROM {$table}");
        $data = [
            'order' => $this->db->resultSet(),
            'count' => $this->db->rowCount()
        ];

        return $data;
    }
    public function save($table, $data)
    {
        $this->table = $table;
        if (!empty($data->name) && !empty($data->description) && !empty($data->image)) {
            return $this->insert($data);
        }
        return false;
    }
    /*
    
     */
    public function addVariant($productId, $data)
    {
        $this->table = "product_variant";
        $variantData = [
            'product_id' => $productId,
            'SKU' => $data['sku'],
            'size_option' => $data['size_option'],
            'color_option' => $data['color_option'],
            'packaging' => $data['packaging'],
            'price' => $data['price'],
            'weight_kg' => $data['weight_kg'] ?? null,
            'image_url' =>  $data['image_url'],
        ];
        $this->insert($data);
        return $this->db->lastInsertId();
    }
    /*

     */
    public function attachSupplier($productId, $supplier)
    {
        $this->table = "product_supplier";
        $this->insert([
            'product_id' => $productId,
            'supplier_id' => $supplier['supplier_id'],
            'supply_price' => $supplier['supply_price'],
            'lead_time_days ' =>$supplier['lead_time_days'] ?? null,
            'minimum_order ' =>$supplier['minimum_order']??1,
        ]);
    }
    /*

     */
    public function addInventory($variantId, $Inventory)
    {
        $this->table = "inventory";
        $this->insert([
            'inventory_id' => $variantId,
            'warehouse_id' => $Inventory['warehouse_id'],
            'quantity_in_stock ' =>$Inventory['quantity'],
            'reorder_level ' =>$Inventory['reorder_level'] ?? 10,
            'reorder_quantity ' =>$Inventory['reorder_quantity'] ?? 50,
        ]);
    }
    public function getO($table)
    {


        $this->db->query("SELECT * FROM {$table}");
        return $this->db->resultSet();
    }
    public function getProduct($table)
    {


        $this->db->query("SELECT * FROM {$table}");
        return $this->db->resultSet();
    }
}
