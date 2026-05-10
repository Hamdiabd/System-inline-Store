<?php
class Supplier extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table = "supplier";
    }
}