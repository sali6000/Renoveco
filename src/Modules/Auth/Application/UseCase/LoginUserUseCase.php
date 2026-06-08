<?php

namespace Src\Modules\Auth\Application\UseCase;

use Config\RateLimitConfig;
use Core\Support\DebugHelper;
use Src\Exception\Application\ApplicationExceptionInterface;
use Src\Exception\Application\RateLimitException;
use Src\Exception\Domain\DomainExceptionInterface;
use Src\Modules\Auth\Application\Service\AuthService;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;

final class LoginUserUseCase
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly SessionManager $session,
        private readonly RateLimitRepositoryInterface $rateLimitRepo
    ) {}

    /**
     * Authentifie un utilisateur et ouvre sa session.
     *
     * @param string $email    Adresse email de l'utilisateur
     * @param string $password Mot de passe en clair
     *
     */
    public function execute(string $email, string $password, string $ip): ResultUseCase
    {
        // Récupérer les tentatives de contact
        $attempts = $this->rateLimitRepo->countRecent('user_login_' . $ip, RateLimitConfig::AUTH_LOGIN['window_minutes']);

        // Bloquer si tentatives max atteinte
        if ($attempts >= RateLimitConfig::AUTH_LOGIN['max_attempts']) {
            throw new RateLimitException("Trop de tentatives. Réessayez dans " . RateLimitConfig::AUTH_LOGIN['window_minutes'] . " minutes.", 'RATE_LIMIT');
        }

        // Soft throttle : ralentir sans bloquer
        if ($attempts >= RateLimitConfig::AUTH_LOGIN['soft_throttle_after']) {
            sleep(2);
        }

        try {
            // Vérification du mail et mot de passe
            $user = $this->authService->loginUser($email, $password);
            $this->authService->updateUserLastLogin($user['id']);

            // Création de la session utilisateur
            $this->session->openAuthenticatedSession($user);

            // Retourner le résultat en cas de succès
            return ResultUseCase::success();
        } catch (ApplicationExceptionInterface | DomainExceptionInterface $e) {

            // Retourner le résultat avec message d'erreur en cas d'échec
            return ResultUseCase::failure($e->getMessage(), $e->getErrorCode());
        } finally {

            // Enregistrement en DB de l'action effectué par l'IP
            $this->rateLimitRepo->record('user_login_' . $ip, $email);
        }
    }
}
