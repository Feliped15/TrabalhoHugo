<?php

namespace Model;

use CoffeeCode\DataLayer\DataLayer;

class Jogos extends DataLayer
{
    /**
     * Jogos constructor.
     */
    public function __construct()
    {
        //string "TABLE_NAME", array ["REQUIRED_FIELD_1", "REQUIRED_FIELD_2"], string "PRIMARY_KEY", bool "TIMESTAMPS"
        parent::__construct("jogos", ["nome", "tipo", "nota", "review"], "id", false);
    }
}