<?php

namespace Src\Modules\Auth\Interface\Http\Controllers;

use Core\BaseController;
use Src\Exception\ServiceException;
use Src\Exception\ValidationException;
use Src\Modules\Auth\Domain\Service\AuthService;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;

#[Route('/auth')]
class AuthLoginController extends BaseController
{
    public function __construct(
        private readonly CsrfGuard      $csrf,
        private readonly SessionManager $session,
        private readonly AuthService    $authService,
    ) {}

    #[Route('login', methods: ['GET'])]
    public function login()
    {
        // Récupère l'URL de la page précédente envoyée par le navigateur (non garantie, peut être absente)
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // Enregistrer la page vers laquel rediriger SI:
        // - Aucune redirection n'est prévue ($_SESSION['redirect_after_login'])
        // - Une nouvelle redirection est prévue ($referer)
        // - La nouvelle redirection prévue ne renvoit pas vers /login (boucle de redirection)
        if (
            empty($this->session->get('redirect_after_login')) // Aucune redirection déjà prévue en session
            && !empty($referer)                                // Le navigateur a bien envoyé un referer
            && !str_contains($referer, '/login')               // Évite une boucle de redirection vers /login
        ) {
            $this->session->setIntendedRedirect($referer);
        }
        $this->render('Auth/login.twig', [
            'csrf_token'  => $this->csrf->generateToken(),
            'flash_error' => $this->getFlash('error'),
        ]);
    }

    #[Route('connection', methods: ['POST'])]
    public function connection()
    {
        // Vérification du formulaire (csrf) + Sanitization du champ email + Récupération du mot de passe
        $this->csrf->validateOrFail();
        $email    = SecurityHelper::sanitizeEmail($_POST['email'] ?? null);
        $password = $_POST['password'] ?? '';

        try {
            // Vérification des informations reçue pour la connection
            $user = $this->authService->loginUser($email, $password);
            $this->authService->updateUserLastLogin($user['id']);

            // Enregistrement de la session utilisateur
            $this->session->openAuthenticatedSession($user);
            $this->redirect($this->session->consumeRedirect());
        } catch (ValidationException | ServiceException $e) {

            // Afficher un message d'erreur en flash et rediriger vers la page de login en cas d'échec
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/auth/login');
        } catch (\Throwable $e) {
            $this->handleException($e, __METHOD__ . ' → System → ');
        }
    }

    #[Route('logout', methods: ['GET'])]
    public function logout()
    {
        $this->session->destroy();
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
        $this->redirect('/');
    }
}
