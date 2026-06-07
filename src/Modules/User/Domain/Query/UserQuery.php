<?php

declare(strict_types=1);

namespace Src\Modules\User\Domain\Query;

final class UserQuery
{
    public function __construct(
        // FILTRES MÉTIER
        public readonly ?int    $id       = null,
        public readonly ?string $email    = null,
        public readonly ?bool   $isActive = null,

        // RELATIONS
        public readonly bool $withRoles = false,

        // PAGINATION
        public readonly ?int $limit  = null,
        public readonly ?int $offset = null,
    ) {}
}
