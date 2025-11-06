<?php

namespace App\Modules\About;

use App\Modules\About\HistoryRepository;

class HistoryService
{
    public function __construct(private HistoryRepository $historyRepo) {}

    public function getAllHistory()
    {
        return $this->historyRepo->getAllHistory();
    }
}
