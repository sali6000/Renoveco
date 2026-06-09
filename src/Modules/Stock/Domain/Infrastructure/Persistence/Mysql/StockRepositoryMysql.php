<?php

namespace Src\Modules\Stock\Domain\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use PDO;
use Src\Modules\Stock\Domain\Infrastructure\Schema\StockProductSchemaMysql;
use Src\Modules\Stock\Domain\Repository\StockRepositoryInterface;

final class StockRepositoryMysql extends RepositoryMysql implements StockRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly QueryBuilderInterface $queryBuilder,
    ) {
        parent::__construct($pdo);
    }


    public function getGlobalStockQuantityByProductId(int $id): int
    {
        return (int) $this->queryBuilder
            ->select(['SUM(' . StockProductSchemaMysql::QUANTITY . ') as total'])
            ->from(StockProductSchemaMysql::TABLE)
            ->where(StockProductSchemaMysql::ID . ' = :product_id', ['product_id' => $id])
            ->executeAndFetchColumn();
    }
}
