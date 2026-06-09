<?php

namespace Src\Modules\Category\Infrastructure\Schema;

final class CategorySchemaMysql
{
    // -------------------------------------------------------
    // 🧩 CATÉGORIES
    // -------------------------------------------------------
    public const TABLE = 'category cat';
    public const ID = 'cat.id';
    public const SLUG = 'cat.slug';
    public const NAME = 'cat.name';
    public const DESCRIPTION = 'cat.description';
    public const PARENT_ID = 'cat.parent_id';
    public const RELATION_PREFIX = "cat_";
}
