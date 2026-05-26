<?php

namespace Src\Modules\Auth\Domain\Service;

use Core\Logger\AccessLogger;
use Src\Exception\ServiceException;
use Src\Exception\ValidationException;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;

class AuthService
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_MINUTES = 15;
    private const SOFT_THROTTLE_AFTER = 3;

    public function __construct(
        private UserRepositoryInterface $userRepo,
        private RateLimitRepositoryInterface $rateLimitRepo
    ) {}

    /**
     * Connection d'un utilisateur
     * 
     * @throws ServiceException si rate limit atteint
     * @throws ValidationException si identifiants incorrects.
     * @throws \PDOException si erreur base de données — propagée depuis les repo
     */
    public function loginUser(string $email, string $password): ?array
    {
        // 1. Vérifier les tentatives de connection
        $attempts = $this->rateLimitRepo->countRecent('login_fail', self::WINDOW_MINUTES);

        if ($attempts >= self::MAX_ATTEMPTS) {
            AccessLogger::logTo(
                "Rate limit atteint pour $email ($attempts tentatives)",
                AccessLogger::LEVEL_WARNING,
                AccessLogger::CHANNEL_SECURITY
            );
            throw new ServiceException("Trop de tentatives. Réessayez dans " . self::WINDOW_MINUTES . " minutes.");
        }

        // 2. Soft throttle : ralentir sans bloquer
        if ($attempts >= self::SOFT_THROTTLE_AFTER) {
            sleep(2);
        }

        // 3. Tentative de login
        $user = $this->userRepo->findForLogin($email);

        // 4. Enregistrement audit si echec
        if (!$user || !password_verify($password, $user->passwordHashed)) {
            $this->rateLimitRepo->record('login_fail', $email);
            AccessLogger::logTo(
                "Échec login pour $email",
                AccessLogger::LEVEL_WARNING,
                AccessLogger::CHANNEL_SECURITY
            );
            throw new ValidationException("Identifiants incorrects.");
        }

        // 5. Authentification réussie
        return [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->getRoles()[0]->name
        ];
    }

    public function updateUserLastLogin(int $userId): void
    {
        $this->userRepo->updateLastLogin($userId);
    }
}
