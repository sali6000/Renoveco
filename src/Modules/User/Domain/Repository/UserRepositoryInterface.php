<?php

declare(strict_types=1);

namespace Src\Modules\User\Domain\Repository;

use Src\Modules\User\Domain\Entity\User;
use Src\Modules\User\Domain\Query\UserQuery;

interface UserRepositoryInterface
{
    /** @return User[] Récupérer tous les users */
    public function findAll(UserQuery $query): array;

    /** @return User Récupérer un user */
    public function findOne(UserQuery $query): ?User;

    /** @return User Récupérer un user avec credentials */
    public function findOneForAuth(UserQuery $query): ?User;

    public function save(User $user): User;

    public function updateLastLogin(int $userId): void;
}
