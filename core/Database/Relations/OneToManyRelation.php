<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;

class OneToManyRelation extends AbstractRelation
{
    public function hydrate(array $rows): array
    {
        return $this->groupRows($rows);
    }

    public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface
    {
        return $query->joinLeft($this->relatedTable, $this->foreignKey, $this->localKey);
    }
}
