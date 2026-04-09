<?php

namespace Src\Modules\User\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Src\Database\SchemaMysql;
use Src\Modules\User\Domain\Entity\Role;
use Src\Modules\User\Domain\Entity\User;
use Core\Database\RepositoryMysql;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;
use Core\Support\DebugHelper;
use DateTime;

class UserRepositoryMysql extends RepositoryMysql implements UserRepositoryInterface
{
    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($pdo); // CRUD auto sur base du model
    }

    /**
     * @return User[]
     */
    public function findAllForAdmin(): array
    {
        $stmt = $this->queryBuilder
            ->select([
                SchemaMysql::USER_ALL,
                SchemaMysql::ROLE_NAME . ' AS role_name',
                SchemaMysql::ROLE_ID . ' AS role_id'
            ])
            ->from(SchemaMysql::TABLE_USERS)
            ->joinManyToMany(
                SchemaMysql::TABLE_PIVOT_ROLE_USER,
                SchemaMysql::USER_ID,
                SchemaMysql::PIVOT_ROLE_USER_FK_USER,
                SchemaMysql::TABLE_ROLES,
                SchemaMysql::PIVOT_ROLE_USER_FK_ROLE,
                SchemaMysql::ROLE_ID
            )
            ->executeAndFetchAll();

        $users = [];
        foreach ($stmt as $row) {
            $userId = $row['id'];
            if (!isset($users[$userId])) {
                $user = new User($row['email']);
                $user->id = $row['id'];
                $user->passwordHashed = $row['password_hash'];
                $users[$userId] = $user;
            }
            $role = new Role($row['role_name']);
            $role->id = $row['role_id'];
            $users[$userId]->addRole($role);
        }
        return array_values($users);
    }

    public function findForLogin(string $email): ?User
    {
        $stmt = $this->queryBuilder
            ->select([
                SchemaMysql::USER_ALL,
                SchemaMysql::ROLE_NAME . ' AS role_name'
            ])
            ->from(SchemaMysql::TABLE_USERS)
            ->joinManyToMany(
                SchemaMysql::TABLE_PIVOT_ROLE_USER,
                SchemaMysql::USER_ID,
                SchemaMysql::PIVOT_ROLE_USER_FK_USER,
                SchemaMysql::TABLE_ROLES,
                SchemaMysql::PIVOT_ROLE_USER_FK_ROLE,
                SchemaMysql::ROLE_ID
            )
            ->where(SchemaMysql::USER_EMAIL . ' = :email', [':email' => $email])
            ->executeAndFetchOne();

        if (!$stmt) {
            return null;
        }

        // Hydrater l'utilisateur
        $user = new User($stmt['email']);
        $user->id = $stmt['id'];
        $user->passwordHashed = $stmt['password_hash'];
        $user->addRole(new Role($stmt['role_name']));
        return $user ?? null;
    }

    public function updateLastLogin(int $userId): void
    {
        // Mettre à jour la date de dernier login
        $data = [
            SchemaMysql::fieldProperty(SchemaMysql::USER_LAST_LOGIN_AT) => (new \DateTime('now', new \DateTimeZone('Europe/Brussels')))->format('Y-m-d H:i:s')
        ];

        $this->queryBuilder->update(SchemaMysql::TABLE_USERS, $data, SchemaMysql::USER_ID . ' = :id', ['id' => $userId]);
    }

    public function save(User $user): User
    {
        // 1️⃣ Création / mise à jour de l'utilisateur
        $data = [
            SchemaMysql::USER_EMAIL => $user->email,
            SchemaMysql::USER_PASSWORD_HASH => $user->passwordHashed
        ];

        if ($user->id) {
            $ok = $this->queryBuilder
                ->update(SchemaMysql::TABLE_USERS, $data, SchemaMysql::USER_ID . ' = :id', ['id' => $user->id]);
        } else {
            $ok = $this->queryBuilder
                ->insert(SchemaMysql::TABLE_USERS, $data);
            if ($ok) {
                $user->id = $this->queryBuilder->returnInsertId();
            }
        }

        // 2️⃣ Associer les rôles (table pivot role_user)
        // Récupérer l'ID du rôle
        $roleId = $this->queryBuilder
            ->select([SchemaMysql::ROLE_ID])
            ->from(SchemaMysql::TABLE_ROLES)
            ->where(SchemaMysql::ROLE_NAME . ' = :name', ['name' => $user->getRoles()[0]->name])
            ->executeAndFetchColumn();

        if (!$roleId) {
            throw new \Exception("Role '{$user->getRoles()[0]->name}' inexistant");
        }

        // Vérifier si l’association existe déjà pour éviter doublon
        $exists = (bool) $this->queryBuilder
            ->select(['COUNT(*)'])
            ->from(SchemaMysql::TABLE_PIVOT_ROLE_USER)
            ->where('user_id = :uid AND role_id = :rid', [
                'uid' => $user->id,
                'rid' => $roleId
            ])->executeAndFetchColumn(); // retourne vrai/faux

        if (!$exists) {
            $this->queryBuilder->insert(SchemaMysql::TABLE_PIVOT_ROLE_USER, [
                'user_id' => $user->id,
                'role_id' => $roleId
            ]);
        }
        return $user;
    }
}
