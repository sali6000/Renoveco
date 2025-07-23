<?php

use App\Core\BaseDAO;
use App\Core\QueryBuilderInterface;

abstract class AbstractDAO extends BaseDAO
{
    protected string $primaryKey = 'id'; // Peut être overridé

    public function __construct(QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    public function getAll(string $table): array
    {
        $query = $this->queryBuilder
            ->select($table)
            ->columns(['*'])
            ->getQuery();
        $stmt = $this->queryBuilder->execute($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(string $table, int $id): ?array
    {
        $query = $this->queryBuilder
            ->select($table)
            ->columns(['*'])
            ->where("{$this->primaryKey} = :id")
            ->getQuery();
        $stmt = $this->queryBuilder->execute($query, ['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteById(string $table, int $id): bool
    {
        $query = $this->queryBuilder
            ->delete($table)
            ->where("{$this->primaryKey} = :id")
            ->getQuery();
        $this->queryBuilder->execute($query, ['id' => $id]);
        return true;
    }

    // Tu peux rajouter ici insert(), update() plus tard.
}
