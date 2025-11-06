<?php

namespace App\Modules\About;

use Core\Database\Repository;

class HistoryRepository extends Repository
{
    public function __construct(private String $src) {}

    public function getAllHistory()
    {
        $json = file_get_contents($this->src);
        return json_decode($json, true);
    }
}
