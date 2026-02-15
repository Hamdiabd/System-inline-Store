<?php
class Product extends Model {
    
    // جلب جميع المنتجات
    public function getAllProducts() {
        $this->db->query("SELECT * FROM products ORDER BY id DESC");
        return $this->db->resultSet();
    }

    // جلب منتج واحد
    public function getProductById($id) {
        $this->db->query("SELECT * FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // إضافة منتج جديد
    public function addProduct($data) {
        $this->db->query("INSERT INTO products (name, price, description) VALUES (:name, :price, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':description', $data['description']);
        return $this->db->execute();
    }

    // تحديث منتج
    public function updateProduct($id, $data) {
        $this->db->query("UPDATE products SET name = :name, price = :price, description = :description WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':description', $data['description']);
        return $this->db->execute();
    }

    // حذف منتج
    public function deleteProduct($id) {
        $this->db->query("DELETE FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // البحث عن منتجات
    public function searchProducts($keyword) {
        $this->db->query("SELECT * FROM products WHERE name LIKE :keyword OR description LIKE :keyword ORDER BY id DESC");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }

    // جلب آخر المنتجات 
    public function getLatestProducts($limit = 5) {
        $this->db->query("SELECT * FROM products ORDER BY id DESC LIMIT $limit");
        return $this->db->resultSet();
    }
}