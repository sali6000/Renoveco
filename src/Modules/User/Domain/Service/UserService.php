<?php

namespace Src\Modules\User\Domain\Service;

use Src\Database\SchemaMysql;
use Src\Exception\ServiceException;
use Src\Exception\UniqueConstraintException;
use Core\Logger\AccessLogger;
use Src\Modules\User\Domain\Entity\User;
use Src\Modules\User\Domain\Entity\Role;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;
use PDOException;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepo) {}

    public function getAllUsersForAdmin(): array
    {
        return $this->userRepo->findAllForAdmin();
    }

    public function deleteUser(int $id): void
    {
        //$this->categoryRepo->deleteCategoryById($id);
    }

    public function createUser(string $email, string $password): User
    {
        try {

            // Création d'un user avec email et password (hasché dans le setPassword)
            $user = new User($email);
            $user->hashAndSetPassword($password);

            // Alimentation du rôle avec rôle "user" actif par défaut
            $role = new Role("user");
            $user->addRole($role);

            // Créer et retourner l'utilisateur
            return $this->userRepo->save($user);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Si duplication à cause d'une contrainte UNIQUE
                $message = $e->errorInfo[2]; // Ex: Duplicate entry ... for key 'users.email'

                // Si le message d'erreur contient "users.email" renvoyer une erreur sur "email"
                if (str_contains(
                    $message,
                    SchemaMysql::fieldTable(SchemaMysql::TABLE_USERS) . "." .
                        SchemaMysql::fieldProperty(SchemaMysql::USER_EMAIL)
                )) {
                    throw new UniqueConstraintException(SchemaMysql::USER_EMAIL);
                }

                AccessLogger::logTo($e, AccessLogger::LEVEL_ERROR, AccessLogger::CHANNEL_DATABASE);
                throw new UniqueConstraintException('unknown');
            }
            throw $e; // PDOException non 1062 → remonte, le kernel logue
        }
    }
}
