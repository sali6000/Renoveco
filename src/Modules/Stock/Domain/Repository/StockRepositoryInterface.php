<?php

namespace Src\Modules\Stock\Domain\Repository;

interface StockRepositoryInterface
{
    public function getGlobalStockQuantityByProductId(int $id): int;
}
