class ApiController extends Controller
{
	private $productModel;
	public function __construct()
	{
		$this->productModel= $this->model("Product");
		header('Content-Type: application/ json; charset=utf-8');
  }
  public function products()
	{
		$page = (int)($this->get('page',1))
  }
  public function products-create()
	{
		$page = (int)($this->get('page',1))
  }

	}