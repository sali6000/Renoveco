<?php

declare(strict_types=1);

namespace Src\Modules\User\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Database\SchemaMysql;
use Src\Modules\User\Domain\Entity\Role;
use Src\Modules\User\Domain\Entity\User;
use Src\Modules\User\Domain\Query\UserQuery;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;

class UserRepositoryMysql extends RepositoryMysql implements UserRepositoryInterface
{
    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($pdo);
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    public function findAll(UserQuery $query): array
    {
        $select = $this->buildSelect($query);

        $qb = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_USERS);

        if ($query->withRoles) {
            $qb = $qb->joinManyToMany(
                SchemaMysql::TABLE_ROLE_USER,
                SchemaMysql::USER_ID,
                SchemaMysql::ROLE_USER_USER_ID,
                SchemaMysql::TABLE_ROLES,
                SchemaMysql::ROLE_USER_ROLE_ID,
                SchemaMysql::ROLE_ID
            );
        }

        $qb = $this->applyWhereConditions($qb, $query);

        if ($query->limit !== null) {
            $qb = $qb->limit($query->limit);
        }

        if ($query->offset !== null) {
            $qb = $qb->offset($query->offset);
        }

        $rows = $qb->executeAndFetchAll();

        return $this->hydrateCollection($rows, $query);
    }

    public function findOne(UserQuery $query): ?User
    {
        $select = $this->buildSelect($query);

        $qb = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_USERS);

        if ($query->withRoles) {
            $qb = $qb->joinManyToMany(
                SchemaMysql::TABLE_ROLE_USER,
                SchemaMysql::USER_ID,
                SchemaMysql::ROLE_USER_USER_ID,
                SchemaMysql::TABLE_ROLES,
                SchemaMysql::ROLE_USER_ROLE_ID,
                SchemaMysql::ROLE_ID
            );
        }

        $qb = $this->applyWhereConditions($qb, $query);

        $row = $qb->executeAndFetchOne();

        return $row === null ? null : $this->hydrateOne($row, $query);
    }

    public function save(User $user): User
    {
        $data = [
            SchemaMysql::USER_EMAIL         => $user->email,
            SchemaMysql::USER_PASSWORD_HASH => $user->passwordHashed,
        ];

        if ($user->id) {
            $this->queryBuilder->update(
                SchemaMysql::TABLE_USERS,
                $data,
                SchemaMysql::USER_ID . ' = :id',
                ['id' => $user->id]
            );
        } else {
            $ok = $this->queryBuilder->insert(SchemaMysql::TABLE_USERS, $data);
            if ($ok) {
                $user->id = $this->queryBuilder->returnInsertId();
            }
        }

        $this->syncRoles($user);

        return $user;
    }

    public function updateLastLogin(int $userId): void
    {
        $this->queryBuilder->update(
            SchemaMysql::TABLE_USERS,
            [
                SchemaMysql::fieldProperty(SchemaMysql::USER_LAST_LOGIN_AT) => (new \DateTime('now', new \DateTimeZone('Europe/Brussels')))
                    ->format('Y-m-d H:i:s')
            ],
            SchemaMysql::USER_ID . ' = :id',
            ['id' => $userId]
        );
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildSelect(UserQuery $query): array
    {
        $base = $query->columns !== null
            ? array_map(fn(string $col) => $col, $query->columns)
            : $this->getSchemeAllColumnsUser();

        if ($query->withRoles) {
            $base[] = SchemaMysql::ROLE_NAME . ' AS role_name';
            $base[] = SchemaMysql::ROLE_ID   . ' AS role_id';
        }

        return $base;
    }

    private function applyWhereConditions(mixed $qb, UserQuery $query): mixed
    {
        if ($query->email !== null) {
            $qb = $qb->where(
                SchemaMysql::USER_EMAIL . ' = :email',
                [':email' => $query->email]
            );
        }

        if ($query->id !== null) {
            $qb = $qb->where(
                SchemaMysql::USER_ID . ' = :id',
                [':id' => $query->id]
            );
        }

        if ($query->isActive !== null) {
            $active = $query->isActive ? 'TRUE' : 'FALSE';
            $qb = $qb->where(SchemaMysql::USER_IS_ACTIVE . " = {$active}");
        }

        return $qb;
    }

    /**
     * Hydrate une collection — agrège les rôles par user si withRoles = true.
     *
     * @return User[]
     */
    private function hydrateCollection(array $rows, UserQuery $query): array
    {
        if (!$query->withRoles) {
            return array_map(fn(array $row) => $this->hydrateOne($row, $query), $rows);
        }

        $users = [];
        foreach ($rows as $row) {
            $userId = $row['id'];
            if (!isset($users[$userId])) {
                $users[$userId] = $this->hydrateOne($row, $query);
            }
            if (isset($row['role_name'])) {
                $role = new Role($row['role_name']);
                $role->id =  (int) $row['role_id'];
                $users[$userId]->addRole($role);
            }
        }

        return array_values($users);
    }

    private function hydrateOne(array $row, UserQuery $query): User
    {
        $user = new User($row['email']);
        $user->id             = (int) $row['id'];
        $user->passwordHashed = $row['password_hash'];

        if ($query->withRoles && isset($row['role_name'])) {
            $role = new Role($row['role_name']);
            $role->id = (int) $row['role_id'];
            $user->addRole($role);
        }

        return $user;
    }

    private function syncRoles(User $user): void
    {
        $roles = $user->getRoles();
        if (empty($roles)) {
            return;
        }

        $roleId = $this->queryBuilder
            ->select([SchemaMysql::ROLE_ID])
            ->from(SchemaMysql::TABLE_ROLES)
            ->where(
                SchemaMysql::ROLE_NAME . ' = :name',
                ['name' => $roles[0]->name]
            )
            ->executeAndFetchColumn();

        if (!$roleId) {
            throw new \RuntimeException("Role '{$roles[0]->name}' inexistant");
        }

        $exists = (bool) $this->queryBuilder
            ->select(['COUNT(*)'])
            ->from(SchemaMysql::TABLE_ROLE_USER)
            ->where('user_id = :uid AND role_id = :rid', [
                'uid' => $user->id,
                'rid' => $roleId,
            ])
            ->executeAndFetchColumn();

        if (!$exists) {
            $this->queryBuilder->insert(SchemaMysql::TABLE_ROLE_USER, [
                'user_id' => $user->id,
                'role_id' => $roleId,
            ]);
        }
    }

    private function getSchemeAllColumnsUser(): array
    {
        return [
            SchemaMysql::USER_ID,
            SchemaMysql::USER_EMAIL,
            SchemaMysql::USER_PASSWORD_HASH,
            SchemaMysql::USER_IS_ACTIVE,
            SchemaMysql::USER_CREATED_AT,
            SchemaMysql::USER_LAST_LOGIN_AT,
        ];
    }
}
