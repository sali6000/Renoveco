<?php

namespace Core\Database;

/**
 * Interface pour le QueryBuilder.
 */
interface QueryBuilderInterface
{
    /**
     * @param string[] $columns Liste des colonnes à sélectionner
     */
    public function select(array $columns = ['*']): static;

    public function from(string $table): static;

    public function joinLeft(string $toTable, string $toTableFK, string $fromTablePK): static;

    public function joinManyToMany(string $toTablePivot, string $fromTablePK, string $toTablePivotFK, string $toTable, string $fromTablePivotFK, string $toTablePK): static;

    /**
     * @param string $condition Condition SQL (ex: "id = :id")
     * @param array<string, mixed> $params Paramètres liés à la condition
     */
    public function where(string $condition, array $params = []): static;

    public function groupBy(string $column): static;

    public function orderBy(string $column, string $direction = 'ASC'): static;

    public function limit(int $limit): static;

    public function offset(int $offset): static;

    /**
     * Retourne la requête SQL complète sous forme de chaîne
     */
    public function buildQuery(): string;

    /**
     * Exécute la requête et retourne le résultat brut
     *
     * @return mixed Le résultat dépend du type de requête (SELECT, INSERT, UPDATE...)
     */
    public function execute(): mixed;

    /**
     * Retourne la requête SQL pour sous-requête avec alias optionnel
     */
    public function toSubSql(?string $alias = null): string;

    /**
     * @param array<string, mixed> $data Données à insérer (clé = nom colonne, valeur = valeur)
     * @return int L’ID de l’élément inséré
     */
    public function insert(string $table, array $data): int;

    /**
     * Exécute la requête et retourne le premier champ de la première ligne
     */
    public function executeAndFetchColumn(int $numColumn = 0): mixed;

    /**
     * Exécute la requête et retourne la première ligne complète
     */
    public function executeAndFetchOne(): ?array;

    /**
     * Exécute la requête et retourne toutes les lignes
     */
    public function executeAndFetchAll(): array;

    /**
     * Retourne le dernier ID inséré (après insert)
     */
    public function returnInsertId(): int;

    /**
     * @param array<string, mixed> $data Données à mettre à jour (clé = nom colonne, valeur = valeur)
     * @param array<string, mixed> $params Paramètres pour la condition WHERE
     */
    public function update(string $table, array $data, string $where, array $params = []): bool;
}
