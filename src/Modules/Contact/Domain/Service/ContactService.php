<?php

namespace Src\Modules\Contact\Domain\Service;

use Config\AppConfig;
use Src\Exception\ServiceException;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;
use Src\Modules\Shared\Domain\Service\MailService;

class ContactService
{
    private const MAX_ATTEMPTS = 3;
    private const WINDOW_MINUTES = 60;
    private const SOFT_THROTTLE_AFTER = 3;

    public function __construct(
        private MailService $mailService,
        private RateLimitRepositoryInterface $rateLimitRepo
    ) {}

    public function send(
        ?string $firstname,
        ?string $lastname,
        ?string $company,
        string $email,
        string $message
    ): void {

        // 1. Vérifier les tentatives de connection
        $attempts = $this->rateLimitRepo->countRecent('contact_send', self::WINDOW_MINUTES);

        if ($attempts >= self::MAX_ATTEMPTS) {
            throw new ServiceException("Trop de tentatives. Réessayez dans " . self::WINDOW_MINUTES . " minutes.");
        }

        // 2. Soft throttle : ralentir sans bloquer
        if ($attempts >= self::SOFT_THROTTLE_AFTER) {
            sleep(2);
        }

        $senderName  = trim(($firstname ?? '') . ' ' . ($lastname ?? '')) ?: 'Anonyme';
        $companyLine = $company ? "<strong>Société :</strong> " . htmlspecialchars($company) . "<br>" : '';

        $body = "
        <strong>De :</strong> " . htmlspecialchars($senderName) . "<br>
        <strong>Email :</strong> " . htmlspecialchars($email) . "<br>
        {$companyLine}
        <strong>Date :</strong> " . date('d/m/Y H:i:s') . "<br><br>
        <strong>Message :</strong><br>
        " . nl2br(htmlspecialchars($message)) . "
    ";

        $altBody = "De : {$senderName}\nEmail : {$email}\nDate : " . date('d/m/Y H:i:s') . "\n\nMessage :\n{$message}";

        $this->mailService->send(
            to: AppConfig::getEnv('MAIL_USERNAME'),
            subject: AppConfig::getEnv('MAIL_SUBJECT'),
            body: $body,
            altBody: $altBody,
            replyToEmail: $email,
            replyToName: $senderName,
        );

        // 4. Enregistrer la tentative APRÈS envoi réussi
        $this->rateLimitRepo->record('contact_send', $email);
    }
}
