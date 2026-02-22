<?php
class Cart extends Model {
    protected $table = 'cart_items';
    
    /**
     * جلب محتويات سلة مستخدم
     */
    public function getUserCart($userId) {
        $this->db->query("SELECT c.*, p.name, p.price, p.main_image,
                          (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as product_image
                          FROM {$this->table} c
                          JOIN products p ON p.id = c.product_id
                          WHERE c.user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    /**
     * إضافة منتج للسلة
     */
    public function addItem($userId, $productId, $quantity = 1) {
        // تحقق إذا كان المنتج موجود مسبقاً
        $this->db->query("SELECT * FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        $existing = $this->db->single();
        
        if($existing) {
            // تحديث الكمية
            $this->db->query("UPDATE {$this->table} SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id");
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':product_id', $productId);
        } else {
            // إضافة جديد
            $this->db->query("INSERT INTO {$this->table} (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':quantity', $quantity);
        }
        
        return $this->db->execute();
    }
    
    /**
     * تحديث كمية منتج في السلة
     */
    public function updateQuantity($userId, $productId, $quantity) {
        if($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }
        
        $this->db->query("UPDATE {$this->table} SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }
    
    /**
     * حذف منتج من السلة
     */
    public function removeItem($userId, $productId) {
        $this->db->query("DELETE FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }
    
    /**
     * إفراغ السلة
     */
    public function clearCart($userId) {
        $this->db->query("DELETE FROM {$this->table} WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
    
    /**
     * حساب إجمالي السلة
     */
    public function getCartTotal($userId) {
        $this->db->query("SELECT SUM(p.price * c.quantity) as total 
                          FROM {$this->table} c
                          JOIN products p ON p.id = c.product_id
                          WHERE c.user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    /**
     * عدد العناصر في السلة
     */
    public function getCartCount($userId) {
        $this->db->query("SELECT SUM(quantity) as count FROM {$this->table} WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}