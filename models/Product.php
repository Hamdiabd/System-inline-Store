<?php
class Product extends Model {
    public function getAll($table)
    {
        
        $this->db->query("SELECT * FROM {$table}");
                return $this->db->resultSet();
    }
    public function getOrder($table)
    {
        $this->db->query("SELECT * FROM {$table}");
            $data =[
                'order'=>$this->db->resultSet(),
                'count' =>$this->db->rowCount()
            ];

                return $data;
    }
    public function getuserName($table,$Names)
    {
        $this->db->query("SELECT * FROM {$table} WHERE role=:Name");
        $this->db->bind('Name',$Names);
            return $this->db->single();
    }
    public function getO($table)
    {
        

        $this->db->query("SELECT * FROM {$table}");
                return $this->db->resultSet();
    }
    public function getProduct($table)
    {
        

        $this->db->query("SELECT * FROM {$table}");
                return $this->db->resultSet();
    }
}
