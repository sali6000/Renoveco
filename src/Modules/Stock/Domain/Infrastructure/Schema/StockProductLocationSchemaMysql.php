<?php

namespace Src\Modules\Stock\Domain\Infrastructure\Schema;

final class StockProductLocationSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 STOCK : PRODUCT : LOCATION
    // -------------------------------------------------------
    public const TABLE = 'stock_product_location stoproloc';
    public const ID = 'stoproloc.id';
    public const PRODUCT_STOCK_ID = 'stoproloc.product_stock_id';
    public const STOCK_LOCATION_ID = 'stoproloc.stock_location_id';
    public const QUANTITY = 'stoproloc.quantity';
    public const CREATED_AT = 'stoproloc.created_at';
    public const UPDATED_AT = 'stoproloc.updated_at';
}
