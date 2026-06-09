<?php

namespace Src\Modules\User\Infrastructure\Schema;

use Core\Database\Relations\ManyToManyRelation;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;

final class UserRoleSchemaMysql extends HelperSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 ROLE : USER
    // -------------------------------------------------------
    public const TABLE = 'role_user roluse';
    public const USER_ID = 'roluse.user_id';
    public const ROLE_ID = 'roluse.role_id';
    private const RELATION_PREFIX = 'role_';

    public static function userRolesRelation(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // SET KEY ARRAY (ex: User['roles'][...])
            key: self::fieldTable(RoleSchemaMysql::TABLE),

            // SET COLUMNS TO GET (ex: self::ROLE_COLUMNS_MINIMAL)
            relationColumns: $columns,

            // SET PREFIX FOR ROLE COLUMNS
            relationPrefix: self::RELATION_PREFIX,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: RoleSchemaMysql::TABLE,
            foreignKey: RoleSchemaMysql::ID,
            localKey: UserSchemaMysql::ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: self::TABLE,
            pivotLocalKey: self::USER_ID,
            pivotForeignKey: self::ROLE_ID,
        );
    }
}
