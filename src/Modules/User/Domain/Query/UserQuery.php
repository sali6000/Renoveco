<?php

declare(strict_types=1);

namespace Src\Modules\User\Domain\Query;

final class UserQuery
{
    public function __construct(
        // FILTRES MÉTIER — ce qu'on cherche
        public readonly ?int    $id       = null,
        public readonly ?string $email    = null,
        public readonly ?bool   $isActive = null,

        // RELATIONS — ce qu'on veut charger
        public readonly bool $withRoles = false,

        // PAGINATION
        public readonly ?int $limit  = null,
        public readonly ?int $offset = null,
    ) {}
}
