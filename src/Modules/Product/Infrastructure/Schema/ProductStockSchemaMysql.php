<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Core\Database\Relations\ManyToOneRelation;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class ProductStockSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 STOCK : PRODUCT
    // -------------------------------------------------------
    public const TABLE = 'stock_product stopro';
    public const ID = 'stopro.id';
    public const PRODUCT_ID = 'stopro.product_id';
    public const QUANTITY = 'stopro.quantity';
    public const STOCK_MINIMUM = 'stopro.stock_minimum';
    public const STOCK_MAXIMUM = 'stopro.stock_maximum';

    public static function withStock(array $columns): ManyToOneRelation
    {
        return new ManyToOneRelation(
            key: self::fieldTable(self::TABLE),
            relationColumns: $columns,

            relatedTable: self::TABLE,
            foreignKey: self::PRODUCT_ID,
            localKey: ProductSchemaMysql::ID
        );
    }
}
