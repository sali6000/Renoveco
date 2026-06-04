<?php

namespace Src\Modules\Stock\Application\Service;

use Src\Modules\Stock\Domain\Repository\StockRepositoryInterface;

final class StockService
{

    public function __construct(private readonly StockRepositoryInterface $stockRepo) {}

    public function getStockForProductId(int $id): int
    {
        return $this->stockRepo->getGlobalStockQuantityByProductId($id);
    }
}
