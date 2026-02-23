<?php
class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = new Database();
    }

    protected function getAll($table) {
        $this->db->query("SELECT * FROM $table ORDER BY id DESC");
        return $this->db->resultSet();
    }

    protected function getById($table, $id) {
        $this->db->query("SELECT * FROM $table WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
 
}