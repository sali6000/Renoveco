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
        private string $pivotTable = '',
        private string $pivotLocalKey = '',
        private string $pivotForeignKey = '',
        private array  $pivotColumns = [],
        string $relatedTable = '',
        string $localKey = '',
        string $foreignKey = '',
    ) {
        parent::__construct($key, $idKey, $relationColumns, $relatedTable, $localKey, $foreignKey);
    }

    public function fetchRelated(\PDO $pdo, array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $columnsSQL = implode(', ', array_merge(
            array_map(fn(string $col) => $col, $this->relationColumns),
            array_map(fn(string $col) => $col, $this->pivotColumns)
        ));

        $pivotTableName   = $this->pivotTable;
        $relatedTableName = $this->relatedTable;
        $pivotLocalKeyClean = $this->pivotLocalKey;
        $foreignKeyClean    = $this->foreignKey;
        $pivotForeignKeyClean = $this->pivotForeignKey;

        $sql = "SELECT {$columnsSQL}, {$pivotLocalKeyClean} AS fk
        FROM {$relatedTableName}
        JOIN {$pivotTableName} ON {$pivotForeignKeyClean} = {$foreignKeyClean}
        WHERE {$pivotLocalKeyClean} IN ({$placeholders})";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function hydrate(array $mainRows, array $relatedRows): array
    {
        return $this->groupRelatedRows($mainRows, $relatedRows);
    }

    // applyJoin conservé pour compatibilité si besoin
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
}
