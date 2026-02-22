<?php
class Category extends Model {
    protected $table = 'categories';
    
    /**
     * جلب جميع التصنيفات النشطة
     */
    public function getAllActive() {
        $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
        return $this->db->resultSet();
    }
    
    /**
     * جلب التصنيفات الرئيسية (بدون أب)
     */
    public function getMainCategories() {
        $this->db->query("SELECT 
            c.*,
            (SELECT COUNT(*) FROM products WHERE category_id = c.id AND status = 'active') as product_count
            FROM {$this->table} c
            WHERE c.parent_id IS NULL AND c.is_active = 1 
            ORDER BY c.sort_order ASC, c.name ASC");
        return $this->db->resultSet();
    }
    public function getCategories() {
        $this->db->query("SELECT id,name FROM {$this->table}
            WHERE is_active = 1 ORDER BY name ASC");
        return $this->db->resultSet();
    }
    
    /**
     * جلب التصنيفات الفرعية لتصنيف معين
     */
    public function getSubCategories($parentId) {
        $this->db->query("SELECT * FROM {$this->table} 
                          WHERE parent_id = :parent_id AND is_active = 1 
                          ORDER BY sort_order ASC, name ASC");
        $this->db->bind(':parent_id', $parentId);
        return $this->db->resultSet();
    }
    
    
    
    public function getBySlug($slug) {
        $this->db->query("SELECT * FROM {$this->table} WHERE slug = :slug AND is_active = 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }
    
    /**
     * إنشاء تصنيف جديد
     */
    public function create($data) {
        // إنشاء slug من الاسم
        $slug = $this->createSlug($data['name']);
        
        $this->db->query("INSERT INTO {$this->table} 
            (name, slug, image, parent_id, is_active, sort_order) 
            VALUES 
            (:name, :slug, :image, :parent_id, :is_active, :sort_order)");
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':image', $data['image'] ?? 'default-category.jpg');
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':sort_order', $data['sort_order'] ?? 0);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * تحديث تصنيف
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name";
        
        // تحديث slug فقط إذا تغير الاسم
        if(isset($data['name']) && !isset($data['slug'])) {
            $sql .= ", slug = :slug";
        }
        
        if(isset($data['image'])) {
            $sql .= ", image = :image";
        }
        
        if(isset($data['parent_id'])) {
            $sql .= ", parent_id = :parent_id";
        }
        
        if(isset($data['is_active'])) {
            $sql .= ", is_active = :is_active";
        }
        
        if(isset($data['sort_order'])) {
            $sql .= ", sort_order = :sort_order";
        }
        
        $sql .= " WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':id', $id);
        
        if(isset($data['name']) && !isset($data['slug'])) {
            $slug = $this->createSlug($data['name']);
            $this->db->bind(':slug', $slug);
        }
        
        if(isset($data['image'])) {
            $this->db->bind(':image', $data['image']);
        }
        
        if(isset($data['parent_id'])) {
            $this->db->bind(':parent_id', $data['parent_id']);
        }
        
        if(isset($data['is_active'])) {
            $this->db->bind(':is_active', $data['is_active']);
        }
        
        if(isset($data['sort_order'])) {
            $this->db->bind(':sort_order', $data['sort_order']);
        }
        
        return $this->db->execute();
    }
    
 
    
    /**
     * إنشاء slug من النص
     */
    private function createSlug($string) {
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('/[^\p{L}\p{N}\-]/u', '', $string);
        $string = mb_strtolower($string, 'UTF-8');
        return $string;
    }
    
    /**
     * البحث عن تصنيفات
     */
    public function search($keyword) {
        $this->db->query("SELECT * FROM {$this->table} 
                          WHERE name LIKE :keyword AND is_active = 1 
                          ORDER BY sort_order ASC, name ASC
                          LIMIT 10");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }
    
    /**
     * جلب شجرة التصنيفات (مع التصنيفات الفرعية)
     */
    public function getCategoryTree($parentId = null) {
        $categories = $this->getMainCategories();
        
        foreach($categories as $category) {
            $category->subcategories = $this->getSubCategories($category->id);
        }
        
        return $categories;
    }
    
public function getProductById($id) {
    $sql = "SELECT 
            p.*,
            c.`name` as category_name,
            b.`name` as brand_name
            FROM `{$this->table}` p
            LEFT JOIN `categories` c ON c.`id` = p.`category_id`
            LEFT JOIN `brands` b ON b.`id` = p.`brand_id`
            WHERE p.`id` = :id";
    
    $this->db->query($sql);
    $this->db->bind(':id', $id);
    return $this->db->single();
}
}