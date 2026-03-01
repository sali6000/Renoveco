<?php

namespace Core\Database;

class RepositoryMysql
{
    public function __construct(protected \PDO $pdo) {}

    // findById(string $table, int $id)
    // findAll(string $table)
    // paginate(), beginTransaction(), commit(), ...

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
