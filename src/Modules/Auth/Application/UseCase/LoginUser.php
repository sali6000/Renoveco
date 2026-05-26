<?php

namespace Src\Modules\Auth\Application\UseCase;

use Src\Exception\DomainExceptionInterface;
use Src\Modules\Auth\Domain\Service\AuthService;
use Src\Modules\Shared\Application\UseCase\UseCaseResult;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;

final class LoginUser
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly SessionManager $session,
    ) {}

    /**
     * Authentifie un utilisateur et ouvre sa session.
     *
     * @param string $email    Adresse email de l'utilisateur
     * @param string $password Mot de passe en clair
     *
     */
    public function execute(string $email, string $password): UseCaseResult
    {
        try {
            // Vérification du mail et mot de passe
            $user = $this->authService->loginUser($email, $password);
            $this->authService->updateUserLastLogin($user['id']);

            // Création de la session utilisateur
            $this->session->openAuthenticatedSession($user);

            // Retourner le résultat en cas de succès
            return UseCaseResult::success();
        } catch (DomainExceptionInterface $e) {

            // Retourner le résultat avec message d'erreur en cas d'échec
            return UseCaseResult::failure($e->getMessage(), $e->getErrorCode());
        }
    }
}
