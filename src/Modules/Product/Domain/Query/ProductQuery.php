<?php

declare(strict_types=1);

namespace Src\Modules\Product\Domain\Query;

final class ProductQuery
{
    public function __construct(

        // FILTRES MÉTIER
        public readonly ?int $id = null,
        public readonly ?string $slug = null,
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
