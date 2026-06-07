<?php

declare(strict_types=1);

namespace Src\Modules\User\Infrastructure\Persistence\Mysql;

use Config\AppConfig;
use Core\Database\QueryBuilderInterface;
use Core\Database\Relations\ManyToManyRelation;
use Core\Database\RepositoryMysql;
use Src\Database\SchemaMysql;
use Src\Exception\Domain\RoleNotFoundException;
use Src\Modules\User\Domain\Entity\User;
use Src\Modules\User\Domain\Query\UserQuery;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;

/**
 * 
 * findAll + findOne + findOneForAuth — points d'entrée publics
 * executeFindOne + executeFindAll — execution de la requête
 * buildQuery + applyFilters + resolveRelations — construction de la requête
 * hydrateMany + makeRolesRelation — hydratation
 * syncRoles + save + updateLastLogin — écriture
 * 
 * Créer une nouvelle relation implique de modifier/créer:
 * - 1. const RELATION_COLUMNS (récupérer les colonnes pour cette relation)
 * - 2. makeRolesRelation (créer la jointure pour cette relation)
 * - 3. resolveRelations (hydrater cette relation dans User)
 * 
 **/
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

    //----------------------------------------------------------------------------
    // CONSTRUCTOR
    //----------------------------------------------------------------------------

    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($pdo);
    }

    //----------------------------------------------------------------------------
    // QUERIES SELECT :
    //----------------------------------------------------------------------------

    // SELECT : FIND ONE 
    public function findOne(UserQuery $q): ?User
    {
        return $this->executeFindOne($q, self::USER_COLUMNS);
    }

    // SELECT : FIND ALL
    public function findAll(UserQuery $q): array
    {
        return $this->executeMany($q, self::USER_COLUMNS);
    }

    // SELECT : FIND ONE (FOR AUTH SERVICE)
    public function findOneForAuth(UserQuery $q): ?User
    {
        return $this->executeFindOne($q, self::USER_COLUMNS_WITH_CREDENTIALS);
    }

    //----------------------------------------------------------------------------
    // QUERIES EXECUTE :
    //----------------------------------------------------------------------------

    // EXECUTE : FIND ONE
    private function executeFindOne(UserQuery $q, array $columns): ?User
    {
        // SELECT COLUMNS = $columns + relations params (ex: $q->withRoles, etc...)
        // FROM USER
        // WHERE (ex: $q->param !=== null)
        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, $columns, $relations);

        // IF RELATIONS (GROUPED <- rows)
        if (!empty($relations)) {

            // EXECUTION (return rows[])
            $rows = $query->executeAndFetchAll();

            // HYDRATATION (Entities['roles'] <- rows['role1'], rows['role2']) (> 1 result)
            $users = $this->hydrateMany($rows, $relations);

            // GET ENTITY FROM ARRAY (Entity['roles'] <- Entities['roles']) (1 result)
            $user = $users[0] ?? null;

            // RETURN ENTITY
            return $user;
        }

        // EXECUTION (return row)
        $row = $query->executeAndFetchOne();

        // HYDRATATION (Entity <- row)
        $user = $row ? User::fromArray($row) : null;

        // RETURN ENTITY
        return $user;
    }

    // EXECUTE : FIND ALL
    public function executeMany(UserQuery $q, array $columns): array
    {
        // SELECT COLUMNS = $columns + $relations (ex: $q->withRoles, etc...)
        // FROM USER
        // WHERE (ex: $q->email !=== null)
        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, $columns, $relations);

        // ADD <= LIMIT, OFFSET, ...
        if ($q->limit !== null) $query = $query->limit($q->limit);
        if ($q->offset !== null) $query = $query->offset($q->offset);

        // EXECUTION (return rows[])
        $rows = $query->executeAndFetchAll();

        // HYDRATATION (Entities['roles'] <- rows['role1'], rows['role2']) (> 1 result)
        $entities = $this->hydrateMany($rows, $relations);

        // RETURN ENTITIES
        return $entities;
    }

    //----------------------------------------------------------------------------
    // QUERY MAKER :
    //----------------------------------------------------------------------------

    // PREPARE QUERY
    private function buildQuery(UserQuery $q, array $columns = self::USER_COLUMNS, array $relations = []): QueryBuilderInterface
    {
        // GET COLUMNS (from $columns and $relations)
        foreach ($relations as $relation) {
            $columns = array_merge($columns, $relation->getColumns());
        }

        // PREPARE QUERY <= SELECT ... FROM ...
        $query = $this->queryBuilder
            ->select($columns)
            ->from(SchemaMysql::TABLE_USERS);

        // ADD TO QUERY <= JOINS ... (for each $relations)
        foreach ($relations as $relation) {
            $query = $relation->applyJoin($query);
        }

        // ADD TO QUERY <= WHERE, LIMIT, OFFSET,... (filter)
        return $this->applyFilters($query, $q);
    }

    // PREPARE FILTERS (conditions)
    private function applyFilters(QueryBuilderInterface $query, UserQuery $queryParams): QueryBuilderInterface
    {
        // EMAIL
        if ($queryParams->email !== null) {
            $query = $query->where(SchemaMysql::USER_EMAIL . ' = :email', [':email' => $queryParams->email]);
        }

        // ID
        if ($queryParams->id !== null) {
            $query = $query->where(SchemaMysql::USER_ID . ' = :id', [':id' => $queryParams->id]);
        }

        // IS ACTIVE
        if ($queryParams->isActive !== null) {
            $active = $queryParams->isActive ? 'TRUE' : 'FALSE';
            $query = $query->where(SchemaMysql::USER_IS_ACTIVE . " = {$active}");
        }

        return $query;
    }

    //----------------------------------------------------------------------------
    // RELATIONS MAKER :
    //----------------------------------------------------------------------------

    // CONDITIONS JOINS (Depend of UserQuery params)
    private function resolveRelations(UserQuery $q): array
    {
        $relations = [];

        // ROLES
        if ($q->withRoles) $relations[] = $this->makeRolesRelation();

        // Demain :
        // if ($q->withPermissions) $relations[] = $this->makePermissionsRelation();
        // if ($q->withGroups)      $relations[] = $this->makeGroupsRelation();

        return $relations;
    }

    /** 
     * Get columns from roles
     * 
     * @return ManyToManyRelation
     * 
     * */
    private function makeRolesRelation(array $columns = self::ROLE_COLUMNS): ManyToManyRelation
    {
        return SchemaMysql::userRolesRelation($columns);
    }

    /**
     * Transforme plusieurs lignes SQL en users uniques.
     *
     * Exemple :
     *
     * Entrée — 1 user avec 2 rôles = 2 lignes SQL :
     * 
     * <code>
     * ['id' => 1, 'email' => 'a@b.com', 'role_id' => 1, 'role_name' => 'admin']
     * 
     * ['id' => 1, 'email' => 'a@b.com', 'role_id' => 2, 'role_name' => 'editor']
     * </code>
     *
     * Sortie — 1 user avec ses rôles groupés :
     * 
     * <code>
     * ['id' => 1, 'email' => 'a@b.com', 'roles' => [['id' => 1, ...], ['id' => 2, ...]]]
     * </code>
     *
     * @return User[]
     */
    private function hydrateMany(array $rows, array $relations): array
    {
        // RETURN (without relations)
        if (empty($relations)) {
            return array_map(fn(array $row) => User::fromArray($row), $rows);
        }

        // HYDRATE (with relations)
        foreach ($relations as $relation) {
            $rows = $relation->hydrate($rows);
        }

        return array_map(fn(array $row) => User::fromArray($row), $rows);
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

    // Ajoute le lien user-role en base si le rôle n'existe pas déjà.
    private function syncRoles(User $user): void
    {
        $roles = $user->roles;
        if (empty($roles)) {
            return;
        }

        // Itère sur TOUS les rôles
        foreach ($user->roles as $role) {
            $roleId = $this->queryBuilder
                ->select([SchemaMysql::ROLE_ID])
                ->from(SchemaMysql::TABLE_ROLES)
                ->where(SchemaMysql::ROLE_NAME . ' = :name', [':name' => $role->name])
                ->executeAndFetchColumn();

            if (!$roleId) {
                throw new RoleNotFoundException("Role '{$role->name}' inexistant");
            }

            $exists = (bool) $this->queryBuilder
                ->select(['COUNT(*)'])
                ->from(SchemaMysql::TABLE_ROLE_USER)
                ->where('user_id = :uid AND role_id = :rid', [
                    ':uid' => $user->id,
                    ':rid' => $roleId,
                ])
                ->executeAndFetchColumn();

            if (!$exists) {
                $this->queryBuilder->insert(SchemaMysql::TABLE_ROLE_USER, [
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    // Met à jour la date de dernière connexion pour un user.
    public function updateLastLogin(int $userId): void
    {
        $this->queryBuilder->update(
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
