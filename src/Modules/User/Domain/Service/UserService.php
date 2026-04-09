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
                    throw new UniqueConstraintException(SchemaMysql::fieldProperty(SchemaMysql::USER_EMAIL));
                }
                $errorId = uniqid('usr_srvc_pdo_', true);
                AccessLogger::log("Contrainte UNIQUE inconnue (Code : $errorId) " . $message, AccessLogger::LEVEL_WARNING);
                throw new UniqueConstraintException("unknown");
            }
            throw $e;
        } catch (\Throwable $e) {
            $errorId = uniqid('usr_srvc_', true);
            AccessLogger::log("Erreur de service (Code : $errorId) " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Erreur de création d'utilisateur (Code : $errorId)", 0, $e, $errorId);
        }
    }
}
