<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Core\Database\Relations\ManyToManyRelation;
use Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema\AttributeSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class ProductAttributeSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 PRODUCT : ATTRIBUTE
    // -------------------------------------------------------
    public const TABLE = 'product_attribute proatt';
    public const ID = 'proatt.id';
    public const PRODUCT_ID = 'proatt.product_id';
    public const ATTRIBUTE_ID = 'proatt.attribute_id';
    public const VALUE = 'proatt.value';
    public const CREATED_AT = 'proatt.created_at';
    public const UPDATED_AT = 'proatt.updated_at';

    public static function withAttributes(array $pivotColumns, array $attributeColumns = []): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // TARGET
            key: self::fieldTable(AttributeSchemaMysql::TABLE), // SET KEY ARRAY (ex: User['roles'][...])
            relationColumns: $attributeColumns, // SET COLUMNS TO RETURN

            // FROM 
            relatedTable: AttributeSchemaMysql::TABLE,
            foreignKey: AttributeSchemaMysql::ID,
            localKey: ProductSchemaMysql::ID,

            // PIVOT 
            pivotTable: self::TABLE,
            pivotForeignKey: self::ATTRIBUTE_ID,
            pivotLocalKey: self::PRODUCT_ID,
            pivotColumns: $pivotColumns,
        );
    }
}
