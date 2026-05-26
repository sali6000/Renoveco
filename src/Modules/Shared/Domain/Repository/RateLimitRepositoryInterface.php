<?php

namespace Src\Modules\Shared\Domain\Repository;

interface RateLimitRepositoryInterface
{
    /**
     * Retourne le nombre de tentatives effectuées sur l'action par la même IP et dans la limite de temps indiquée
     * 
     * @throws \PDOException        si erreur base de données
     */
    public function countRecent(string $type, int $minutes): int;

    /**
     * Enregistre l'action effectuée par tel identifiant
     * 
     * @throws \PDOException        si erreur base de données
     */
    public function record(string $type, ?string $identifier): void;
}
