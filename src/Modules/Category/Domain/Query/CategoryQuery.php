<?php

declare(strict_types=1);

namespace Src\Modules\Category\Domain\Query;

final class CategoryQuery
{
    public function __construct(

        // FILTRES MÉTIER
        public readonly ?int $id = null,
        public readonly ?string $slug = null,

        // RELATIONS

        // PAGINATION
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {}
}
