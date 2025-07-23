<?php

namespace App\DAO;

use App\Core\BaseDAO;

class HistoryDAO extends BaseDAO
{
    private $src;

    public function __construct(String $src)
    {
        $this->src = $src;
    }

    public function getAllHistory()
    {
        $json = file_get_contents($this->src);
        return json_decode($json, true);
    }
}
