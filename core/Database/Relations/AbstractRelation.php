<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;

/** Classe de base pour toutes les relations.
 * Centralise la logique commune : extraction des colonnes préfixées.
 *
 * Chaque relation concrète implémente :
 * - hydrate()   : comment regrouper les lignes (rows ou row)
 * - applyJoin() : quel type de JOIN appliquer
 */
abstract class AbstractRelation
{
    /** Gère les relations one-to-many de façon générique et réutilisable.
     *
     * Exemple : un User a plusieurs Address (addresses.user_id = users.id)
     *
     * $userAddresses = new OneToManyRelation(
     *     key: 'addresses',
     *     idKey: 'id',
     *     relationColumns: ['id', 'street', 'city'],
     *     relationPrefix: 'address_',
     *     relatedTable: 'addresses',
     *     localKey: 'users.id',
     *     foreignKey: 'addresses.user_id',
     * );
     */
    public function __construct(
        // PARAMS
        protected string $key,  // Clé du résultat final Ex: $user['roles']
        protected string $idKey = 'id',  // Clé qui sert à regrouper les résultats Ex: par $user['id']
        protected array  $relationColumns = [], // Colonnes à récupérer dans la table cible

        // PARAMS (JOIN)
        protected string $relatedTable = '',
        protected string $localKey = '',
        protected string $foreignKey = '',
    ) {}

    protected function groupRelatedRows(array $mainRows, array $relatedRows): array
    {
        /** Construction de la table de correspondance
         * 
         * Exemple:
         * $grouped = [
         *  1 => [[...Article A...], [...Article B...], ...],
         *  2 => [[...Article C...], ...]]
         * ];
         * 
         */
        $grouped = [];
        foreach ($relatedRows as $related) {
            $fkValue = $related['fk'] ?? null; // clé temporaire injectée par fetchRelated
            if ($fkValue !== null) {
                $grouped[$fkValue][] = $related;
            }
        }

        /** Injection dans les parents
         * 
         * Exemple:
         * [
         *  ['id' => 1, 'name' => 'Jean', 'posts' => [['id' => 10, 'title' => 'Article A'], ['id' => 11, 'title' => 'Article B']]],
         *  ['id' => 2, 'name' => 'Paul', 'posts' => [['id' => 12, 'title' => 'Article C'],
         * ]
         * 
         */
        return array_map(function (array $row) use ($grouped) {
            $mainId = $row[$this->idKey];
            $row[$this->key] = $grouped[$mainId] ?? [];
            return $row;
        }, $mainRows);
    }

    public function extractKeys(array $rows): array
    {
        $keys = array_column($rows, $this->idKey);
        return array_values(array_unique(array_filter($keys, fn($v) => $v !== null)));
    }

    protected function flatRelatedRows(array $mainRows, array $relatedRows): array
    {
        /** Construction de la table de correspondance
         * (Contrairement à groupRelatedRows(), chaque clé ne contient qu'une seule ligne liée.)
         *
         * Exemple:
         * $indexed = [1 => ['id' => 10, 'title' => 'Article A'], 2 => ['id' => 12, 'title' => 'Article C'], ... ];
         *
         */
        $indexed = [];
        foreach ($relatedRows as $related) {
            $fk = $related['fk'] ?? null; // clé temporaire injectée par fetchRelated

            if ($fk !== null) {
                $indexed[$fk] = $related;
            }
        }

        /** Injection dans les parents
         *
         * Exemple:
         * [
         *     ['id' => 1, 'name' => 'Jean', 'profile' => ['id' => 10, 'bio' => 'Développeur PHP']],
         *     ['id' => 2, 'name' => 'Paul', 'profile' => ['id' => 12, 'bio' => 'Designer']],
         *     ['id' => 3, 'name' => 'Marc', 'profile' => null] (<= Si aucune relation n'existe)
         * ]
         * 
         */
        return array_map(function (array $row) use ($indexed) {
            $mainId = $row[$this->idKey];
            $related = $indexed[$mainId] ?? null;
            if ($related !== null) {
                unset($related['fk']);
            }
            $row[$this->key] = $related;
            return $row;
        }, $mainRows);
    }

    /** Hydrate une collection de lignes brutes (issues d'un JOIN) en entités avec relations.
     *
     * @param array $mainRows Lignes brutes issues de la requête SQL
     * @return array Entités hydratées avec les relations chargées
     */
    abstract public function hydrate(array $mainRows, array $relatedRows): array;
    abstract public function fetchRelated(\PDO $pdo, array $ids): array;

    /** Chaque implémentation fait son propre join */
    abstract public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface;
}
