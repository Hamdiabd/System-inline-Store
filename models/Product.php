<?php
class Product extends Model
{
    #[Override]
    function __construct()
    {
        parent::__construct();
        $this->table = "product";
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
