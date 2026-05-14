<?php

namespace Src\Modules\Contact\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Exception\ServiceException;
use Src\Exception\ValidationException;
use Src\Modules\Contact\Domain\Service\ContactService;

#[Route('/contact')]
class ContactIndexController extends BaseController
{
    public function __construct(private ContactService $contactService) {}

    #[Route('', methods: ['GET'])]
    public function index()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->render('Contact/index.twig', [
            'csrf_token'    => $_SESSION['csrf_token'],
            'flash_error'   => $this->getFlash('error'),
            'flash_success' => $this->getFlash('success'),
        ]);
    }

    #[Route('mailSend', methods: ['POST'])]
    public function mailSend()
    {
        // CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->setFlash('error', 'Requête invalide.');
            $this->redirect('/contact');
        }

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
