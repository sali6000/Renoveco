<?php

namespace App\Modules\User\Domain\Repository;

use App\Modules\User\Domain\Entity\User;

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
