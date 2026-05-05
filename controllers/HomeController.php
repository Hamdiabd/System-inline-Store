<?php
class HomeController extends Controller
{
    protected $table;
    function __construct()
    {
        $this->table =$this->model("Home");
    }
    public function index()
    {

        $order_at = $this->table->getAll_at("order_header");
        $res = $this->table->get_All_column_count("order_header","order_status");
        $user = $this->table->getuserName("user","admin");
        $_SERVER["ordercount"]=$this->table->getOrder_count("order_header");
        $data = [
            'order_at'=>$order_at,
            'Name'=> 'Name all',
            'status' => $res
        ];
        $this->view('home/index',$data);
    }
    public function about()
    {

        $res = $this->table->getAll("user");
        $order = $this->table->getOrder("order_header");
        $user = $this->table->getuserName("user","admin");
        
        $data = [
            'Name'=> 'Name all',
            'orderdata' => $order['order'],
            'ordercount' => $order['count'],
            'user' => $res
        ];
        $this->view('home/about',$data);
    }
}
?>