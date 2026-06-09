<?php

namespace Src\Modules\Shared\Infrastructure\Schema;

final class RateLimitSchemaMysql
{
    // -------------------------------------------------------
    // 🧩 RATE LIMIT ATTEMPTS (📋 Limite de tentatives)
    // -------------------------------------------------------
    public const TABLE = 'rate_limit_attempt rla';
    public const ID         = 'rla.id';
    public const TYPE       = 'rla.type';
    public const IP         = 'rla.ip_address';
    public const IDENTIFIER = 'rla.identifier';
    public const AT         = 'rla.attempted_at';
}
