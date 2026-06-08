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
    // QUERY MAKER :
    //----------------------------------------------------------------------------

    // PREPARE QUERY
    protected function buildQuery(object $q, array $columns, array $relations, callable $applyFilters): QueryBuilderInterface
    {
        // GET COLUMNS (from $columns and $relations)
        foreach ($relations as $relation) {
            $columns = array_merge($columns, $relation->getColumns());
        }

        // PREPARE QUERY <= SELECT ... FROM ...
        $query = $this->queryBuilder
            ->select($columns)
            ->from($this->getTable());

        // ADD TO QUERY <= JOINS ... (for each $relations)
        foreach ($relations as $relation) {
            $query = $relation->applyJoin($query);
        }

        // ADD CONDITIONS TO QUERY <= WHERE, LIMIT, OFFSET,... (from $q->param)
        return $applyFilters ? $applyFilters($query, $q) : $query;
    }

    //----------------------------------------------------------------------------
    // QUERIES EXECUTE :
    //----------------------------------------------------------------------------

    // EXECUTE : FIND ONE
    protected function executeFindOne(object $q, array $columns, ?callable $applyFilters = null, ?callable $applyRelations = null): ?object
    {
        // SELECT COLUMNS = $columns + relations params (ex: $q->withRoles, etc...)
        // FROM USER
        // WHERE (ex: $q->param !=== null)
        $relations = $applyRelations ? $applyRelations($q) : [];
        $query = $this->buildQuery($q, $columns, $relations, $applyFilters);

        // IF RELATIONS (GROUPED <- rows)
        if (!empty($relations)) {

            // EXECUTION (return rows[])
            $rows = $query->executeAndFetchAll();

            // HYDRATATION (Entities['roles'] <- rows['role1'], rows['role2']) (> 1 result)
            $entities = $this->hydrateMany($rows, $relations);

            // RETURN ENTITY FROM ARRAY (Entity['roles'] <- Entities['roles']) (1 result)
            return $entities[0] ?? null;
        }

        // EXECUTION (return row)
        $row = $query->executeAndFetchOne();

        // RETURN HYDRATATION (Entity <- row)
        return $row ? $this->fromArray($row) : null;
    }

    // EXECUTE : FIND ALL
    protected function executeMany(object $q, array $columns, ?callable $applyFilters = null, ?callable $applyRelations = null): array
    {
        // SELECT COLUMNS = $columns + $relations (ex: $q->withRoles, etc...)
        // FROM TABLE
        // WHERE (ex: $q->param !=== null)
        $relations = $applyRelations ? $applyRelations($q) : [];
        $query = $this->buildQuery($q, $columns, $relations, $applyFilters);

        // ADD <= LIMIT, OFFSET, ...
        if (isset($q->limit))  $query = $query->limit($q->limit);
        if (isset($q->offset)) $query = $query->offset($q->offset);
        // EXECUTION (return rows[])

        $rows = $query->executeAndFetchAll();

        // RETURN HYDRATATION (Entities['roles'] <- rows['role1'], rows['role2']) (> 1 result)
        return $this->hydrateMany($rows, $relations);
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
