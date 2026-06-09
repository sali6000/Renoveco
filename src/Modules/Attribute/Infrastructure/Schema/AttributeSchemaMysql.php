<?php

namespace Src\Modules\Attribute\Infrastructure\Schema;

final class AttributeSchemaMysql
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
    public const RELATION_PREFIX = 'attribute_';
}
