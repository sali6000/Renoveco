<?php

namespace Src\Modules\User\Infrastructure\Schema;

final class UserSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 USER
    // -------------------------------------------------------
    public const TABLE = 'user usr';
    public const ID = 'usr.id';
    public const EMAIL = 'usr.email';
    public const PASSWORD_HASH = 'usr.password_hash';
    public const CREATED_AT = 'usr.created_at';
    public const LAST_LOGIN_AT = 'usr.last_login_at';
    public const EMAIL_VERIFIED_AT = 'usr.email_verified_at';
    public const DELETED_AT = 'usr.deleted_at';
    public const IS_ACTIVE = 'usr.is_active';
}
