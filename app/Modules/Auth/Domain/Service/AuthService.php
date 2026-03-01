<?php

namespace App\Modules\Auth\Domain\Service;

use Core\Logger\AccessLogger;
use App\Exception\ServiceException;
use App\Exception\ValidationException;
use App\Modules\User\Domain\Repository\UserRepositoryInterface;

class AuthService
{
    public function __construct(private UserRepositoryInterface $userRepo) {}

    public function loginUser(string $email, string $password): ?array
    {
        try {

            $user = $this->userRepo->findForLogin($email);

            if (!$user || !password_verify($password, $user->passwordHashed)) {
                throw new ValidationException("Identifiants incorrects.");
            }

            return [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->getRoles()[0]->name
            ];
        } catch (ValidationException $e) {
            // Laisser les erreurs de validation remonter pour que le contrôleur les gère
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
