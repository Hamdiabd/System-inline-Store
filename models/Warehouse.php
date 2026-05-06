<?php
class Warehouse extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table ="warehouse";
    }
}