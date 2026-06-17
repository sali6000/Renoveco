<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Core\Database\Relations\ManyToManyRelation;
use Src\Modules\Domain\Infrastructure\Schema\DomainSchemaMysql;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

// PIVOT
final class ProductDomainSchemaMysql extends HelperSchemaMysql
{
    public const TABLE = 'product_domain prodom';
    public const ID = 'prodom.id';
    public const PRODUCT_ID = 'prodom.product_id';
    public const DOMAIN_ID = 'prodom.domain_id';

    // RELATION PIVOT
    public static function withDomains(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(

            // TARGET SCHEMA
            relationColumns: $columns,  // TARGET : COLUMNS (ex: [self::NAME, self::DESCRIPTION, ... ])
            relatedTable: DomainSchemaMysql::TABLE, // TARGET : TABLE
            key: self::fieldTable(DomainSchemaMysql::TABLE), // TABLE : KEY ARRAY (ex: User['roles'][...])
            foreignKey: DomainSchemaMysql::ID, // TARGET : ID

            // SOURCE SCHEMA
            localKey: ProductSchemaMysql::ID, // SOURCE : ID 

            // PIVOT SCHEMA
            pivotLocalKey: self::PRODUCT_ID, // PIVOT : ID SRC
            pivotTable: self::TABLE, // PIVOT : TABLE
            pivotForeignKey: self::DOMAIN_ID, // PIVOT : ID TARGET
        );
    }
}
