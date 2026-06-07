<?php

declare(strict_types=1);

namespace Core\Database\Relations;

use Core\Database\QueryBuilderInterface;
use Core\Support\DebugHelper;
use Src\Database\SchemaMysql;

/**
 * Classe de base pour toutes les relations.
 * Centralise la logique commune : extraction des colonnes préfixées.
 *
 * Chaque relation concrète implémente :
 * - hydrate()   : comment regrouper les lignes (rows ou row)
 * - applyJoin() : quel type de JOIN appliquer
 * - getColumns(): quelles colonnes sélectionner
 */
abstract class AbstractRelation implements RelationInterface
{
    /**
     * Gère les relations one-to-many de façon générique et réutilisable.
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
        protected string $key,  // Clé du résultat final Ex: $user['roles']
        protected string $idKey = 'id',  // Clé qui sert à regrouper les résultats Ex: par $user['id']
        protected array  $relationColumns = [], // Colonnes à récupérer dans la table cible
        protected string $relationPrefix = '', // Préfixe des alias SQL Ex: 'role_'

        // JOIN PARAMS
        protected string $relatedTable = '',
        protected string $localKey = '',
        protected string $foreignKey = '',
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Retourne les colonnes (avec préfixe) à sélectionner dans la requête SQL.
     *
     * Exemple : si tu demandes ['id', 'name'] avec préfixe 'role_',
     * ça retourne ['role_id', 'role_name'] pour qu'on les sélectionne dans la requête.
     *
     * @return string[]
     */
    public function getColumns(): array
    {
        if (empty($this->relationColumns)) {
            return [];
        }

        // Ex: role_id AS role_id, role_name AS role_name
        $result = array_map(
            fn(string $col) => $col . ' AS ' . $this->relationPrefix . SchemaMysql::fieldProperty($col),
            $this->relationColumns
        );

        return $result;
    }

    /**
     * Extrait les colonnes d'une relation depuis une ligne brute.
     * Identifie les colonnes via le préfixe, puis le strip.
     *
     * Entrée : ['id' => 1, 'email' => '...', 'role_id' => 2, 'role_name' => 'admin']
     * Sortie : ['id' => 2, 'name' => 'admin']
     */
    protected function extractRelationData(array $row): array
    {
        if (empty($this->relationPrefix)) {
            return [];
        }

        $relationData = [];

        foreach ($row as $column => $value) {

            // Vérifie si la colonne appartient à la relation (démarre avec le préfixe)
            if (str_starts_with($column, $this->relationPrefix)) {

                // Enlève le préfixe : 'role_id' → 'id'
                $cleanColumn = substr($column, strlen($this->relationPrefix));

                if (empty($this->relationColumns) || in_array($cleanColumn, array_map(
                    fn(string $col) => SchemaMysql::fieldProperty($col),
                    $this->relationColumns
                ))) {
                    $relationData[$cleanColumn] = $value;
                }
            }
        }

        return $relationData;
    }

    /**
     * Regroupe les lignes par entité principale.
     * Utilisé par OneToMany et ManyToMany — chaque parent a plusieurs enfants.
     *
     * Entrée (1 user, 2 rôles) :
     * [
     *   ['id' => 1, 'email' => '...', 'role_id' => 1, 'role_name' => 'admin'],
     *   ['id' => 1, 'email' => '...', 'role_id' => 2, 'role_name' => 'editor'],
     * ]
     *
     * Sortie :
     * [
     *   ['id' => 1, 'email' => '...', 'roles' => [['id' => 1, ...], ['id' => 2, ...]]]
     * ]
     */
    protected function groupRows(array $rows): array
    {
        $grouped = [];

        // ROWS[] -> ROW (FOREACH)
        foreach ($rows as $row) {

            // Identifié la clé 'id' de la row en cours
            // (un user a plusieurs rôles, donc plusieurs lignes associés à ce même 'id')
            $mainId = $row[$this->idKey];

            // Première row : Enregistrer la row + initialiser un tableau de relations vide Ex: 
            // $grouped[1] = ['mainRow' => $row, 'relations' => []];
            if (!isset($grouped[$mainId])) {
                $grouped[$mainId] = [
                    'mainRow'   => $row,
                    'relations' => [],
                ];
            }

            // Extraire les colonnes de la relation (enlève les préfixes) Ex:
            // 1. ['role_id' => 1, 'role_name' => 'admin']
            // 2. ['id' => 1, 'name' => 'admin']
            $relationData = $this->extractRelationData($row);

            // Ajouter le rôle à l'User
            // (Exemple après 2 résultats trouvés pour le même mainId):
            // $grouped[1]['relations'] = [['id'=>1,'name'=>'admin'],['id'=>2,'name'=>'editor']];
            if (!empty($relationData)) {
                $grouped[$mainId]['relations'][] = $relationData;
            }
        }

        // Récupérer un tableau prêt et indexé
        // ['roles' => [['id'=>1,'name'=>'admin'],['id'=>2,'name'=>'editor']]]
        return array_values(array_map(function (array $entry) {
            $mainRow = $entry['mainRow'];
            $mainRow[$this->key] = $entry['relations'];
            return $mainRow;
        }, $grouped));
    }

    /**
     * Injecte la relation dans chaque ligne sans groupement.
     * Utilisé par ManyToOne et OneToOne — chaque enfant a exactement un parent.
     *
     * Entrée :
     * ['id' => 1, 'user_id' => 5, 'user_email' => 'a@b.com']
     *
     * Sortie :
     * ['id' => 1, 'user_id' => 5, 'user' => ['email' => 'a@b.com']]
     */
    protected function flatRows(array $rows): array
    {
        return array_map(function (array $row) {
            $relationData = $this->extractRelationData($row);
            $row[$this->key] = !empty($relationData) ? $relationData : null;
            return $row;
        }, $rows);
    }

    abstract public function hydrate(array $rows): array;
    abstract public function applyJoin(QueryBuilderInterface $query): QueryBuilderInterface;
}
