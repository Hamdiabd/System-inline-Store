<?php
class Brand extends Model {
    protected $table = 'brands';
    
    /**
     * جلب جميع العلامات التجارية النشطة
     */
    public function getAllActive() {
        $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC");
        return $this->db->resultSet();
    }
 //جلب عدد المنتجات لعلامه تجارية
    public function getProductCount($brandId) {
        $this->db->query("SELECT COUNT(*) as count FROM products WHERE brand_id = :brand_id AND status = 'active'");
        $this->db->bind(':brand_id', $brandId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }
      public function getBrands() {
        $this->db->query("SELECT id,name FROM {$this->table}
            WHERE is_active = 1 ORDER BY name ASC");
        return $this->db->resultSet();
    }
    
    /**
     * إنشاء علامة تجارية جديدة
     */
    public function create($data) {
        $this->db->query("INSERT INTO {$this->table} (name, logo, website, is_active) 
                        VALUES (:name, :logo, :website, :is_active)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':logo', $data['logo'] ?? 'default-brand.jpg');
        $this->db->bind(':website', $data['website'] ?? null);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * تحديث علامة تجارية
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET name = :name";
        
        if(isset($data['logo'])) {
            $sql .= ", logo = :logo";
        }
        
        if(isset($data['website'])) {
            $sql .= ", website = :website";
        }
        
        if(isset($data['is_active'])) {
            $sql .= ", is_active = :is_active";
        }
        
        $sql .= " WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':id', $id);
        
        if(isset($data['logo'])) {
            $this->db->bind(':logo', $data['logo']);
        }
        
        if(isset($data['website'])) {
            $this->db->bind(':website', $data['website']);
        }
        
        if(isset($data['is_active'])) {
            $this->db->bind(':is_active', $data['is_active']);
        }
        
        return $this->db->execute();
    }

}