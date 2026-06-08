<?php

declare(strict_types=1);

namespace Src\Modules\User\Infrastructure\Persistence\Mysql;

use Config\AppConfig;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Database\SchemaMysql;
use Src\Exception\Domain\RoleNotFoundException;
use Src\Modules\User\Domain\Entity\User;
use Src\Modules\User\Domain\Query\UserQuery;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;

class UserRepositoryMysql extends RepositoryMysql implements UserRepositoryInterface
{
    //----------------------------------------------------------------------------
    // COLUMNS SELECT :
    //----------------------------------------------------------------------------

    private const USER_COLUMNS = [
        SchemaMysql::USER_ID,
        SchemaMysql::USER_EMAIL,
        SchemaMysql::USER_IS_ACTIVE,
        SchemaMysql::USER_LAST_LOGIN_AT,
    ];

    private const USER_COLUMNS_WITH_CREDENTIALS = [
        SchemaMysql::USER_ID,
        SchemaMysql::USER_EMAIL,
        SchemaMysql::USER_IS_ACTIVE,
        SchemaMysql::USER_LAST_LOGIN_AT,
        SchemaMysql::USER_PASSWORD_HASH,
    ];

    private const ROLE_COLUMNS = [
        SchemaMysql::ROLE_ID,
        SchemaMysql::ROLE_NAME,
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
        return SchemaMysql::TABLE_USERS;
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
        if ($q->email !== null) $qb = $qb->where(SchemaMysql::USER_EMAIL . ' = :email', [':email' => $q->email]);

        // ID
        if ($q->id !== null) $qb = $qb->where(SchemaMysql::USER_ID . ' = :id', [':id' => $q->id]);

        // IS ACTIVE
        if ($q->isActive !== null) {
            $active = $q->isActive ? 'TRUE' : 'FALSE';
            $qb = $qb->where(SchemaMysql::USER_IS_ACTIVE . " = {$active}");
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

        if ($q->withRoles) $relations[] = SchemaMysql::userRolesRelation(self::ROLE_COLUMNS);

        return $relations;
    }

    //----------------------------------------------------------------------------
    // (OTHERS) WRITE => INSERT, UPDATE, ... :
    //----------------------------------------------------------------------------

    // SAVE ONE : USER
    public function save(User $user): User
    {
        $data = [
            SchemaMysql::USER_EMAIL         => $user->email,
            SchemaMysql::USER_PASSWORD_HASH => $user->passwordHashed,
        ];

        if ($user->id !== null) {
            $this->qb->update(
                SchemaMysql::TABLE_USERS,
                $data,
                SchemaMysql::USER_ID . ' = :id',
                ['id' => $user->id]
            );
        } else {
            $ok = $this->qb->insert(SchemaMysql::TABLE_USERS, $data);
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
                ->select([SchemaMysql::ROLE_ID])
                ->from(SchemaMysql::TABLE_ROLES)
                ->where(SchemaMysql::ROLE_NAME . ' = :name', [':name' => $role->name])
                ->executeAndFetchColumn();

            if (!$roleId) {
                throw new RoleNotFoundException("Role '{$role->name}' inexistant");
            }

            $exists = (bool) $this->qb
                ->select(['COUNT(*)'])
                ->from(SchemaMysql::TABLE_ROLE_USER)
                ->where('user_id = :uid AND role_id = :rid', [
                    ':uid' => $user->id,
                    ':rid' => $roleId,
                ])
                ->executeAndFetchColumn();

            if (!$exists) {
                $this->qb->insert(SchemaMysql::TABLE_ROLE_USER, [
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
            SchemaMysql::TABLE_USERS,
            [
                SchemaMysql::fieldProperty(SchemaMysql::USER_LAST_LOGIN_AT) => (new \DateTime('now', new \DateTimeZone(AppConfig::getEnv('DATETIMEZONE'))))
                    ->format('Y-m-d H:i:s')
            ],
            SchemaMysql::USER_ID . ' = :id',
            ['id' => $userId]
        );
    }
}
