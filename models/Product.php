<?php
class Product extends Model
{
    public function getAll($table)
    {
        $this->db->query("SELECT * FROM {$table}");
        return $this->db->resultSet();
    }
    public function getOrder($table)
    {
        $this->db->query("SELECT * FROM {$table}");
        $data = [
            'order' => $this->db->resultSet(),
            'count' => $this->db->rowCount()
        ];

        return $data;
    }
    public function save($table, $data)
    {
    	
        if(!empty($data->name)&& !empty($data->description)&&!empty($data->image))
            {
                $this->insert($data);
                
            }
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
