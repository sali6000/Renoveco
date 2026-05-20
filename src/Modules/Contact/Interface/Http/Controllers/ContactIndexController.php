<?php

namespace Src\Modules\Contact\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Exception\ServiceException;
use Src\Modules\Contact\Domain\Service\ContactService;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;

#[Route('/contact')]
class ContactIndexController extends BaseController
{
    public function __construct(
        private readonly CsrfGuard $csrf,
        private readonly ContactService $contactService
    ) {}

    #[Route('', methods: ['GET'])]
    public function index()
    {
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
        $this->csrf->validateOrFail();

        try {
            $firstname = SecurityHelper::sanitizeString($_POST['firstname'] ?? null, 'Prénom',    minLength: 2, maxLength: 50, canBeEmpty: true, canBeNull: true);
            $lastname  = SecurityHelper::sanitizeString($_POST['lastname']  ?? null, 'Nom',       minLength: 2, maxLength: 50, canBeEmpty: true, canBeNull: true);
            $company   = SecurityHelper::sanitizeString($_POST['company']   ?? null, 'Société',   minLength: 2, maxLength: 50, canBeEmpty: true, canBeNull: true);
            $message   = SecurityHelper::sanitizeString($_POST['message']   ?? null, 'Message',   minLength: 5, maxLength: 300);
            $email   = SecurityHelper::sanitizeEmail($_POST['email'] ?? null);

            $this->contactService->send($firstname, $lastname, $company, $email, $message);

            $this->setFlash('success', 'Votre message a bien été envoyé.');
            $this->redirect('/contact');
        } catch (ServiceException $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/contact');
        } catch (\Throwable $e) {
            $this->handleException($e, __METHOD__ . ' → ');
        }
    }
}
