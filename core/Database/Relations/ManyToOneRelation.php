<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;

class ManyToOneRelation extends AbstractRelation
{
    public function hydrate(array $rows): array
    {
        return $this->flatRows($rows);
    }

    public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface
    {
        return $query->joinLeft($this->relatedTable, $this->foreignKey, $this->localKey);
    }
}
