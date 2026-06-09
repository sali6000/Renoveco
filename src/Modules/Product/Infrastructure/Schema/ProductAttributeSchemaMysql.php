<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Core\Database\Relations\ManyToManyRelation;
use Src\Modules\Attribute\Infrastructure\Schema\AttributeSchemaMysql;
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

    public static function productAttributesRelation(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // SET KEY ARRAY (ex: User['roles'][...])
            key: self::fieldTable(AttributeSchemaMysql::TABLE),

            // SET COLUMNS TO GET (ex: self::ROLE_COLUMNS_MINIMAL)
            relationColumns: $columns,

            // SET PREFIX FOR ROLE COLUMNS
            relationPrefix: AttributeSchemaMysql::RELATION_PREFIX,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: AttributeSchemaMysql::TABLE,
            foreignKey: AttributeSchemaMysql::ID,
            localKey: ProductSchemaMysql::ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: self::TABLE,
            pivotForeignKey: self::ATTRIBUTE_ID,
            pivotLocalKey: self::PRODUCT_ID,
        );
    }
}
