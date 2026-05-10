<?php
class Warehouse extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table ="warehouse";
    }
}
<?php
class Supplier extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table = "supplier";
    }
}