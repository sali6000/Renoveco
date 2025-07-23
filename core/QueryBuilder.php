<?php

namespace App\Core;

use PDO;
use App\Core\Logger\AccessLogger;

class QueryBuilder implements QueryBuilderInterface
{
    private $pdo;
    private $query;
    private $params = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function select($table)
    {
        $this->query = "SELECT * FROM `$table`";
        return $this;
    }

    public function columns(array $columns)
    {
        $cols = implode(', ', $columns);
        $this->query = str_replace('*', $cols, $this->query);
        return $this;
    }

    public function where($condition, array $params = [])
    {
        // Validation des paramètres
        foreach ($params as $key => $value) {
            if (!is_string($key) || empty($key)) {
                throw new \InvalidArgumentException("Invalid parameter key: $key");
            }
        }

        // Ajouter la condition échappée à la requête
        $escapedCondition = $this->escapeCondition($condition);
        $this->query .= ' WHERE ' . $escapedCondition;

        // Fusionner les paramètres passés avec ceux déjà présents
        $this->params = array_merge($this->params, $params);

        // Logging pour vérification
        AccessLogger::log("Condition: " . $condition);
        AccessLogger::log("Escaped Condition: " . $escapedCondition);
        AccessLogger::log("Params: " . print_r($this->params, true), 3, BASE_PATH_STORAGE_LOGS);

        return $this;
    }

    public function innerJoin($table, $condition)
    {
        $this->query .= " INNER JOIN `$table` ON " . $this->escapeCondition($condition);
        return $this;
    }

    public function leftJoin($table, $condition)
    {
        $this->query .= " LEFT JOIN `$table` ON " . $this->escapeCondition($condition);
        return $this;
    }

    public function insert($table)
    {
        $this->query = "INSERT INTO `$table`";
        return $this;
    }

    public function values(array $values)
    {
        $columns = implode(', ', array_map([$this, 'escapeIdentifier'], array_keys($values)));
        $placeholders = ':' . implode(', :', array_keys($values));
        $this->query .= " ($columns) VALUES ($placeholders)";
        $this->params = $values;
        return $this;
    }

    public function update($table)
    {
        $this->query = "UPDATE `$table` SET ";
        return $this;
    }

    public function set(array $values)
    {
        $set = [];
        foreach ($values as $column => $value) {
            $set[] = $this->escapeIdentifier($column) . " = :$column";
        }
        $this->query .= implode(', ', $set);
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function delete($table)
    {
        $this->query = "DELETE FROM `$table`";
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function execute($query = null, $params = [])
    {
        // Si aucune requête n'est passée, utiliser celle stockée dans l'objet
        if ($query === null) {
            $query = $this->query;
        }
        // Si aucun paramètre n'est passé, utiliser ceux stockés dans l'objet
        if (empty($params)) {
            $params = $this->params;
        }

        try {
            // Préparer et exécuter la requête avec les paramètres
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            return $stmt;
        } catch (\PDOException $e) {
            // Logging des erreurs SQL
            AccessLogger::log("SQL Error: " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e; // Re-throwing the exception to handle it further up if needed
        } finally {
            // Réinitialiser les paramètres et la requête stockée dans l'objet
            $this->params = [];
            $this->query = null;
        }
        $this->query = null;
    }

    private function escapeCondition($condition)
    {
        // N'échappez que les identifiants, ignorez les paramètres (commençant par :)
        return preg_replace_callback('/(?<!:)(\b\w+\b)/', function ($matches) {
            return $this->escapeIdentifier($matches[1]);
        }, $condition);
    }

    private function escapeIdentifier($identifier)
    {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }
}
