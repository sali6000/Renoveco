<?php

namespace Src\Modules\Stock\Domain\Infrastructure\Schema;

final class StockLocationSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 STOCK : LOCATION
    // -------------------------------------------------------
    public const TABLE = 'stock_location stoloc';
    public const ID = 'stoloc.id';
    public const NAME = 'stoloc.name';
    public const DESCRIPTION = 'stoloc.description';
}
