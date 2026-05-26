<?php

namespace Src\Modules\Contact\Domain\Service;

use Config\AppConfig;
use Src\Exception\ServiceException;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;
use Src\Modules\Shared\Domain\Service\MailService;

/**
 * Service de gestion des messages de contact.
 *
 * Orchestre la validation du rate limit et l'envoi d'email
 * via MailService pour les formulaires de contact.
 */
class ContactService
{
    /** @var int Nombre maximum de tentatives autorisées par fenêtre */
    private const MAX_ATTEMPTS = 5;

    /** @var int Durée de la fenêtre de rate limit en minutes */
    private const WINDOW_MINUTES = 60;

    /** @var int Seuil à partir duquel un délai artificiel est appliqué */
    private const SOFT_THROTTLE_AFTER = 3;

    public function __construct(
        private MailService $mailService,
        private RateLimitRepositoryInterface $rateLimitRepo
    ) {}

    /**
     * Envoie un message de contact après vérification du rate limit.
     *
     * Le rate limit est vérifié avant l'envoi et enregistré uniquement
     * après un envoi réussi. Un soft throttle ralentit les tentatives
     * répétées sans bloquer.
     *
     * @param string|null $firstname  Prénom de l'expéditeur (optionnel)
     * @param string|null $lastname   Nom de l'expéditeur (optionnel)
     * @param string|null $company    Société de l'expéditeur (optionnel)
     * @param string      $email      Adresse email de l'expéditeur
     * @param string      $message    Corps du message
     *
     * @throws ServiceException 'RATE_LIMIT' si le nombre maximum de tentatives est atteint
     * @throws ServiceException 'MAIL_FAILED' si l'envoi SMTP échoue — propagée depuis MailService
     * @throws \PDOException si une erreur base de données survient — propagée depuis RateLimitRepo
     */
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
            throw new ServiceException("Trop de tentatives. Réessayez dans " . self::WINDOW_MINUTES . " minutes.", 'RATE_LIMIT');
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

        $this->rateLimitRepo->record('contact_send', $email);
    }
}
