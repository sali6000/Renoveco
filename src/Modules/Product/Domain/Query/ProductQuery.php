<?php

declare(strict_types=1);

namespace Src\Modules\Product\Domain\Query;

final class ProductQuery
{
    public function __construct(
        public readonly bool $withImages = false,
        public readonly bool $withCategories = false,
        public readonly bool $onlyMainImage = false,
        public readonly ?array $columns = null,
        public readonly ?int $id = null,
        public readonly ?string $slug = null,
        public readonly ?bool $isActive = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {}
}
