<?php
class Brand extends Model
{
    function __construct()
    {
        parent::__construct();
        $this->table ="brand";
    }
}