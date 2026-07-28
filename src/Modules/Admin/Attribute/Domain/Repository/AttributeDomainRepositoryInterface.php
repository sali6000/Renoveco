<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Domain\Repository;

use Src\Modules\Admin\Attribute\Domain\Entity\AttributeDomain;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeDomainQuery;

interface AttributeDomainRepositoryInterface
{
    /** @return AttributeDomain */
    public function findAttribute(AttributeDomainQuery $query): ?AttributeDomain;

    /** @return AttributeDomain[] */
    public function findAttributes(AttributeDomainQuery $query): array;
}
