<?php
// SendContactMessage.php

namespace Src\Modules\Contact\Application\UseCase;

use Config\RateLimitConfig;
use Src\Exception\Application\RateLimitException;
use Src\Exception\Domain\DomainExceptionInterface;
use Src\Modules\Contact\Application\Service\ContactService;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;

final class SendContactMessageUseCase
{
    public function __construct(
        private readonly ContactService $contactService,
        private readonly RateLimitRepositoryInterface $rateLimitRepo
    ) {}

    public function execute(
        ?string $firstname,
        ?string $lastname,
        ?string $company,
        string $email,
        string $message,
        string $ip
    ): ResultUseCase {

        // Récupérer les tentatives de contact
        $attempts = $this->rateLimitRepo->countRecent('contact_send_fail_' . $ip, RateLimitConfig::AUTH_LOGIN['window_minutes']);

        // Bloquer si tentatives max atteinte
        if ($attempts >= RateLimitConfig::AUTH_LOGIN['max_attempts']) {
            throw new RateLimitException("Trop de tentatives. Réessayez dans " . RateLimitConfig::AUTH_LOGIN['window_minutes'] . " minutes.", 'RATE_LIMIT');
        }

        // Soft throttle : ralentir sans bloquer
        if ($attempts >= RateLimitConfig::AUTH_LOGIN['soft_throttle_after']) {
            sleep(2);
        }

        try {

            // Envoie d'un message
            $this->contactService->send($firstname, $lastname, $company, $email, $message);

            // Retourner le résultat en cas de succès
            return ResultUseCase::success();
        } catch (DomainExceptionInterface $e) {

            // Enregistrement en DB de l'action effectué par l'IP
            $this->rateLimitRepo->record('contact_send_fail_' . $ip, $email);

            // Retourner le résultat avec message d'erreur en cas d'échec
            return ResultUseCase::failure($e->getMessage(), $e->getErrorCode());
        }
    }
}
