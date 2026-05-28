<?php

namespace Src\Modules\Contact\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Exception\Application\ApplicationExceptionInterface;
use Src\Exception\Http\HttpExceptionInterface;
use Src\Modules\Contact\Application\UseCase\SendContactMessageUseCase;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;

#[Route('/contact')]
class ContactIndexController extends BaseController
{
    public function __construct(
        private readonly CsrfGuard $csrf,
        private readonly SendContactMessageUseCase $sendContactMessage
    ) {}

    #[Route('', methods: ['GET'])]
    public function index()
    {
        // Afficher la vue
        $this->render('Contact/index.twig', [
            'csrf_token'    => $this->csrf->generateToken(),
            'flash_error'   => $this->getFlash('error'),
            'flash_success' => $this->getFlash('success'),
            'canonical' => "https://renoveconstruct.be/contact"
        ]);
    }

    #[Route('mailSend', methods: ['POST'])]
    public function mailSend()
    {
        try {
            // Validation du formulaire
            $this->csrf->validateOrFail();

            // 1. Sanitize (nettoie la donnée brute)
            $firstname = SecurityHelper::sanitizeString($_POST['firstname'] ?? null);
            $lastname  = SecurityHelper::sanitizeString($_POST['lastname']  ?? null);
            $company   = SecurityHelper::sanitizeString($_POST['company']   ?? null);
            $message   = SecurityHelper::sanitizeString($_POST['message']   ?? null);
            $email     = SecurityHelper::sanitizeEmail($_POST['email']      ?? null);

            // 2. Validate (vérifie les règles métier sur la donnée nettoyée)
            $firstname = SecurityHelper::validateString($firstname, 'Prénom',  minLength: 2, maxLength: 50,  canBeEmpty: true);
            $lastname  = SecurityHelper::validateString($lastname,  'Nom',     minLength: 2, maxLength: 50,  canBeEmpty: true);
            $company   = SecurityHelper::validateString($company,   'Société', minLength: 2, maxLength: 50,  canBeEmpty: true);
            $message   = SecurityHelper::validateString($message,   'Message', minLength: 5, maxLength: 300);
            $email     = SecurityHelper::validateEmail($email, 'Email');
        } catch (HttpExceptionInterface $e) {

            // Résultat en cas d'erreur (de formulaire ou sanitization)
            $this->flashAndRedirect('error', $e->getMessage(), '/contact');
        }

        // Récupération de l'IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        try {
            // Envoi du message
            $result = $this->sendContactMessage->execute($firstname, $lastname, $company, $email, $message, $ip);

            // Résultat en cas de réussite ou échec
            $result->isFailure()
                ? $this->flashAndRedirect('error', $result->getMessage(), '/contact')
                : $this->flashAndRedirect('success', 'Votre message a bien été envoyé.', '/contact');
        } catch (ApplicationExceptionInterface $e) {

            // Erreur en cas de rate limit atteind
            $this->flashAndRedirect('error', $e->getMessage(), '/contact');
        }
    }
}
