<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;

class ManyToManyRelation extends AbstractRelation
{
    public function __construct(
        string $key,
        string $idKey = 'id',
        array  $relationColumns = [],
        string $relationPrefix = '',

        // JOIN PARAMS spécifiques ManyToMany
        private string $pivotTable = '',
        private string $pivotLocalKey = '',
        private string $pivotForeignKey = '',

        // JOIN PARAMS communs (relatedTable, localKey, foreignKey via parent)
        string $relatedTable = '',
        string $localKey = '',
        string $foreignKey = '',
    ) {
        parent::__construct($key, $idKey, $relationColumns, $relationPrefix, $relatedTable, $localKey, $foreignKey);
    }

    public function hydrate(array $rows): array
    {
        return $this->groupRows($rows);
    }

    public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface
    {
        return $query->joinManyToMany(
            $this->pivotTable,
            $this->localKey,
            $this->pivotLocalKey,
            $this->relatedTable,
            $this->pivotForeignKey,
            $this->foreignKey,
        );
    }

    public function getColumns(): array
    {
        if (empty($this->relationColumns)) {
            return [];
        }

        // ManyToMany : pas de table préfixée dans le SELECT — juste l'alias
        // Ex: role_id AS role_id, role_name AS role_name
        return array_map(
            fn(string $col) => $this->relationPrefix . $col . ' AS ' . $this->relationPrefix . $col,
            $this->relationColumns
        );
    }
}
