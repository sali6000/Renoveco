<?php

namespace Core\Database;

use Config\AppConfig;
use PDO;
use Core\Logger\AccessLogger;
use Core\Support\DebugHelper;

class QueryBuilder implements QueryBuilderInterface
{
    /*
        Quand tu construis ta requête, pose-toi toujours ces 3 questions :
        “Est-ce que je veux une seule ligne/colonne d'une référence ?” → Oui ⇒ sous-requête possible.
        “Est-ce que je veux toutes les données associées ?” → Oui ⇒ jointure directe.
        “Est-ce que je veux un résumé (liste, moyenne, etc.) ?” → Oui ⇒ jointure + agrégation.
    */

    /** @var string[] Liste des colonnes à sélectionner */
    private array $select = [];

    /** @var string|null Table principale */
    private ?string $from = null;

    /** @var array<string, mixed> Paramètres liés à la condition */
    private array $whereConditions = [];

    /** @var array<string, mixed> Paramètres pour la requête préparée */
    private array $whereParams = [];

    private array $joins = [];

    /** @var string[] Liste des colonnes pour GROUP BY */
    private array $groupBy = [];

    /** @var array<int, array{string, string}> Liste des colonnes pour ORDER BY avec direction ('ASC'|'DESC') */
    private array $orderBy = [];

    /** @var int|null Limite de la requête */
    private ?int $limit = null;

    /** @var int|null Offset de la requête */
    private ?int $offset = null;

    public function __construct(private PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Responses
    public function toSql(bool $wrapParentheses = false): string
    {
        $sql = $this->buildQuery();

        // ✅ On ne fait PAS de reset ici (sinon la requête est perdue)
        if ($wrapParentheses) {
            $sql = "($sql)";
        }
        return $sql;
    }

    public function execute(): \PDOStatement
    {
        $sql = $this->buildQuery();

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->whereParams);

            // Dev uniquement : log de toutes les requêtes réussies
            if ((AppConfig::getEnv('APP_DEBUG') ?? false) === true) {
                DebugHelper::logSQL($sql, $this->whereParams, true);
            }
            return $stmt;
        } catch (\PDOException $e) {
            // Toujours log l'erreur
            DebugHelper::logSQL($sql, $this->whereParams, true);
            AccessLogger::log("SQL Error: " . $e->getMessage(), AccessLogger::LEVEL_ERROR);
            throw $e;
        } finally {
            $this->reset();
        }
    }

    // Type of request response
    public function toSubSql(?string $alias = null): string
    {
        $sql = $this->toSql(true); // true → ajoute déjà les parenthèses
        if ($alias) {
            $sql .= " AS $alias";
        }
        return $sql;
    }

    /**
     * Exécute la requête et retourne le premier champ de la première ligne (id)
     */
    public function executeAndFetchColumn(int $numColumn = 0): mixed
    {
        $stmt = $this->execute();      // execute() retourne PDOStatement
        return $stmt->fetchColumn($numColumn);   // récupère directement la valeur
    }

    /**
     * Exécute la requête et retourne la première ligne complète
     */
    public function executeAndFetchOne(): ?array
    {
        $stmt = $this->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Exécute la requête et retourne toutes les lignes
     */
    public function executeAndFetchAll(): array
    {
        $stmt = $this->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
        But : réinitialiser le QueryBuilder après exécution (pour ne pas polluer la requête suivante).
    */
    private function reset(): void
    {
        $this->select = [];
        $this->joins = [];
        $this->whereConditions = [];
        $this->whereParams = [];
        $this->groupBy = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * Retourne le dernier ID inséré (après insert)
     */
    public function returnInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Met à jour des lignes dans une table SQL de manière sécurisée.
     *
     * @param string $table   Nom de la table à mettre à jour.
     * @param array  $data    Tableau associatif des colonnes et valeurs à mettre à jour.
     *                        Exemple : ['name' => 'Nouveau nom', 'price' => 19.99]
     * @param string $where   Condition SQL pour limiter les lignes affectées.
     *                        Exemple : 'id = :id'
     * @param array  $params  Paramètres supplémentaires pour la clause WHERE si nécessaire.
     *                        Exemple : ['id' => 10]
     *
     * @return bool           true si la mise à jour a réussi, false sinon.
     */
    public function update(string $table, array $data, string $where, array $params = []): bool
    {
        // 1️⃣ Construire la partie SET dynamiquement :
        // transforme ['name'=>'Nouveau nom','price'=>19.99]
        // en "name = :name, price = :price"
        $set = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));

        // 2️⃣ Construire la requête SQL complète
        // Exemple final : "UPDATE products SET name = :name, price = :price WHERE id = :id"
        $sql = sprintf("UPDATE %s SET %s WHERE %s", $table, $set, $where);

        // 3️⃣ Préparer la requête pour sécuriser les paramètres (PDO)
        $stmt = $this->pdo->prepare($sql);

        // 4️⃣ Exécuter la requête avec tous les paramètres
        // array_merge($data, $params) combine :
        // - les colonnes à mettre à jour
        // - les paramètres du WHERE
        return $stmt->execute(array_merge($data, $params));
    }

    /**********************************************************************************************
     * 
     * => ÉXECUTION (Fin): Tous ce qui concerne l'execution (PDO)     
     *  
     **********************************************************************************************/
    /**********************************************************************************************
     * 
     * => PRÉPARATION, FORMAT ET INTERPREATION (Début): Tous ce qui concerne la préparation de la query SQL
     * 
     **********************************************************************************************/
    public function buildQuery(): string
    {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM {$this->from}";

        //JOIN 
        $sql .= implode('', $this->joins);

        // 🔹 WHERE
        if (!empty($this->whereConditions)) {
            $sql .= ' WHERE ' . implode(' AND ', array_map([$this, 'escapeCondition'], $this->whereConditions));
        }

        // 🔹 GROUP BY
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        // 🔹 ORDER BY
        if (!empty($this->orderBy)) {
            $orders = array_map(function ($o) {
                [$col, $dir] = $o;
                return $this->escapeIdentifierWithDots($col) . " $dir";
            }, $this->orderBy);
            $sql .= ' ORDER BY ' . implode(', ', $orders);
        }

        // 🔹 LIMIT
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . intval($this->limit);
        }

        // 🔹 OFFSET
        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . intval($this->offset);
        }

        return $sql;
    }

    public function select(array $columns = ['*']): static
    {
        $this->select = $columns; // ["p.id", "p.name"]
        return $this;
    }

    public function from(string $table): static
    {
        $this->from = $table; // "products p"
        return $this;
    }

    public function joinLeft(string $toTable, string $toTableFK, string $fromTablePK): static
    {
        $this->joins[] = " LEFT JOIN " . $toTable . " ON " . $toTableFK . " = " . $fromTablePK;
        return $this;
    }

    public function joinManyToMany(string $toTablePivot, string $fromTablePK, string $toTablePivotFK, string $toTable, string $fromTablePivotFK, string $toTablePK): static
    {
        $this->joins[] =
            " JOIN " . $toTablePivot . " ON " . $toTablePivotFK . " = " . $fromTablePK .
            " JOIN " . $toTable . " ON " . $toTablePK . " = " . $fromTablePivotFK;
        return $this;
    }

    public function insert(string $table, array $data): int
    {
        // ✅ 1. Nettoyer les alias dans les clés (ex: "c.name" → "name")
        $cleanData = [];
        foreach ($data as $key => $value) {
            $cleanKey = preg_replace('/^[a-zA-Z0-9_]+\./', '', $key);
            $cleanData[$cleanKey] = $value;
        }

        // ✅ 2. Nettoyer un éventuel alias de table (ex: "categories c" → "categories")
        $table = preg_replace('/\s+[a-zA-Z0-9_]+$/', '', $table);

        // ✅ 3. Construire la requête
        $columns = array_keys($cleanData);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        // ✅ 4. Exécuter
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($cleanData);

        // ✅ 5. Retourner le dernier ID inséré
        return $this->returnInsertId();
    }

    public function groupBy(string $column): static
    {
        $this->groupBy[] = $column;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function where(string $condition, array $params = []): static
    {
        $this->whereConditions[] = $condition;
        $this->whereParams = array_merge($this->whereParams, $params);
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    /*
        escapeIdentifierWithDots() : protège table.column
    */
    private function escapeIdentifierWithDots(string $identifier): string
    {
        if (substr($identifier, -2) === '.*') {
            $alias = substr($identifier, 0, -2);
            return $this->escapeIdentifier($alias) . '.*';
        }

        if (strpos($identifier, '.') !== false) {
            $parts = explode('.', $identifier);
            foreach ($parts as &$part) {
                $part = $this->escapeIdentifier($part);
            }
            return implode('.', $parts);
        }
        return $this->escapeIdentifier($identifier);
    }

    /*
        escapeIdentifier() : entoure les identifiants avec backticks (`).
    */
    private function escapeIdentifier(string $identifier): string
    {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }

    /*
        escapeCondition() : protège les conditions SQL sauf paramètres.
    */
    private function escapeCondition(string $condition): string
    {
        $sqlKeywords = ['AND', 'OR', 'NOT', 'IN', 'IS', 'NULL', 'TRUE', 'FALSE', 'LIKE', 'BETWEEN', 'ON', 'AS'];

        return preg_replace_callback('/(?<!:)(\b\w+\b)/', function ($matches) use ($sqlKeywords) {
            $word = strtoupper($matches[1]);
            if (in_array($word, $sqlKeywords)) {
                return $word;
            }
            return $this->escapeIdentifier($matches[1]);
        }, $condition);
    }

    /**********************************************************************************************
     * 
     * => PRÉPARATION, FORMAT ET INTERPREATION (Fin): Tous ce qui concerne la préparation de la query SQL
     * 
     **********************************************************************************************/
}
