<?php

namespace Src\Modules\Auth\Application\Service;

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
        $user = $this->userRepo->findOne(new UserQuery(email: $email));

        if (!$user || !password_verify($password, $user->passwordHashed)) {
            throw new AuthentificationException("Identifiants incorrects.");
        }

        return ['id' => $user->id, 'email' => $user->email, 'role' => $user->roles[0]->name];
    }

    public function updateUserLastLogin(int $userId): void
    {
        $this->userRepo->updateLastLogin($userId);
    }
}
