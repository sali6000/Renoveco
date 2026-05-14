<?php


namespace Src\Modules\Auth\Domain\Repository;

interface LoginAttemptRepositoryInterface
{
    public function countRecent(string $ip, int $minutes): int;
    public function record(string $ip, string $email, ?int $userId, bool $success): void;
}
