<?php

namespace Src\Modules\Attribute\Infrastructure\Schema;

final class AttributeGroupSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 ATTRIBUTE : GROUP
    // -------------------------------------------------------
    public const TABLE = 'attribute_group attgro';
    public const ID = 'attgro.id';
    public const NAME = 'attgro.name';
    public const DOMAIN_ID = 'attgro.domain_id';
    public const DISPLAY_ORDER = 'attgro.display_order';
    public const RELATION_PREFIX = 'attgro_';
}
