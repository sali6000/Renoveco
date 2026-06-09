<?php

declare(strict_types=1);

namespace Src\Modules\User\Infrastructure\Persistence\Mysql;

use Config\AppConfig;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Core\Support\DebugHelper;
use Src\Exception\Domain\RoleNotFoundException;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;
use Src\Modules\User\Domain\Entity\User;
use Src\Modules\User\Domain\Query\UserQuery;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;
use Src\Modules\User\Infrastructure\Schema\RoleSchemaMysql;
use Src\Modules\User\Infrastructure\Schema\UserRoleSchemaMysql;
use Src\Modules\User\Infrastructure\Schema\UserSchemaMysql;

class UserRepositoryMysql extends RepositoryMysql implements UserRepositoryInterface
{
    //----------------------------------------------------------------------------
    // COLUMNS SELECT :
    //----------------------------------------------------------------------------

    private const USER_COLUMNS = [
        UserSchemaMysql::ID,
        UserSchemaMysql::EMAIL,
        UserSchemaMysql::IS_ACTIVE,
        UserSchemaMysql::LAST_LOGIN_AT,
    ];

    private const USER_COLUMNS_WITH_CREDENTIALS = [
        UserSchemaMysql::ID,
        UserSchemaMysql::EMAIL,
        UserSchemaMysql::IS_ACTIVE,
        UserSchemaMysql::LAST_LOGIN_AT,
        UserSchemaMysql::PASSWORD_HASH,
    ];

    private const ROLE_COLUMNS = [
        RoleSchemaMysql::ID,
        RoleSchemaMysql::NAME,
    ];

    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $qb
    ) {
        parent::__construct($pdo, $qb);
    }

    //----------------------------------------------------------------------------
    // PREPARE METHODS SCHEMES :
    //----------------------------------------------------------------------------

    protected function getTable(): string
    {
        return UserSchemaMysql::TABLE;
    }

    protected function fromArray(array $row): User
    {
        return User::fromArray($row);
    }

    //----------------------------------------------------------------------------
    // QUERIES SELECT :
    //----------------------------------------------------------------------------

    // SELECT : FIND ONE 
    public function findOne(UserQuery $q): ?User
    {
        return $this->executeFindOne($q, self::USER_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

    // SELECT : FIND ALL
    public function findAll(UserQuery $q): array
    {
        return $this->executeMany($q, self::USER_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

    // SELECT : FIND ONE (FOR AUTH SERVICE)
    public function findOneForAuth(UserQuery $q): ?User
    {
        return $this->executeFindOne($q, self::USER_COLUMNS_WITH_CREDENTIALS, $this->applyFilters(...), $this->applyRelations(...));
    }

    //----------------------------------------------------------------------------
    // FILTERS MAKER :
    //----------------------------------------------------------------------------

    // PREPARE FILTERS (conditions)
    private function applyFilters(QueryBuilderInterface $qb, UserQuery $q): QueryBuilderInterface
    {
        // EMAIL
        if ($q->email !== null) $qb = $qb->where(UserSchemaMysql::EMAIL . ' = :email', [':email' => $q->email]);

        // ID
        if ($q->id !== null) $qb = $qb->where(UserSchemaMysql::ID . ' = :id', [':id' => $q->id]);

        // IS ACTIVE
        if ($q->isActive !== null) {
            $active = $q->isActive ? 'TRUE' : 'FALSE';
            $qb = $qb->where(UserSchemaMysql::IS_ACTIVE . " = {$active}");
        }

        return $qb;
    }

    //----------------------------------------------------------------------------
    // RELATIONS MAKER :
    //----------------------------------------------------------------------------

    // CONDITIONS JOINS (Depend of UserQuery params)
    private function applyRelations(UserQuery $q): array
    {
        $relations = [];

        if ($q->withRoles) $relations[] = UserRoleSchemaMysql::userRolesRelation(self::ROLE_COLUMNS);
        return $relations;
    }

    //----------------------------------------------------------------------------
    // (OTHERS) WRITE => INSERT, UPDATE, ... :
    //----------------------------------------------------------------------------

    // SAVE ONE : USER
    public function save(User $user): User
    {
        $data = [
            UserSchemaMysql::EMAIL         => $user->email,
            UserSchemaMysql::PASSWORD_HASH => $user->passwordHashed,
        ];

        if ($user->id !== null) {
            $this->qb->update(
                UserSchemaMysql::TABLE,
                $data,
                UserSchemaMysql::ID . ' = :id',
                ['id' => $user->id]
            );
        } else {
            $ok = $this->qb->insert(UserSchemaMysql::TABLE, $data);
            if ($ok) {
                $user->id = $this->qb->returnInsertId();
            }
        }

        $this->syncRoles($user);

        return $user;
    }

    // Ajoute le lien user-role en base si le rôle n'existe pas déjà.
    private function syncRoles(User $user): void
    {
        $roles = $user->roles;
        if (empty($roles)) {
            return;
        }

        // Itère sur TOUS les rôles
        foreach ($user->roles as $role) {
            $roleId = $this->qb
                ->select([RoleSchemaMysql::ID])
                ->from(RoleSchemaMysql::TABLE)
                ->where(RoleSchemaMysql::NAME . ' = :name', [':name' => $role->name])
                ->executeAndFetchColumn();

            if (!$roleId) {
                throw new RoleNotFoundException("Role '{$role->name}' inexistant");
            }

            $exists = (bool) $this->qb
                ->select(['COUNT(*)'])
                ->from(UserRoleSchemaMysql::TABLE)
                ->where('user_id = :uid AND role_id = :rid', [
                    ':uid' => $user->id,
                    ':rid' => $roleId,
                ])
                ->executeAndFetchColumn();

            if (!$exists) {
                $this->qb->insert(UserRoleSchemaMysql::TABLE, [
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    // Met à jour la date de dernière connexion pour un user.
    public function updateLastLogin(int $userId): void
    {
        $this->qb->update(
            UserSchemaMysql::TABLE,
            [
                HelperSchemaMysql::fieldProperty(UserSchemaMysql::LAST_LOGIN_AT) => (new \DateTime('now', new \DateTimeZone(AppConfig::getEnv('DATETIMEZONE'))))
                    ->format('Y-m-d H:i:s')
            ],
            UserSchemaMysql::ID . ' = :id',
            ['id' => $userId]
        );
    }
}
