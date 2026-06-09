<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Core\Database\Relations\OneToManyRelation;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class ProductImageSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 PRODUCT : IMAGE
    // -------------------------------------------------------
    public const TABLE = 'product_image proima';
    public const ID = 'proima.id';
    public const PRODUCT_ID = 'proima.product_id';
    public const FILE_PATH = 'proima.file_path';
    public const ALT_TEXT = 'proima.alt_text';
    public const IS_MAIN = 'proima.is_main';
    public const RELATION_PREFIX = 'image_';

    public static function productImagesRelation(array $columns): OneToManyRelation
    {
        return new OneToManyRelation(
            key: self::fieldTable(self::TABLE),
            relationColumns: $columns,
            relationPrefix: self::RELATION_PREFIX,

            // JOIN PARAMS
            relatedTable: self::TABLE,
            foreignKey: self::PRODUCT_ID,
            localKey: ProductSchemaMysql::ID,
        );
    }
}
