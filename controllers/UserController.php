<?php
class UserController extends Controller
{
    protected $table;
    function __construct()
    {
        $this->table =$this->model("User");
    }
    public function index()
    {
        $res = $this->table->getAllUser("User");
        $datausers = [
            'Name'=> 'Name all',
            'user' => $res
        ];
        $this->view('user/index',$datausers);
    }
}
?>