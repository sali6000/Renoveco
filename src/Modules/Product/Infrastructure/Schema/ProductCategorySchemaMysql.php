<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Core\Database\Relations\ManyToManyRelation;
use Src\Modules\Category\Infrastructure\Schema\CategorySchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class ProductCategorySchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 CATEGORY : PRODUCT
    // -------------------------------------------------------
    public const TABLE = 'category_product catpro';
    public const PRODUCT_ID = 'catpro.product_id';
    public const CATEGORY_ID = 'catpro.category_id';

    public static function withCategories(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // SET KEY ARRAY (ex: User['roles'][...])
            key: self::fieldTable(CategorySchemaMysql::TABLE),

            // SET COLUMNS TO GET (ex: self::ROLE_COLUMNS_MINIMAL)
            relationColumns: $columns,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: CategorySchemaMysql::TABLE,
            foreignKey: CategorySchemaMysql::ID,
            localKey: ProductSchemaMysql::ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: self::TABLE,
            pivotForeignKey: self::CATEGORY_ID,
            pivotLocalKey: self::PRODUCT_ID,
        );
    }
}
