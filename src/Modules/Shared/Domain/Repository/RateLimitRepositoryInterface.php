<?php

namespace Src\Modules\Shared\Domain\Repository;

interface RateLimitRepositoryInterface
{
    public function countRecent(string $type, int $minutes): int;
    public function record(string $type, ?string $identifier): void;
}
