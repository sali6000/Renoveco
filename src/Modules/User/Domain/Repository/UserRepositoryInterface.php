<?php

namespace Src\Modules\User\Domain\Repository;

use Src\Modules\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findAllForAdmin(): array;
    public function findForLogin(string $email): ?User;
    public function save(User $user): User;
    public function updateLastLogin(int $userId): void;
}
