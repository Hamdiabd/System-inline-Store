<?php
class Wishlist extends Model {
    protected $table = 'wishlists';
    
    /**
     * جلب قائمة مفضلة مستخدم
     */
    public function getUserWishlist($userId) {
        $this->db->query("SELECT w.*, p.name, p.price, p.main_image,
                          (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as product_image
                          FROM {$this->table} w
                          JOIN products p ON p.id = w.product_id
                          WHERE w.user_id = :user_id
                          ORDER BY w.created_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    /**
     * إضافة منتج للمفضلة
     */
    public function addToWishlist($userId, $productId) {
        // تحقق من عدم التكرار
        $this->db->query("SELECT * FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        $existing = $this->db->single();
        
        if($existing) {
            return true; // موجود بالفعل
        }
        
        $this->db->query("INSERT INTO {$this->table} (user_id, product_id) VALUES (:user_id, :product_id)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }
    
    /**
     * إزالة منتج من المفضلة
     */
    public function removeFromWishlist($userId, $productId) {
        $this->db->query("DELETE FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }
    
    /**
     * التحقق إذا كان المنتج في المفضلة
     */
    public function isInWishlist($userId, $productId) {
        $this->db->query("SELECT * FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->single() ? true : false;
    }
}