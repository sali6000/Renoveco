<?php

namespace Src\Modules\Admin\Attribute\Infrastructure\Persistence\Schema;

use Core\Database\Relations\ManyToOneRelation;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class AttributeSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 ATTRIBUTE
    // -------------------------------------------------------    
    public const TABLE = 'attribute att';
    public const ID = 'att.id';
    public const NAME = 'att.name';
    public const TYPE = 'att.type';
    public const UNIT = 'att.unit';
    public const IS_REQUIRED = 'att.is_required';
    public const PARENT_ID = 'att.parent_attribute_id';
    public const GROUP_ID = 'att.attribute_group_id';

    public static function withGroup(array $columns): ManyToOneRelation
    {
        return new ManyToOneRelation(
            key: self::fieldTable(AttributeGroupSchemaMysql::TABLE),
            idKey: HelperSchemaMysql::fieldProperty(self::GROUP_ID),
            relationColumns: $columns,

            relatedTable: AttributeGroupSchemaMysql::TABLE,
            localKey: self::GROUP_ID,
            foreignKey: AttributeGroupSchemaMysql::ID,
        );
    }
}
