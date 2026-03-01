<?php

namespace App\Modules\Contact\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use Config\AppConfig;
use Core\Routing\Attribute\Route;
use PHPMailer\PHPMailer\PHPMailer;
use Dotenv\Dotenv;

#[Route('/contact')]
class ContactIndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct('Contact');
    }

    /**
     * Affiche la page par défaut
     */
    #[Route('', methods: ['GET'])]
    public function index()
    {
        $this->render();
    }

    #[Route('mailSend', methods: ['POST'])]
    public function mailSend()
    {
        $firstname_dest = $_POST['firstname'];
        $lastname_dest = $_POST['lastname'];
        $message_dest = $_POST['message'];

        //Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        $headers = "From: " . AppConfig::getEnv('MAIL_USERNAME') . "\r\n";
        $headers .= "Reply-To: " . AppConfig::getEnv('MAIL_USERNAME') . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        // Pour avoir un retour d'information lors de l'envoi de mail => 

        switch (AppConfig::getEnv('MAIL_DEBUG')) {
            case 'SERVER':
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                break;
            case 'CLIENT':
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_CLIENT;
                break;
            default:
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
        }

        //Set the hostname of the mail server
        $mail->Host = AppConfig::getEnv('MAIL_HOST');
        //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
        //if your network does not support SMTP over IPv6,
        //though this may cause issues with TLS

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $mail->Port = AppConfig::getEnv('MAIL_PORT');

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = AppConfig::getEnv('MAIL_USERNAME');

        //Password to use for SMTP authentication
        $mail->Password = AppConfig::getEnv('MAIL_PASSWORD');

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        //or predefined aliases that you have configured within your account.
        //Do not use user-submitted addresses in here
        $mail->setFrom(AppConfig::getEnv('MAIL_USERNAME'), 'Service client');

        //Set an alternative reply-to address
        //This is a good place to put user-submitted addresses
        $mail->addReplyTo($_POST['email'], $firstname_dest . ' ' . $lastname_dest);

        //Set who the message is to be sent to
        $mail->addAddress(AppConfig::getEnv('MAIL_USERNAME'), 'Service client');

        //Set the subject line
        $mail->Subject = AppConfig::getEnv('MAIL_SUBJECT');

        $mail->isHTML(true);

        $mail->Body = 'Message de ' . $_POST['email'] . ' le ' . date("d/m/Y H:i:s") . ' au service client.<br><br>Communication:<br>' . $message_dest;

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';

        //send the message, check for errors
        if (!$mail->send()) {
            echo "<div style=\"margin:10%; text-align:center; \"><h3>Une erreur est survenue lors de l'envoi du fichier PDF: </h3></div>" . $mail->ErrorInfo;
        } else {
            echo "<div style=\"margin:10%; text-align:center; \"><h3>Le mail a été envoyé avec succès au service client. Un collaborateur reprendra contact avec vous.</h3></div>";
        }
    }
}
