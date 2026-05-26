<?php

namespace Src\Modules\Shared\Domain\Service;

use Config\AppConfig;
use Core\Logger\AccessLogger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Src\Exception\ServiceException;

/**
 * Service d'envoi d'emails via SMTP.
 *
 * Encapsule PHPMailer et expose une interface simple pour l'envoi d'emails.
 * Les erreurs SMTP sont loguées et transformées en ServiceException
 * afin d'isoler les couches supérieures des détails d'implémentation PHPMailer.
 */
class MailService
{
    /**
     * Envoie un email via SMTP.
     *
     * La configuration SMTP est chargée depuis les variables d'environnement.
     * Les exceptions PHPMailer sont interceptées, loguées, puis transformées
     * en ServiceException pour ne pas exposer les détails techniques aux couches supérieures.
     *
     * @param string      $to           Adresse email du destinataire
     * @param string      $subject      Sujet de l'email
     * @param string      $body         Corps HTML de l'email
     * @param string      $altBody      Corps texte alternatif (fallback non-HTML)
     * @param string|null $replyToEmail Adresse de réponse optionnelle
     * @param string|null $replyToName  Nom associé à l'adresse de réponse
     *
     * @throws ServiceException 'MAIL_FAILED' si l'envoi SMTP échoue
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        string $altBody,
        ?string $replyToEmail = null,
        ?string $replyToName  = null,
    ): void {
        try {
            $mail = new PHPMailer(exceptions: true);

            $mail->isSMTP();
            $mail->Host       = AppConfig::getEnv('MAIL_HOST');
            $mail->Port       = AppConfig::getEnv('MAIL_PORT');
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Username   = AppConfig::getEnv('MAIL_USERNAME');
            $mail->Password   = AppConfig::getEnv('MAIL_PASSWORD');
            $mail->CharSet    = 'UTF-8';

            $mail->SMTPDebug = match (AppConfig::getEnv('MAIL_DEBUG')) {
                'SERVER' => SMTP::DEBUG_SERVER,
                'CLIENT' => SMTP::DEBUG_CLIENT,
                default  => SMTP::DEBUG_OFF,
            };

            $mail->setFrom(AppConfig::getEnv('MAIL_USERNAME'), 'Service client');

            if ($replyToEmail !== null) {
                $mail->addReplyTo($replyToEmail, $replyToName ?? '');
            }

            $mail->addAddress($to);

            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            AccessLogger::logTo($e, AccessLogger::LEVEL_ERROR, AccessLogger::CHANNEL_APP);
            throw new ServiceException(
                "L'envoi du message a échoué, veuillez réessayer plus tard.",
                'MAIL_FAILED'
            );
        }
    }
}
