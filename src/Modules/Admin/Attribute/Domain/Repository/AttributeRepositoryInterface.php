<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Domain\Repository;

use Src\Modules\Admin\Attribute\Domain\Entity\Attribute;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeQuery;

interface AttributeRepositoryInterface
{
    /** @return Attribute */
    public function findAttribute(AttributeQuery $query): ?Attribute;

    /** @return Attribute[] */
    public function findAttributes(AttributeQuery $query): array;
}
