<?php
class Model {
    protected $db;
    function __construct()
    {
        $this->db= new Database;
    }
      public function getAll($table)
     {
         $this->db->query("SELECT * FROM {$table}");
         return $this->db->resultSet();
     }
}