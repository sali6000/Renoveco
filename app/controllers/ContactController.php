<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use Dotenv\Dotenv;

class ContactController extends Controller
{
    public function __construct()
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct();
    }

    /**
     * Affiche la page de contact et gère l'envoi de mail
     */
    public function Index()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $this->view('contact');
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->mailSend();
        }
    }

    /**
     * Envoie un mail au service client
     */
    public function mailSend()
    {
        // Charger les variables d'environnement
        $dotenv = Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();

        $firstname_dest = $_POST['firstname'];
        $lastname_dest = $_POST['lastname'];
        $message_dest = $_POST['message'];

        //Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        $headers = "From: " . $_ENV['MAIL_FROM_ADDRESS'] . "\r\n";
        $headers .= "Reply-To: " . $_ENV['MAIL_FROM_ADDRESS'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        // Pour avoir un retour d'information lors de l'envoi de mail => 

        switch ($_ENV['MAIL_DEBUG']) {
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
        $mail->Host = $_ENV['MAIL_HOST'];
        //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
        //if your network does not support SMTP over IPv6,
        //though this may cause issues with TLS

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $mail->Port = $_ENV['MAIL_PORT'];

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = $_ENV['MAIL_FROM_ADDRESS'];

        //Password to use for SMTP authentication
        $mail->Password = $_ENV['MAIL_PASSWORD'];

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        //or predefined aliases that you have configured within your account.
        //Do not use user-submitted addresses in here
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], 'Service client');

        //Set an alternative reply-to address
        //This is a good place to put user-submitted addresses
        $mail->addReplyTo($_POST['email'], $firstname_dest . ' ' . $lastname_dest);

        //Set who the message is to be sent to
        $mail->addAddress($_ENV['MAIL_FROM_ADDRESS'], 'Service client');

        //Set the subject line
        $mail->Subject = $_ENV['MAIL_SUBJECT'];

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
