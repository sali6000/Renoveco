<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

class ManyToOneRelation extends AbstractRelation
{
    public function fetchRelated(\PDO $pdo, array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $relatedTableName = HelperSchemaMysql::fieldTable($this->relatedTable);

        $columns = $this->buildSelectColumns($this->relationColumns);
        $columnsSQL = implode(', ', $columns);

        $foreignKeyClean = HelperSchemaMysql::fieldProperty($this->foreignKey);

        $sql = "SELECT {$columnsSQL}, {$foreignKeyClean} AS fk
            FROM {$relatedTableName}
            WHERE {$foreignKeyClean} IN ({$placeholders})";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function hydrate(array $mainRows, array $relatedRows): array
    {
        return $this->flatRelatedRows($mainRows, $relatedRows);
    }

    public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface
    {
        return $query->joinLeft($this->relatedTable, $this->foreignKey, $this->localKey);
    }


    private function buildSelectColumns(array $columns): array
    {
        return array_map(
            fn(string $col) => HelperSchemaMysql::fieldProperty($col) . ' AS ' . HelperSchemaMysql::fieldProperty($col),
            $columns
        );
    }
}
