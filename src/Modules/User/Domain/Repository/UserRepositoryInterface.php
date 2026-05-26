<?php

namespace Src\Modules\User\Domain\Repository;

use Src\Modules\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * Récupérer tous les utilisateurs pour l'admin
     * 
     * @throws \PDOException        si erreur base de données
     */
    public function findAllForAdmin(): array;

    /**
     * Récupérer un user pour l'authentification
     * 
     * @throws \PDOException        si erreur base de données
     */
    public function findForLogin(string $email): ?User;

    /**
     * Sauvegarder un nouvel user
     * 
     * @throws \PDOException        si erreur base de données
     */
    public function save(User $user): User;

    /**
     * Mettre à jour en base la date de dernière connection de l'user
     * 
     * @throws \PDOException        si erreur base de données
     */
    public function updateLastLogin(int $userId): void;
}
