<?php
// SendContactMessage.php

namespace Src\Modules\Contact\Application\UseCase;

use Src\Exception\DomainExceptionInterface;
use Src\Modules\Contact\Domain\Service\ContactService;
use Src\Modules\Shared\Application\UseCase\UseCaseResult;

final class SendContactMessage
{
    public function __construct(private readonly ContactService $contactService) {}

    public function execute(
        ?string $firstname,
        ?string $lastname,
        ?string $company,
        string $email,
        string $message
    ): UseCaseResult {
        try {
            // Envoie d'un message
            $this->contactService->send($firstname, $lastname, $company, $email, $message);

            // Retourner le résultat en cas de succès
            return UseCaseResult::success();
        } catch (DomainExceptionInterface $e) {

            // Retourner le résultat avec message d'erreur en cas d'échec
            return UseCaseResult::failure($e->getMessage(), $e->getErrorCode());
        }
    }
}
