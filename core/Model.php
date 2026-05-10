<?php
class Model
{
    public $db;
    protected $table;
    function __construct()
    {
        $this->db = new Database;
    }
    public function getAll()
    {
        $this->db->query("SELECT * FROM {$this->table}");
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
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE {$this->table}._id =:id ");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }
    /*
    upload image
    */
    public function AddFile($filedname,$folder ="Image")
    {
        if (!isset($_FILES[$filedname]) || $_FILES[$filedname]['error'] !== 0) {
            return false;
        }
        $file = $_FILES[$filedname];
        $dir = $folder . "/";
        if (!is_dir(BASE_URL."uploads/".$dir)) {
            mkdir(BASE_URL."uploads/".$dir, 0777, true);
        }
        $filename = basename($file['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filenames = uniqid($folder).'.'.$ext;
        $path = $dir . $filenames;
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return false;
        }
        if (file_exists(BASE_URL.$path)) {
            return false;
        }
        if (move_uploaded_file($file['tmp_name'], $path)) {
            return $path;
        }
        return false;
    }
}
