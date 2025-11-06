<?php

namespace App\Modules\User\Repository;

use Core\Database\Repository;
use Core\Database\QueryBuilderInterface;
use App\Database\Schema;
use App\Modules\User\Entity\UserModel;

class UserRepository extends Repository
{
    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($pdo, UserModel::class); // CRUD auto sur base du model
    }

    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->queryBuilder
            ->selectFrom(SCHEMA::TABLE_USERS, [
                SCHEMA::USER_ALL,
                SCHEMA::ROLE_NAME . ' AS role_name'
            ])
            ->selectJoinManyToMany(
                SCHEMA::TABLE_PIVOT_ROLE_USER,
                SCHEMA::TABLE_ROLES
            )
            ->where(SCHEMA::USER_EMAIL . ' = :email', [':email' => $email])
            ->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
