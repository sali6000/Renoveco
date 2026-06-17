<?php

declare(strict_types=1);

namespace Src\Modules\Product\Domain\Query;

use Src\Modules\Category\Domain\Entity\Category;

final class ProductQuery
{
    public function __construct(

        // FILTRES MÉTIER
        public readonly ?int $byId = null,
        public readonly ?string $bySlug = null,
        /** @var Category */
        public readonly ?Category $byCategory = null,
        public readonly ?bool $isActive = null,
        public readonly bool $onlyMainImage = false,

        // RELATIONS
        public readonly bool $withImages = false,
        public readonly bool $withCategories = false,
        public readonly bool $withAttributes = false,
        public readonly bool $withStock = false,

        // PAGINATION
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {}
}
