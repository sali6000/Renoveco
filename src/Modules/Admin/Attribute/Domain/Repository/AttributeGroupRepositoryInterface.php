<?php

declare(strict_types=1);

namespace Src\Modules\Admin\Attribute\Domain\Repository;

use Src\Modules\Admin\Attribute\Domain\Entity\AttributeGroup;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeGroupQuery;

interface AttributeGroupRepositoryInterface
{
    /** @return AttributeGroup */
    public function findAttributeGroup(AttributeGroupQuery $query): ?AttributeGroup;

    /** @return AttributeGroup[] */
    public function findAttributeGroups(AttributeGroupQuery $query): array;
}
