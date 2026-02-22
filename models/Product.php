<?php
class Product extends Model {
    protected $table = 'products';
    
    public function getAllForAdmin() {
        $this->db->query("SELECT 
            p.*,
            c.name as category_name,
            b.name as brand_name,
            (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
            (SELECT COUNT(*) FROM order_items WHERE product_id = p.id) as order_count
            FROM {$this->table} p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN brands b ON b.id = p.brand_id
            ORDER BY p.created_at DESC");
        
        return $this->db->resultSet();
    }
    
    /**
     * جلب منتج واحد مع كل التفاصيل
     */
public function getProductById($id) {
    $sql = "SELECT 
            p.*,
            c.`name` as category_name,
            c.`id` as category_id,
            b.`name` as brand_name,
            b.`id` as brand_id
            FROM `{$this->table}` p
            LEFT JOIN `categories` c ON c.`id` = p.`category_id`
            LEFT JOIN `brands` b ON b.`id` = p.`brand_id`
            WHERE p.`id` = :id";
    
    $this->db->query($sql);
    $this->db->bind(':id', $id);
    return $this->db->single();
}
        public function create($data) {
        try {
            // بدء معاملة قاعدة البيانات
            $this->db->beginTransaction();
            
            // إنشاء slug من الاسم
            $slug = $this->createSlug($data['name']);
            
            // التأكد من عدم تكرار slug
            $originalSlug = $slug;
            $counter = 1;
            while($this->slugExists($slug)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            // إدخال المنتج
            $sql = "INSERT INTO {$this->table} 
                    (`name`, `slug`, `price`, `category_id`, `brand_id`, `stock`, `description`, `main_image`, `featured`, `status`, `created_at`) 
                    VALUES 
                    (:name, :slug, :price, :category_id, :brand_id, :stock, :description, :main_image, :featured, :status, NOW())";
            
            $this->db->query($sql);
            
            // ربط القيم
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':slug', $slug);
            $this->db->bind(':price', $data['price']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':stock', $data['stock']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':main_image', $data['main_image']);
            $this->db->bind(':featured', $data['featured']);
            $this->db->bind(':status', $data['status']);
            
            if($this->db->execute()) {
                $productId = $this->db->lastInsertId();
                
                // إضافة الصورة إلى جدول الصور إذا لم تكن افتراضية
                if($data['main_image'] != 'default.jpg') {
                    $sql = "INSERT INTO `product_images` (`product_id`, `image`, `is_primary`, `sort_order`) 
                            VALUES (:product_id, :image, 1, 0)";
                    
                    $this->db->query($sql);
                    $this->db->bind(':product_id', $productId);
                    $this->db->bind(':image', $data['main_image']);
                    $this->db->execute();
                }
                
                // تأكيد المعاملة
                $this->db->commit();
                return $productId;
            }
            
            // فشل الإدخال
            $this->db->rollBack();
            return false;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("خطأ في إنشاء المنتج: " . $e->getMessage());
            return false;
        }
    }/**
 * تحديث منتج
 * @param int $id معرف المنتج
 * @param array $data البيانات الجديدة
 * @return bool
 */
public function update($id, $data) {
    try {
        $this->db->beginTransaction();
        
        $sql = "UPDATE {$this->table} SET 
                `name` = :name,
                `price` = :price,
                `category_id` = :category_id,
                `brand_id` = :brand_id,
                `stock` = :stock,
                `description` = :description,
                `main_image` = :main_image,
                `featured` = :featured,
                `status` = :status
                WHERE `id` = :id";
        
        $this->db->query($sql);
        
        // ربط القيم
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':brand_id', $data['brand_id']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':main_image', $data['main_image']);
        $this->db->bind(':featured', $data['featured']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        $result = $this->db->execute();
        
        // تحديث الصورة في جدول الصور إذا تغيرت
        if($data['main_image'] != 'default.jpg') {
            // حذف الصورة القديمة من جدول الصور
            $this->db->query("DELETE FROM `product_images` WHERE `product_id` = :product_id");
            $this->db->bind(':product_id', $id);
            $this->db->execute();
            
            // إضافة الصورة الجديدة
            $sql = "INSERT INTO `product_images` (`product_id`, `image`, `is_primary`, `sort_order`) 
                    VALUES (:product_id, :image, 1, 0)";
            $this->db->query($sql);
            $this->db->bind(':product_id', $id);
            $this->db->bind(':image', $data['main_image']);
            $this->db->execute();
        }
        
        $this->db->commit();
        return $result;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("خطأ في تحديث المنتج: " . $e->getMessage());
        return false;
    }
}

/**
 * جلب صور المنتج
 * @param int $productId
 * @return array
 */
public function getProductImages($productId) {
    $sql = "SELECT * FROM `product_images` WHERE `product_id` = :product_id ORDER BY `is_primary` DESC, `sort_order` ASC";
    $this->db->query($sql);
    $this->db->bind(':product_id', $productId);
    return $this->db->resultSet();
}
    
    
    private function createSlug($string) {
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('/[^a-zA-Z0-9\-]/', '', $string);
        $string = strtolower($string);
        return $string;
    }
        private function slugExists($slug) {
        $this->db->query("SELECT id FROM {$this->table} WHERE slug = :slug");
        $this->db->bind(':slug', $slug);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
    
    /**
     * جلب جميع التصنيفات النشطة
     */
        public function getFeaturedProducts($limit = 4) {
        $this->db->query("SELECT 
            p.*,
            c.name as category_name,
            (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as main_image
            FROM {$this->table} p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.featured = 1 AND p.status = 'active'
            ORDER BY p.created_at DESC
            LIMIT :limit");
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * جلب آخر المنتجات المضافة
     * @param int $limit عدد المنتجات المطلوبة
     * @return array مصفوفة المنتجات
     */
    public function getLatestProducts($limit = 4) {
        $this->db->query("SELECT 
            p.*,
            c.name as category_name,
            (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as main_image
            FROM {$this->table} p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.status = 'active'
            ORDER BY p.created_at DESC
            LIMIT :limit");
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * جلب المنتجات حسب الفئة
     * @param int $categoryId معرف الفئة
     * @param int $limit عدد المنتجات
     * @return array مصفوفة المنتجات
     */
    public function getProductsByCategory($categoryId, $limit = 10) {
        $this->db->query("SELECT 
            p.*,
            (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as main_image
            FROM {$this->table} p
            WHERE p.category_id = :category_id AND p.status = 'active'
            ORDER BY p.created_at DESC
            LIMIT :limit");
        
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    public function getCategories() {
        $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
        return $this->db->resultSet();
    }
    
    /**
     * جلب جميع العلامات التجارية النشطة
     */
    public function getBrands() {
        $this->db->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC");
        return $this->db->resultSet();
    }

/**
 * حذف منتج
 * @param int $id معرف المنتج
 * @return bool
 */
public function delete($id) {
    try {
        $this->db->beginTransaction();
        
        // 1️⃣ حذف الصور المرتبطة من جدول product_images
        $this->db->query("DELETE FROM `product_images` WHERE `product_id` = :product_id");
        $this->db->bind(':product_id', $id);
        $this->db->execute();
        
        // 2️⃣ حذف المنتج من جدول products
        $this->db->query("DELETE FROM {$this->table} WHERE `id` = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->execute();
        
        $this->db->commit();
        return $result;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("خطأ في حذف المنتج: " . $e->getMessage());
        return false;
    }
}
public function updateStatus($id, $status) {
    $this->db->query("UPDATE {$this->table} SET `status` = :status WHERE `id` = :id");
    $this->db->bind(':status', $status);
    $this->db->bind(':id', $id);
    return $this->db->execute();
}
}
