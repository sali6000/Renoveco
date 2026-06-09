<?php

namespace Src\Modules\Stock\Domain\Infrastructure\Schema;

use Core\Database\Relations\ManyToOneRelation;
use Src\Modules\Product\Infrastructure\Schema\ProductSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class StockProductSchemaMysql extends HelperSchemaMysql
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
    public const RELATION_PREFIX = 'stopro_';

    public static function stockProductRelation(array $columns): ManyToOneRelation
    {
        return new ManyToOneRelation(
            key: self::fieldTable(self::TABLE),
            relationColumns: $columns,
            relationPrefix: self::RELATION_PREFIX,

            relatedTable: self::TABLE,
            foreignKey: self::PRODUCT_ID,
            localKey: ProductSchemaMysql::ID
        );
    }
}
