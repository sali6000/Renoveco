<?php

namespace Core\Database;

use PDO;
use Core\Logger\AccessLogger;
use Core\Support\DebugHelper;

class QueryBuilder implements QueryBuilderInterface
{

    /*
        Quand tu construis ta requête, pose-toi toujours ces 3 questions :
        “Est-ce que je veux une seule ligne par entité principale ?” → Oui ⇒ sous-requête possible.
        “Est-ce que je veux toutes les données associées ?” → Oui ⇒ jointure directe.
        “Est-ce que je veux un résumé (liste, moyenne, etc.) ?” → Oui ⇒ jointure + agrégation.
    */
    private array $select = [];
    private ?string $from = null;
    private array $whereConditions = [];
    private array $whereParams = [];
    private array $joins = [];
    private array $groupBy = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;

    /*

        But : stocker l’instance PDO, afin d’exécuter des requêtes.

        Exemple attendu :

        $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
        $qb = new QueryBuilder($pdo);

    */
    public function __construct(private PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // products p -> ["p.id", "pi.name"]
    public function selectFrom(string $tableName, array $columns = ['*']): static
    {
        $this->from = $tableName; // "products p"
        $this->select = $columns; // ["p.id", "p.name"]
        return $this;
    }

    // product_images pi -> ["pi.id", "pi.file_path"] 
    public function selectJoinLeft(
        string $tableTarget,        // Ex: "product_images pi"
        array $columns = []        // Ex: ["pi.id", "pi.file_path"]
    ): static {
        // ✅ Fusionner les colonnes
        if (!empty($columns)) {
            $columnsArray = is_array($columns) ? $columns : [$columns];
            $this->select = array_merge($this->select, $columnsArray);
        }

        // ✅ Extraire alias du FROM "products p"
        $fromParts = explode(' ', $this->from); // ["products", "p"]
        $aliasFrom = trim(end($fromParts)); // "p"

        // ✅ Extraire alias de la target "products_images pi"
        $targetParts = explode(' ', $tableTarget); // ["product_images", "pi"]
        $aliasTarget = trim(end($targetParts)); // "pi"

        $prefixParts = explode('_', $targetParts[0]);    // ["product"]
        $foreignKey ??= "{$prefixParts[0]}_id"; // "product_id"

        // ✅ Construire condition ON
        $condition = "{$aliasTarget}.{$foreignKey} = {$aliasFrom}.id"; // pi.product_id = p.id
        $this->leftJoin("{$tableTarget}", $condition); // LEFT JOIN product_images pi ON

        return $this;
    }

    // (table pivot) product_category pc -> categories c -> ["c.id"]["c.name"]
    public function selectJoinManyToMany(
        string $tablePivot, // Ex: "product_category pc"
        string $tableTarget, // Ex: "categories c"
        array $columns = [], // Ex: ["c.id"]["c.name"]
        ?string $joinColumn = null, // (optionnel) Ex: "category_id"
        ?string $inverseJoinColumn = null // (optionnel) Ex: "product_id"
    ): static {

        // Fusion des colonnes (select) du selectFromManyToMany avec les colonnes du selectFrom
        if (!empty($columns)) {
            $columnsArray = is_array($columns) ? $columns : [$columns];
            $this->select = array_merge($this->select, $columnsArray);
        }

        // Extraire alias
        $parts = explode(' ', $this->from);
        $aliasFrom = trim(end($parts)); // Ex: "p"
        $parts = explode(' ', $tablePivot);
        $aliasPivot = trim(end($parts)); // Ex: "pca"
        $parts = explode(' ', $tableTarget);
        $aliasTarget = trim(end($parts)); // Ex: "pc"


        // Auto-détection si pas fournis 
        if (!$joinColumn || !$inverseJoinColumn) {
            // Etape 0: $pivotTable = "product_category pc"
            // Etape 1: explode(' ',$pivotTable); 
            // Etape 2: ["product_category", "pc"][0]
            // Etape 4: explode('_', ["product_category"]); 
            // Résultat: [[0] => "product", [1] => "category"]]
            $parts = explode('_', explode(' ', $tablePivot)[0]); // [[0] => "product", [1] => "category"]]
            $inverseJoinColumn = "{$parts[0]}_id" ?? null; // Ex: product_id
            $joinColumn = "{$parts[1]}_id" ?? null; // Ex: category_id
        }

        $condition = "{$aliasPivot}.{$joinColumn} = {$aliasFrom}.id"; // ru.user_id = u.id
        $this->leftJoin("{$tablePivot}", $condition); // LEFT JOIN role_user ru ON

        $condition = "{$aliasPivot}.{$inverseJoinColumn} = {$aliasTarget}.id"; // ru.role_id = r.id
        $this->leftJoin("{$tableTarget}", $condition); // LEFT JOIN roles r ON

        return $this;
    }

    public function buildQuery(): string
    {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM {$this->from}";

        // 🔹 JOIN
        foreach ($this->joins as $join) {
            $type = strtoupper($join['type']); // [ "INNER", "LEFT", ...]
            $sql .= " {$type} JOIN " . $this->escapeTableWithAlias($join['table']) .
                " ON " . $this->escapeCondition($join['condition']);
        }

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

    /**
     * A -> V <- B
     */
    public function join(string $table, string $condition, string $type = 'INNER'): static
    {
        $this->joins[] = [
            'table' => $table,
            'condition' => $condition,
            'type' => strtoupper($type),
        ];
        return $this;
    }

    /**
     * -> A & V <- B
     */
    public function leftJoin(string $table, string $condition): static
    {
        return $this->join($table, $condition, 'LEFT');
    }

    public function toSql(bool $wrapParentheses = false): string
    {
        $sql = $this->buildQuery();

        // ✅ On ne fait PAS de reset ici (sinon la requête est perdue)
        if ($wrapParentheses) {
            $sql = "($sql)";
        }

        return $sql;
    }

    public function toSubSql(?string $alias = null): string
    {
        $sql = $this->toSql(true); // true → ajoute déjà les parenthèses
        if ($alias) {
            $sql .= " AS $alias";
        }
        return $sql;
    }

    /*
        But : préparer, exécuter et retourner le résultat.
        Relation : utilise $this->whereParams pour sécuriser l’injection SQL.

        Exemple attendu :
        $stmt = $qb->execute(); => $stmt->fetchAll(PDO::FETCH_ASSOC);

    */
    public function execute(): \PDOStatement
    {
        $sql = $this->buildQuery();

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->whereParams);

            // Dev uniquement : log de toutes les requêtes réussies
            if (($_ENV['APP_DEBUG'] ?? false) === true) {
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

    /*
        escapeColumns() : sécurisent les noms de colonnes (protège contre SQL injection).
        (applique escapeColumn() sur chaque élément du tableau $columns)
    */
    private function escapeColumns(array $columns): string
    {
        /**
         * Exemple:
         * $columns = ['id', 'name', 'COUNT(order_id) AS order_count'];
         * $escaped = array_map([$this, 'escapeColumn'], $columns);
         * $escaped = ['`id`', '`name`', 'COUNT(order_id) AS order_count'] <= Résultat
         * return => "`id`, `name`, COUNT(order_id) AS order_count"
         */
        $escaped = array_map([$this, 'escapeColumn'], $columns);
        return implode(', ', $escaped);
    }

    /*
        escapeColumn() : sécurisent les noms de colonnes (protège contre SQL injection).
    */
    private function escapeColumn(mixed $column): string
    {
        // Si c'est du RAW, ne rien toucher
        if (is_array($column) && isset($column['raw'])) {
            return $column['raw'];
        }

        // Si s'est une sous-requête SQL, ne rien faire
        if (preg_match('/^\(SELECT.+\)\s+AS\s+(\w+)$/is', $column, $matches)) {
            return $column;
        }

        // Détecte si la colonne est une fonction SQL comme COUNT(id), SUM(price), GROUP_CONCAT(...)
        if (preg_match('/^(COUNT|SUM|AVG|MIN|MAX|GROUP_CONCAT|LOWER|UPPER|IFNULL|COALESCE|IF)\s*\((.+)\)(?:\s+AS\s+(\w+))?$/i', $column, $matches)) {
            $func = strtoupper($matches[1]); // COUNT
            $inner = $matches[2]; // (id)
            $alias = $matches[3] ?? null; // order_count

            // On n’échappe **que les identifiants simples**, on laisse les CONCAT, etc.
            $innerEscaped = $inner;
            $result = "$func($innerEscaped)"; // $result = COUNT (id)
            if ($alias) {
                $result .= " AS $alias";
            }
            return $result;
        }

        if (preg_match('/^(.+)\s+AS\s+(\w+)$/i', $column, $matches)) {
            $colPart = $matches[1];
            $alias = $matches[2];
            return $this->escapeIdentifierWithDots($colPart) . " AS $alias";
        }

        return $this->escapeIdentifierWithDots($column);
    }

    /*
        escapeTableWithAlias() : protège les noms de tables + alias.
    */
    private function escapeTableWithAlias(string $tableWithAlias): string
    {
        if (preg_match('/^(\w+)(?:\s+(\w+))?$/', $tableWithAlias, $matches)) {
            $table = $matches[1];
            $alias = $matches[2] ?? null;

            $sql = '`' . str_replace('`', '``', $table) . '`';
            if ($alias) {
                $sql .= ' `' . str_replace('`', '``', $alias) . '`';
            }
            return $sql;
        }
        return $this->escapeIdentifier($tableWithAlias);
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

    public function insert(string $table, array $data): bool
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
        return (int)$this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $params = []): bool
    {
        $set = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $sql = sprintf("UPDATE %s SET %s WHERE %s", $table, $set, $where);

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_merge($data, $params));
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
