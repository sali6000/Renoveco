<?php

namespace Src\Modules\Auth\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Exception\Application\ApplicationExceptionInterface;
use Src\Exception\Http\HttpExceptionInterface;
use Src\Modules\Auth\Application\UseCase\LoginUserUseCase;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;

#[Route('/auth')]
class AuthLoginController extends BaseController
{
    public function __construct(
        private readonly CsrfGuard      $csrf,
        private readonly SessionManager $session,
        private readonly LoginUserUseCase $loginUser
    ) {}

    #[Route('login', methods: ['GET'])]
    public function login()
    {
        // Afficher la vue
        $this->render('Auth/login.twig', [
            'csrf_token'  => $this->csrf->generateToken(),
            'flash_error' => $this->getFlash('error'),
            'flash_success' => $this->getFlash('success'),
        ]);
    }

    #[Route('connection', methods: ['POST'])]
    public function connection()
    {
        try {
            // Validation du formulaire
            $this->csrf->validateOrFail();

            // Validation du champ email
            $email = SecurityHelper::validateEmail($_POST['email'] ?? null);
        } catch (HttpExceptionInterface $e) {

            // Résultat en cas d'erreur (de formulaire ou sanitization)
            $this->flashAndRedirect('error', $e->getMessage(), '/contact');
        }

        // Récupération du mot de passe
        $password = $_POST['password'] ?? '';

        // Récupération de l'IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        try {
            // Connection
            $result = $this->loginUser->execute($email, $password, $ip);

            // Résultat en cas de réussite ou échec de connection
            $result->isSuccess()
                ? $this->flashAndRedirect('success', 'Vous êtes connecté', '/auth/login')
                : $this->flashAndRedirect('error', $result->getMessage(), '/auth/login');
        } catch (ApplicationExceptionInterface $e) {

            // Erreur en cas de rate limite atteind
            $this->flashAndRedirect('error', $e->getMessage(), '/auth/login');
        }
    }

    #[Route('logout', methods: ['GET'])]
    public function logout()
    {
        // Déconnection de la session
        $this->session->destroy();

        // Redirection vers home
        $this->redirect('/');
    }
}
