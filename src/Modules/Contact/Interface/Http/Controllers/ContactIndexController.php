<?php

namespace Src\Modules\Contact\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Exception\ValidationException;
use Src\Modules\Contact\Application\UseCase\SendContactMessage;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfException;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;

#[Route('/contact')]
class ContactIndexController extends BaseController
{
    public function __construct(
        private readonly CsrfGuard $csrf,
        private readonly SendContactMessage $sendContactMessage
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

            // Sanitization des champs
            $firstname = SecurityHelper::sanitizeString($_POST['firstname'] ?? null, 'Prénom',    minLength: 2, maxLength: 50, canBeEmpty: true, canBeNull: true);
            $lastname  = SecurityHelper::sanitizeString($_POST['lastname']  ?? null, 'Nom',       minLength: 2, maxLength: 50, canBeEmpty: true, canBeNull: true);
            $company   = SecurityHelper::sanitizeString($_POST['company']   ?? null, 'Société',   minLength: 2, maxLength: 50, canBeEmpty: true, canBeNull: true);
            $message   = SecurityHelper::sanitizeString($_POST['message']   ?? null, 'Message',   minLength: 5, maxLength: 300);
            $email   = SecurityHelper::sanitizeEmail($_POST['email'] ?? null);
        } catch (CsrfException | ValidationException $e) {

            // Résultat en cas d'erreur (de formulaire ou sanitization)
            $this->flashAndRedirect('error', $e->getMessage(), '/contact');
        }

        // Envoi du message
        $result = $this->sendContactMessage->execute($firstname, $lastname, $company, $email, $message);

        // Résultat en cas de réussite ou échec
        $result->isFailure()
            ? $this->flashAndRedirect('error', $result->getMessage(), '/contact')
            : $this->flashAndRedirect('success', 'Votre message a bien été envoyé.', '/contact');
    }
}
