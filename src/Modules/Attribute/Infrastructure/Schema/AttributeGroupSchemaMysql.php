<?php

namespace Src\Modules\Attribute\Infrastructure\Schema;

use Core\Database\Relations\OneToManyRelation;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class AttributeGroupSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 ATTRIBUTE : GROUP
    // -------------------------------------------------------
    public const TABLE = 'attribute_group attgro';
    public const ID = 'attgro.id';
    public const NAME = 'attgro.name';
    public const DOMAIN_ID = 'attgro.domain_id';
    public const DISPLAY_ORDER = 'attgro.display_order';

    public static function withAttributes(array $columns): OneToManyRelation
    {
        return new OneToManyRelation(
            key: self::fieldTable(AttributeSchemaMysql::TABLE),
            relationColumns: $columns,

            relatedTable: AttributeSchemaMysql::TABLE,
            foreignKey: AttributeSchemaMysql::GROUP_ID,
            localKey: self::ID,
        );
    }
}
