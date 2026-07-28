<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Domain\Query;

use Src\Modules\Admin\Attribute\Domain\Entity\AttributeGroup;

final class AttributeDomainQuery
{
    public function __construct(

        // FILTRES MÉTIER
        public readonly ?int $byId = null,
        public readonly ?string $byName = null,

        /** @var AttributeGroup */
        public readonly ?AttributeGroup $byAttributeGroup = null,

        // RELATIONS
        public readonly bool $withAttributes = false,

        // PAGINATION
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {}
}
