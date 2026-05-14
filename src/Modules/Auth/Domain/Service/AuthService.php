<?php

namespace Src\Modules\Auth\Domain\Service;

use Core\Logger\AccessLogger;
use Src\Exception\ServiceException;
use Src\Exception\ValidationException;
use Src\Modules\Auth\Domain\Repository\LoginAttemptRepositoryInterface;
use Src\Modules\User\Domain\Repository\UserRepositoryInterface;

class AuthService
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_MINUTES = 15;
    private const SOFT_THROTTLE_AFTER = 3;

    public function __construct(
        private UserRepositoryInterface $userRepo,
        private LoginAttemptRepositoryInterface $loginAttemptRepo
    ) {}

    public function loginUser(string $email, string $password, string $ip): ?array
    {
        try {

            // 1. Vérifier le rate limit
            // countRecent ne compte que les échecs (success = 0)
            $attempts = $this->loginAttemptRepo->countRecent($ip, self::WINDOW_MINUTES);

            if ($attempts >= self::MAX_ATTEMPTS) {
                throw new ServiceException("Trop de tentatives. Réessayez dans " . self::WINDOW_MINUTES . " minutes.");
            }

            // 2. Soft throttle : ralentir sans bloquer
            if ($attempts >= self::SOFT_THROTTLE_AFTER) {
                sleep(2);
            }

            // 3. Tentative de login
            $user = $this->userRepo->findForLogin($email);

            // 4. Enregistrement
            if (!$user || !password_verify($password, $user->passwordHashed)) {
                $this->loginAttemptRepo->record($ip, $email, null, false);
                throw new ValidationException("Identifiants incorrects.");
            }

            $this->loginAttemptRepo->record($ip, $email, $user->id, true);

            return [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->getRoles()[0]->name
            ];
        } catch (ValidationException | ServiceException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur: " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur technique est survenue lors de la connection, veuillez contacter l'administrateur.");
        }
    }

    public function updateUserLastLogin(int $userId): void
    {
        $this->userRepo->updateLastLogin($userId);
    }
}
