<?php

namespace Core\Database;

class SqlAggregator
{

    private string $type = '';
    private bool $distinct = false;
    private ?string $separator = null;
    private ?string $column = null;
    private ?string $alias = null; // Ex: 'categories'

    // Méthode pour activer DISTINCT
    public function distinct(bool $value = true): self
    {
        $this->distinct = $value;
        return $this;
    }

    // Méthode pour définir le séparateur (pour GROUP_CONCAT)
    public function separator(string $sep): self
    {
        $this->separator = $sep;
        return $this;
    }

    // Méthode pour indiquer la colonne
    public function column(array $col, string $split = ", ':',"): self
    {
        $this->column = implode($split, $col);
        return $this;
    }

    // Alias
    public function alias(string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }

    // Méthodes pour choisir le type d’agrégation
    public function groupConcat(): self
    {
        $this->type = 'GROUP_CONCAT';
        return $this;
    }

    public function sum(): self
    {
        $this->type = 'SUM';
        return $this;
    }

    public function count(): self
    {
        $this->type = 'COUNT';
        return $this;
    }

    // Génération SQL
    public function toSql(): string
    {
        if (!$this->column) {
            throw new \LogicException("Aucune colonne définie pour l'agrégation.");
        }
        if (!$this->type) {
            throw new \LogicException("Aucun type d'agrégation défini (ex: groupConcat(), sum(), count()).");
        }

        $sql = $this->type . '(';
        if ($this->distinct) {
            $sql .= 'DISTINCT ';
        }
        $sql .= $this->column;

        if ($this->type === 'GROUP_CONCAT' && $this->separator) {
            $sql .= " SEPARATOR '" . $this->separator . "'";
        }

        $sql .= ')';

        // Si alias défini, on l'ajoute
        if ($this->alias) {
            $sql .= " AS {$this->alias}";
        }
        // Si alias non défini, on avertit (optionnel ou obligatoire selon ton choix)
        else {
            trigger_error(
                "⚠️ Aucun alias défini pour l'agrégation {$this->type}({$this->column})",
                E_USER_WARNING
            );
        }
        return $sql;
    }
}
