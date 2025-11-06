<?php

namespace Core\Database;

class Repository
{
    public function __construct(protected \PDO $pdo) {}

    // ------------------------ DELETE ------------------------
    public function delete(string $table, string $where, int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE {$where} = :id");
        $stmt->execute(['id' => $id]);
    }
}
