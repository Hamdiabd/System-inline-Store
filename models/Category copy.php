<?php
class Category extends Model
{
    #[Override]
    function __construct()
    {
        parent::__construct();
        $this->table = "category";
    }
    public function save($productId, $data)
    {
        $this->table = "product_categroy";
        foreach ($data as $catId) {
            $this->insert([
                'product_id' => $productId,
                'category_id' => $catId
            ]);
        }
    }
}
