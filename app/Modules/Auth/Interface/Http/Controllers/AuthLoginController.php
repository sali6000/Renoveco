<?php

namespace App\Modules\Auth\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use App\Exception\ServiceException;
use App\Exception\ValidationException;
use App\Modules\Auth\Domain\Service\AuthService;
use Core\Routing\Attribute\Route;

#[Route('/auth')]
class AuthLoginController extends BaseController
{
    public function __construct(private AuthService $authService)
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct('Auth');
    }

    #[Route('login', methods: ['GET'])]
    public function login()
    {
        // Génération d'un token CSRF unique si inexistant
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Toujours envoyer le token à la vue
        $this->set('csrf_token', $_SESSION['csrf_token']);

        // Récupérer et vider le flash pour qu'il n'apparaisse qu'une seule fois
        if ($msg = $this->getFlash('error')) {
            $this->set('flash_error', $msg);
        }

        // Sauvegarder l'URL où l’utilisateur voulait aller (si pas déjà en login)
        if (empty($_SESSION['redirect_after_login']) && isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], '/login')) {
            $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
        }
        $this->render(__FUNCTION__);
    }

    #[Route('connection', methods: ['POST'])]
    public function connection()
    {
        // Sanitization de l'email
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $password = $_POST['password'] ?? '';

        // Pour éviter qu'un site externe POST sur mon /login sans mon consentement
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {

            // Afficher le message d'erreur en flash et rediriger vers la page de login.
            $this->setFlash('error', 'Requête invalide car non effectué depuis le serveur officiel.');
            $this->redirect('/auth/login');
        }

        try {
            // Effectuer le login via le service
            $user = $this->authService->loginUser($email, $password);
            $this->authService->updateUserLastLogin($user['id']);

            // Stocker les infos utilisateur dans une nouvelle session
            session_regenerate_id(true); // Prévenir les attaques de fixation de session
            $_SESSION['user'] = $user;

            // Rediriger vers la page initialement demandée ou la page d'accueil
            $redirectTo = $_SESSION['redirect_after_login'] ?? '/home';
            unset($_SESSION['redirect_after_login']);
            $this->redirect($redirectTo);
        } catch (ValidationException $e) {

            // Afficher le message d'erreur en flash et rediriger vers la page de login
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/auth/login');
        } catch (ServiceException $e) {

            // Afficher le message d'erreur en flash et rediriger vers la page de login
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/auth/login');
        } catch (\Throwable $e) {
            $this->handleException($e, __METHOD__ . ' → System → ');
        }
    }

    #[Route('logout', methods: ['GET'])]
    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}
