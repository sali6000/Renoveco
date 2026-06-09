<?php

namespace Src\Modules\Auth\Application\Service;

use Core\Support\DebugHelper;
use Src\Exception\Application\AuthentificationException;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;
use Src\Modules\User\Domain\Query\UserQuery;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;
use Src\Modules\User\Infrastructure\Schema\RoleSchemaMysql;
use Src\Modules\User\Infrastructure\Schema\UserSchemaMysql;

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
        $user = $this->userRepo->findOneForAuth(new UserQuery(email: $email, withRoles: true));

        // Vérification du mot de passe
        if (!$user || !password_verify($password, $user->passwordHashed)) {
            throw new AuthentificationException("Identifiants incorrects.");
        }

        $this->userRepo->updateLastLogin($user->id);

        return [
            HelperSchemaMysql::fieldProperty(UserSchemaMysql::ID) => $user->id,
            HelperSchemaMysql::fieldProperty(UserSchemaMysql::EMAIL) => $user->email,
            HelperSchemaMysql::fieldTable(RoleSchemaMysql::TABLE) => $user->roles[0]->name
        ];
    }

    public function updateUserLastLogin(int $userId): void
    {
        $this->userRepo->updateLastLogin($userId);
    }
}
