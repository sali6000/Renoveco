<?php

namespace App\Services;

use App\DAO\HistoryDAO;

class HistoryService
{
    private $historyDAO;

    public function __construct(HistoryDAO $historyDAO)
    {
        $this->historyDAO = $historyDAO;
    }

    public function getAllHistory()
    {
        return $this->historyDAO->getAllHistory();
    }
}
