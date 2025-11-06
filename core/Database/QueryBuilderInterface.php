<?php

namespace Core\Database;

/**
 * Interface QueryBuilderInterface
 * 
 * 📘 Cette interface définit les méthodes fondamentales qu’un "constructeur de requêtes SQL" (Query Builder)
 * doit implémenter.
 * 
 * 👉 Le but : permettre de construire dynamiquement des requêtes SQL propres, lisibles,
 * et réutilisables sans jamais concaténer des chaînes de texte manuellement.
 * 
 * 💡 Chaque méthode retourne "static" pour permettre le chaînage fluide :
 * Exemple : 
 *    $qb->selectFrom('products', ['id', 'name'])
 *        ->where('id = :id', [':id' => 5])
 *        ->limit(1)
 *        ->execute();
 */
interface QueryBuilderInterface
{
    /**
     * Sélectionne une table et les colonnes à récupérer.
     * 
     * @param string $tableName Nom complet de la table avec éventuellement un alias.  
     *        Exemple : "products p"
     * @param array $columns Liste des colonnes à sélectionner.  
     *        Exemple : ['p.id', 'p.name', 'p.price'] ou ['*']
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p', ['p.id', 'p.name']);
     * 
     * 🔹 Génère :
     *    SELECT p.id, p.name FROM products p
     */
    public function selectFrom(string $tableName, array $columns = ['*']): static;


    /**
     * Effectue une jointure LEFT JOIN (relation One-To-One ou One-To-Many).
     * 
     * @param string $tableTarget Table à joindre avec alias.  
     *        Exemple : "product_images pi"
     * @param array $columns Colonnes à ajouter à la sélection.  
     *        Exemple : ['pi.file_path', 'pi.alt_text']
     * @param ?string $foreignKey Nom de la colonne de clé étrangère.  
     *        Exemple : "product_id"
     * @param ?string $localKey Nom de la clé locale (souvent "id").  
     *        Exemple : "id"
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p', ['p.id', 'p.name'])
     *       ->selectJoinLeft('product_images pi', ['pi.file_path'], 'product_id', 'id');
     * 
     * 🔹 Génère :
     *    SELECT p.id, p.name, pi.file_path
     *    FROM products p
     *    LEFT JOIN product_images pi ON pi.product_id = p.id
     */
    public function selectJoinLeft(
        string $tableTarget,
        array $columns = []
    ): static;

    /**
     * Effectue une jointure Many-To-Many via une table pivot (relation n-n).
     * 
     * @param string $tablePivot Table pivot (relation intermédiaire entre deux entités).  
     *        Exemple : "product_category_assignements pca"
     * @param string $tableTarget Table cible finale.  
     *        Exemple : "product_categories pc"
     * @param array $columns Colonnes à sélectionner de la table cible.  
     *        Exemple : ["pc.id", "pc.name"]
     * @param ?string $joinColumn Colonne reliant la table pivot à la table cible.  
     *        Exemple : "category_id"
     * @param ?string $inverseJoinColumn Colonne reliant la table pivot à la table principale.  
     *        Exemple : "product_id"
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p', ['p.id', 'p.name'])
     *       ->selectJoinManyToMany(
     *           'product_category_assignements pca',
     *           'product_categories pc',
     *           ['pc.name']
     *       );
     * 
     * 🔹 Génère :
     *    SELECT p.id, p.name, pc.name
     *    FROM products p
     *    LEFT JOIN product_category_assignements pca ON pca.product_id = p.id
     *    LEFT JOIN product_categories pc ON pc.id = pca.category_id
     */
    public function selectJoinManyToMany(
        string $tablePivot,
        string $tableTarget,
        array $columns = []
    ): static;

    /**
     * Ajoute une condition WHERE à la requête.
     * 
     * @param string $condition Expression SQL.  
     *        Exemple : "p.id = :id AND p.active = TRUE"
     * @param array $params Tableau des paramètres pour une requête préparée.  
     *        Exemple : [':id' => 5]
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p')
     *       ->where('p.id = :id', [':id' => 5]);
     * 
     * 🔹 Génère :
     *    SELECT * FROM products p WHERE p.id = :id
     */
    public function where(string $condition, array $params = []): static;

    /**
     * Ajoute un GROUP BY à la requête.
     * 
     * @param string $column Colonne sur laquelle regrouper.  
     *        Exemple : "p.category_id"
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p', ['p.category_id', 'COUNT(*) as total'])
     *       ->groupBy('p.category_id');
     * 
     * 🔹 Génère :
     *    SELECT p.category_id, COUNT(*) as total FROM products p GROUP BY p.category_id
     */
    public function groupBy(string $column): static;


    /**
     * Trie les résultats (ORDER BY).
     * 
     * @param string $column Colonne de tri.  
     *        Exemple : "p.created_at"
     * @param string $direction Sens du tri : 'ASC' (croissant) ou 'DESC' (décroissant).
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p')
     *       ->orderBy('p.created_at', 'DESC');
     * 
     * 🔹 Génère :
     *    SELECT * FROM products p ORDER BY p.created_at DESC
     */
    public function orderBy(string $column, string $direction = 'ASC'): static;


    /**
     * Limite le nombre de résultats retournés.
     * 
     * @param int $limit Nombre maximum de lignes.
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p')->limit(10);
     * 
     * 🔹 Génère :
     *    SELECT * FROM products p LIMIT 10
     */
    public function limit(int $limit): static;


    /**
     * Définit un décalage (OFFSET) — utile pour la pagination.
     * 
     * @param int $offset Nombre de lignes à ignorer avant de commencer à retourner les résultats.
     * 
     * @return static
     * 
     * 🧠 Exemple :
     *    $qb->selectFrom('products p')->limit(10)->offset(20);
     * 
     * 🔹 Génère :
     *    SELECT * FROM products p LIMIT 10 OFFSET 20
     */
    public function offset(int $offset): static;


    /**
     * Construit et retourne la requête SQL complète sous forme de chaîne.
     * 
     * @return string
     * 
     * 🧠 Exemple :
     *    echo $qb->buildQuery();
     * 
     * 🔹 Résultat :
     *    "SELECT p.id, p.name FROM products p WHERE p.active = TRUE LIMIT 5"
     */
    public function buildQuery(): string;


    /**
     * Exécute la requête SQL finale et retourne le résultat.
     * 
     * @return mixed Peut être un PDOStatement, un tableau, ou un bool selon l’implémentation.
     * 
     * 🧠 Exemple :
     *    $results = $qb->selectFrom('products')->execute();
     * 
     * 🔹 Résultat possible :
     *    [
     *        ['id' => 1, 'name' => 'Chaise'],
     *        ['id' => 2, 'name' => 'Table']
     *    ]
     */
    public function execute(): mixed;

    public function toSubSql(?string $alias = null): string;
    public function insert(string $table, array $data): bool;
    public function lastInsertId(): string;
    public function update(string $table, array $data, string $where, array $params = []): bool;
}
