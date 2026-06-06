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

    // CONSTRUCTOR
    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($pdo);
    }


    // PREPARE QUERY
    private function buildQuery(UserQuery $q, array $columns = self::USER_COLUMNS, array $relations = []): QueryBuilderInterface
    {
        // GET SELECT (from USER_COLUMNS + from each relations)
        foreach ($relations as $relation) {
            $columns = array_merge($columns, $relation->getColumns());
        }

        // PREPARE SELECTS
        // PREPARE FROM (table USER)
        $query = $this->queryBuilder
            ->select($columns)
            ->from(SchemaMysql::TABLE_USERS);

        // PREPARE JOINS FOREACH RELATIONS
        foreach ($relations as $relation) {
            $query = $relation->applyJoin($query);
        }

        // PREPARE FILTERS
        return $this->applyFilters($query, $q);
    }

    // PREPARE CONDITIONS (where)
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

    // FIND ALL : USER
    public function findAll(UserQuery $q): array
    {
        // SELECT COLUMNS (from user and relations)
        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, self::USER_COLUMNS, $relations);

        // ADD (limit)
        if ($q->limit !== null) {
            $query = $query->limit($q->limit);
        }

        // ADD (offset)
        if ($q->offset !== null) {
            $query = $query->offset($q->offset);
        }

        // EXECUTION (return rows[])
        $rows = $query->executeAndFetchAll();

        // RETURN : HYDRATATION (Entities[] <- rows[] & relations)
        return $this->hydrateMany($rows, $relations);
    }

    // FIND ONE : USER
    public function findOne(UserQuery $q): ?User
    {
        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, self::USER_COLUMNS, $relations);

        // GROUP CONDITION (MANYTOMANY ?)
        if (!empty($relations)) {

            // EXECUTE (=> many rows)
            $rows = $query->executeAndFetchAll();

            // HYDRATE (Entities[] <- rows[]) AND RETURN 1ST RESULT
            $users = $this->hydrateMany($rows, $relations);
            return $users[0] ?? null;
        }

        // EXCUTE (=> one row)
        $row = $query->executeAndFetchOne();

        // TRANSFORM (Entity <- row) AND RETURN RESULT
        return $row ? User::fromArray($row) : null;
    }


    // FIND ONE : USER
    public function findOneForAuth(UserQuery $q): ?User
    {

        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, self::USER_COLUMNS_WITH_CREDENTIALS, $relations);

        // GROUP CONDITION (MANYTOMANY ?)
        if (!empty($relations)) {

            // EXECUTE (=> many rows)
            $rows = $query->executeAndFetchAll();

            // HYDRATE (Entities[] <- rows[]) AND RETURN 1ST RESULT
            $users = $this->hydrateMany($rows, $relations);
            return $users[0] ?? null;
        }

        // EXCUTE (=> one row)
        $row = $query->executeAndFetchOne();

        // TRANSFORM (Entity <- row) AND RETURN RESULT
        return $row ? User::fromArray($row) : null;
    }

    // GET RELATIONS (from UserQuery)
    private function resolveRelations(UserQuery $q): array
    {
        $relations = [];

        // ROLES
        if ($q->withRoles) {
            $relations[] = $this->makeRolesRelation();
        }

        // Demain :
        // if ($q->withPermissions) $relations[] = $this->makePermissionsRelation();
        // if ($q->withGroups)      $relations[] = $this->makeGroupsRelation();

        return $relations;
    }

    /**
     * Transforme plusieurs lignes SQL en users uniques.
     * Regroupe les rôles par user pour éviter les doublons.
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


    /** 
     * Make Roles Relation
     * 
     * @return ManyToManyRelation
     * 
     * */
    private function makeRolesRelation(): ManyToManyRelation
    {
        return new ManyToManyRelation(

            // SET KEY FOR USER (ex: User['Roles'][...])
            key: SchemaMysql::fieldProperty(SchemaMysql::TABLE_ROLES),

            // SET COLUMNS FROM SELF::ROLE_COLUMNS
            relationColumns: array_map(
                fn(string $col) => SchemaMysql::fieldProperty($col),
                self::ROLE_COLUMNS
            ),

            // SET PREFIX FOR ROLE COLUMNS
            relationPrefix: SchemaMysql::ROLE_RELATION_PREFIX,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: SchemaMysql::TABLE_ROLES,
            foreignKey: SchemaMysql::ROLE_ID,
            localKey: SchemaMysql::USER_ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: SchemaMysql::TABLE_ROLE_USER,
            pivotLocalKey: SchemaMysql::ROLE_USER_USER_ID,
            pivotForeignKey: SchemaMysql::ROLE_USER_ROLE_ID,
        );
    }

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
