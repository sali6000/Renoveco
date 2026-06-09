<?php

namespace Src\Modules\User\Infrastructure\Schema;

final class RoleSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 ROLE
    // -------------------------------------------------------
    public const TABLE = 'role rol';
    public const ID = 'rol.id';
    public const NAME = 'rol.name';
    public const IS_ACTIVE = 'rol.is_active';
}
