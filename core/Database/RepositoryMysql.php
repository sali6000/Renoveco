<?php

namespace Core\Database;

abstract class RepositoryMysql
{
    abstract protected function getTable(): string;
    abstract protected function fromArray(array $row): object;

    public function __construct(
        protected \PDO $pdo,
        private QueryBuilderInterface $queryBuilder
    ) {}

    //----------------------------------------------------------------------------
    // QUERIES EXECUTE :
    //----------------------------------------------------------------------------

    // EXECUTE : FIND ONE
    protected function executeFindOne(object $objectQuery, array $columns, ?callable $applyFilters = null, ?callable $applyRelations = null): ?object
    {
        // 1. Récupération de l'objet en base (sans relations)
        $row = $this->buildQuery($objectQuery, $columns, $applyFilters)->executeAndFetchOne();
        if (!$row) return null;

        // 2. Récupération des relations en base liés à l'objet
        $relations = $applyRelations ? $applyRelations($objectQuery) : [];
        foreach ($relations as $relation) {
            $relatedRows = $relation->fetchRelated($this->pdo, [$row['id']]);
            [$row] = $relation->hydrate([$row], $relatedRows);
        }

        return $this->fromArray($row);
    }

    protected function executeFindAll(object $objectQuery, array $columns, ?callable $applyFilters = null, ?callable $applyRelations = null): array
    {
        // 1. Récupération des objets en base (sans relations)
        $query = $this->buildQuery($objectQuery, $columns, $applyFilters);
        if (isset($objectQuery->limit))  $query = $query->limit($objectQuery->limit);
        if (isset($objectQuery->offset)) $query = $query->offset($objectQuery->offset);

        $rows = $query->executeAndFetchAll();
        if (empty($rows)) return [];

        // 2. Récupération des relations en base liés aux objets
        $relations = $applyRelations ? $applyRelations($objectQuery) : [];

        foreach ($relations as $relation) {
            $keys = $relation->extractKeys($rows);
            $relatedRows = $relation->fetchRelated($this->pdo, $keys);
            $rows = $relation->hydrate($rows, $relatedRows);
        }
        return array_map(fn($row) => $this->fromArray($row), $rows);
    }

    protected function buildQuery(object $q, array $columns, ?callable $applyFilters): QueryBuilderInterface
    {
        $query = $this->queryBuilder
            ->select($columns)
            ->from($this->getTable());

        return $applyFilters ? $applyFilters($query, $q) : $query;
    }

    //----------------------------------------------------------------------------
    // HYDRATATIONS :
    //----------------------------------------------------------------------------


    /**
     * Transforme plusieurs lignes SQL en users uniques.
     *
     * Exemple :
     *
     * Entrée — 1 user avec 2 rôles = 2 lignes SQL :
     * 
     * <code>
     * ['id' => 1, 'email' => 'a@b.com', 'role_id' => 1, 'role_name' => 'admin']
     * 
     * ['id' => 1, 'email' => 'a@b.com', 'role_id' => 2, 'role_name' => 'editor']
     * </code>
     *
     * Sortie — 1 user avec ses rôles groupés :
     * 
     * <code>
     * ['id' => 1, 'email' => 'a@b.com', 'roles' => [['id' => 1, ...], ['id' => 2, ...]]]
     * </code>
     *
     * @return object[]
     */
    protected function hydrateMany(array $rows, array $relations): array
    {
        // RETURN (without relations)
        if (empty($relations)) {
            return array_map(fn(array $row) => $this->fromArray($row), $rows);
        }

        // HYDRATE (with relations)
        foreach ($relations as $relation) {
            $rows = $relation->hydrate($rows);
        }

        return array_map(fn(array $row) => $this->fromArray($row), $rows);
    }

    //----------------------------------------------------------------------------
    // OTHERS QUERIES EXECUTE :
    //----------------------------------------------------------------------------


    /**
     * Supprime une ligne d'une table en base de données selon un identifiant.
     *
     * Cette méthode prépare et exécute une requête DELETE en s'assurant
     * que le nom de la table et de la colonne sont valides afin d'éviter
     * toute injection SQL.
     *
     * @param string $table Nom de la table sur laquelle exécuter la suppression.
     * @param string $where Nom de la colonne utilisée dans la clause WHERE (ex: 'id' ou 'category_id').
     * @param int    $id    Valeur de l'identifiant correspondant à la ligne à supprimer.
     *
     * @return void
     *
     * @throws \InvalidArgumentException Si le nom de la table ou de la colonne est invalide.
     * @throws \PDOException Si la requête échoue.
     */
    public function delete(string $table, string $where, int $id): void
    {
        // Vérification stricte pour éviter toute injection via les noms
        // Vérification des noms de table et colonne (acceptant Ex: alias "categories c" et points "c.id")
        if (!preg_match('/^[a-zA-Z0-9_ ]+$/', $table)) {
            throw new \InvalidArgumentException("Nom de table invalide : {$table}");
        }

        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $where)) {
            throw new \InvalidArgumentException("Nom de colonne invalide : {$where}");
        }

        $sql = "DELETE FROM {$table} WHERE {$where} = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
