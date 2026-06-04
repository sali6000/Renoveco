<?php

declare(strict_types=1);

namespace Src\Modules\User\Domain\Query;

final class UserQuery
{
    public function __construct(
        public readonly bool $withRoles = true,
        public readonly ?array $columns = null,
        public readonly ?string $email = null,
        public readonly ?int $id = null,
        public readonly ?bool $isActive = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {}
}
