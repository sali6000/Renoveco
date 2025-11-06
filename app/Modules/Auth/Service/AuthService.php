<?php

namespace App\Modules\Auth\Service;

use Core\Logger\AccessLogger;
use App\Modules\User\Repository\UserRepository;
use App\Exception\ServiceException;

class AuthService
{
    public function __construct(private UserRepository $userRepo) {}

    public function authenticate(string $email, string $password): ?array
    {
        try {
            $user = $this->userRepo->getUserByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                return [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role_name'], // à récupérer via jointure
                ];
            }
            return null;
        } catch (\Exception $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur DAO (getUserByEmail($email)): " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur est survenue dans la récupération de l'utilisateur par email (Code : $errorId).");
        }
    }
}
