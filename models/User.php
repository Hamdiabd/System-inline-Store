<?php
class User extends Model {
    protected $table = 'users';
    
    /**
     * البحث عن مستخدم بالبريد الإلكتروني
     */
    public function findByEmail($email) {
        $this->db->query("SELECT u.*, p.avatar, p.bio 
                          FROM {$this->table} u 
                          LEFT JOIN profiles p ON p.user_id = u.id 
                          WHERE u.email = :email AND u.status = 'active'");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
    
    /**
     * جلب مستخدم بواسطة ID
     */
    public function getById1($id) {
        $this->db->query("SELECT u.*, p.avatar, p.bio 
                    FROM {$this->table} u 
                    LEFT JOIN profiles p ON p.user_id = u.id 
                    WHERE u.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * تسجيل الدخول
     */
    public function login($email, $password) {
        $user = $this->findByEmail($email);
        
        if($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }
    
    /**
     * تسجيل مستخدم جديد (عميل)
     */
    public function register($data) {
        try {
            $this->db->beginTransaction();
            
            // تشفير كلمة المرور
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // إدخال المستخدم
            $this->db->query("INSERT INTO users (name, email, password, phone, role, status) 
                              VALUES (:name, :email, :password, :phone, 'customer', 'active')");
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':phone', $data['phone']);
            
            if($this->db->execute()) {
                $userId = $this->db->lastInsertId();
                
                // إنشاء ملف شخصي
                $this->db->query("INSERT INTO profiles (user_id, avatar) VALUES (:user_id, 'default.jpg')");
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
                
                $this->db->commit();
                return $userId;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * تحديث آخر دخول
     */
    public function updateLastLogin($userId) {
        $this->db->query("UPDATE users SET last_login = NOW() WHERE id = :id");
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }
}