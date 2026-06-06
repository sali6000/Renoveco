<?php

namespace Src\Modules\Auth\Application\Service;

use Src\Database\SchemaMysql;
use Src\Exception\Application\AuthentificationException;
use Src\Modules\User\Domain\Query\UserQuery;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;

final class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {}

    /**
     * Connection d'un utilisateur
     * 
     * @throws AuthentificationException si identifiants incorrects
     * @throws \PDOException si erreur base de données
     */
    public function loginUser(string $email, string $password): ?array
    {
        // Récupérer une utilisateur :
        $user = $this->userRepo->findOneForAuth(new UserQuery(email: $email));

        // Vérification du mot de passe
        if (!$user || !password_verify($password, $user->passwordHashed)) {
            throw new AuthentificationException("Identifiants incorrects.");
        }

        $this->userRepo->updateLastLogin($user->id);

        // Attribution du rôle
        $roleName = isset($user->roles[0]) ? $user->roles[0]->name : null;

        return [
            SchemaMysql::fieldProperty(SchemaMysql::USER_ID) => $user->id,
            SchemaMysql::fieldProperty(SchemaMysql::USER_EMAIL) => $user->email,
            SchemaMysql::fieldProperty(SchemaMysql::ROLE_NAME) => $roleName
        ];
    }

    public function updateUserLastLogin(int $userId): void
    {
        $this->userRepo->updateLastLogin($userId);
    }
}
