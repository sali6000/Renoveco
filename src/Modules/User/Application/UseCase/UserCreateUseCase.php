<?php

namespace Src\Modules\User\Application\UseCase;

use Config\RateLimitConfig;
use Src\Exception\Application\RateLimitException;
use Src\Exception\Domain\DomainExceptionInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;
use Src\Modules\User\Application\Service\UserService;

final class UserCreateUseCase
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RateLimitRepositoryInterface $rateLimitRepo
    ) {}

    public function execute(string $email, string $password, string $ip): ResultUseCase
    {

        // Récupérer les tentatives de contact
        $attempts = $this->rateLimitRepo->countRecent('user_create_' . $ip, RateLimitConfig::USER_CREATE['window_minutes']);

        // Bloquer si tentatives max atteinte
        if ($attempts >= RateLimitConfig::USER_CREATE['max_attempts']) {
            throw new RateLimitException("Trop de tentatives. Réessayez dans " . RateLimitConfig::USER_CREATE['window_minutes'] . " minutes.", 'RATE_LIMIT');
        }

        // Soft throttle : ralentir sans bloquer
        if ($attempts >= RateLimitConfig::USER_CREATE['soft_throttle_after']) {
            sleep(2);
        }

        try {
            // Création de l'user
            $user = $this->userService->createUser($email, $password);

            // Retourner le résultat en cas de succès
            return ResultUseCase::success();
        } catch (DomainExceptionInterface $e) {

            // Retourner le résultat avec message d'erreur en cas d'échec
            return ResultUseCase::failure($e->getMessage(), $e->getErrorCode());
        } finally {

            // Enregistrement en DB de l'action effectué par l'IP
            $this->rateLimitRepo->record('user_create_' . $ip, $email);
        }
    }
}
