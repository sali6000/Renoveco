<?php

namespace Src\Modules\Product\Infrastructure\Schema;

use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class ProductSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 PRODUCT
    // -------------------------------------------------------
    public const TABLE = 'product pro';
    public const ID = 'pro.id';
    public const REFERENCE = 'pro.reference';
    public const SLUG = 'pro.slug';
    public const NAME = 'pro.name';
    public const DESCRIPTION = 'pro.description';
    public const COMPOSITION = 'pro.composition';
    public const USE_FOR = 'pro.use_for';
    public const IS_ACTIVE = 'pro.is_active';
    public const DEFAULT_SUPPLIER_ID = 'pro.default_supplier_id';
    public const CREATED_AT = 'pro.created_at';
    public const UPDATED_AT = 'pro.updated_at';
    public const SUBTITLE = 'pro.subtitle';
    public const META_DESCRIPTION = 'pro.meta_description';
    public const FEATURES = 'pro.features';
}
