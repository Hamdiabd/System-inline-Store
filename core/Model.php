<?php
class Model
{
    protected $db;
    protected $table;
    function __construct()
    {
        $this->db = new Database;
    }
    public function getAll($table)
    {
        $this->db->query("SELECT * FROM {$table}");
        return $this->db->resultSet();
    }
    public function insert($data)
    {
        $column = implode(',', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $this->db->query("INSERT INTO {$this->table}($column)VALUES($values)");

        foreach ($data as $key => $value) {
            $this->db->bind(":$key", $value);
        }
        return $this->db->execute();
    }
    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE {$this->table}._id =:id ");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }
    public function AddFile($folder)
    {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
            return false;
        }
        $file = $_FILES['file'];
        $dir = "uploads/" . $folder . "/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = basename($file['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $path = $dir . $filename;
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return false;
        }
        if (file_exists($path)) {
            return false;
        }
        if (move_uploaded_file($file['tmp_name'], $path)) {
            return $path;
        }
        return false;
    }
}
