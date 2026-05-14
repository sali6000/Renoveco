<?php

namespace Src\Modules\Shared\Domain\Service;

use Config\AppConfig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Src\Exception\ServiceException;

class MailService
{
    public function send(
        string $to,
        string $subject,
        string $body,
        string $altBody,
        ?string $replyToEmail = null,
        ?string $replyToName  = null,
    ): void {
        try {
            $mail = new PHPMailer(true);

            // --- SMTP config ---
            $mail->isSMTP();
            $mail->Host       = AppConfig::getEnv('MAIL_HOST');
            $mail->Port       = AppConfig::getEnv('MAIL_PORT');
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Username   = AppConfig::getEnv('MAIL_USERNAME');
            $mail->Password   = AppConfig::getEnv('MAIL_PASSWORD');
            $mail->CharSet    = 'UTF-8';

            // --- Debug selon env ---
            $mail->SMTPDebug = match (AppConfig::getEnv('MAIL_DEBUG')) {
                'SERVER' => SMTP::DEBUG_SERVER,
                'CLIENT' => SMTP::DEBUG_CLIENT,
                default  => SMTP::DEBUG_OFF,
            };

            // --- Expéditeur ---
            $mail->setFrom(AppConfig::getEnv('MAIL_USERNAME'), 'Service client');

            // --- Reply-To optionnel ---
            if ($replyToEmail !== null) {
                $mail->addReplyTo($replyToEmail, $replyToName ?? '');
            }

            // --- Destinataire ---
            $mail->addAddress($to);

            // --- Contenu ---
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();
        } catch (\Throwable $e) {
            throw new ServiceException("L'envoi du message a échoué, veuillez réessayer plus tard.");
        }
    }
}
