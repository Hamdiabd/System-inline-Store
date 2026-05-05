<?php
class Home extends Model {
    public function getAll_at($table)
    {
        
        $this->db->query("SELECT e.*,u.full_name,u.phone,u.role
        FROM order_header  e JOIN `user` u ON u.user_id = e.user_id 
        WHERE e.order_date <= CURDATE()  OR e.order_date < CURDATE() + INTERVAL 1 DAY");
                return $this->db->resultSet();
    }
    public function getOrder_count($table)
    {
                $this->db->query("SELECT * FROM {$table} WHERE order_date <= CURDATE() OR order_date < CURDATE() + INTERVAL 1 DAY");
                $this->db->resultSet();
                return $this->db->rowCount();
    }
    public function get_All_column_count($table,$column)
    {
                $this->db->query("SELECT order_status,COUNT(*) as total FROM {$table} GROUP BY order_status");
                return $this->db->resultSet();
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
