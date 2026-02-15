<?php
class User extends Model {
    
    public function getAllUsers() {
        return $this->getAll('users');
    }

    public function getUserById($id) {
        return $this->getById('users', $id);
    }

    public function getUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function addUser($data) {
        $this->db->query("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        return $this->db->execute();
    }
}