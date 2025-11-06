<?php

namespace App\Modules\Auth\Controller;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\Controller;
use App\Exception\ServiceException;
use App\Modules\Auth\Service\AuthService;

class AuthController extends Controller
{
    protected const VIEW = 'Auth';

    public function __construct(private AuthService $authService)
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct();
    }

    public function login()
    {
        // Génération d'un token CSRF unique si inexistant
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Toujours envoyer le token à la vue
        $this->set('csrf_token', $_SESSION['csrf_token']);

        // Sauvegarder l'URL où l’utilisateur voulait aller (si pas déjà en login)
        if (empty($_SESSION['redirect_after_login']) && isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], '/login')) {
            $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
        }
        $this->render(__FUNCTION__);
    }

    public function connection()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''; // Évite les caractères indésirables (XSS indirectes via email).
        $password = $_POST['password'] ?? '';

        // Pour éviter qu'un site externe POST sur mon /login sans mon consentement
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = "Requête invalide car non effectué depuis le serveur officiel.";
            header('Location: /auth/login');
            exit;
        }

        try {
            $user = $this->authService->authenticate($email, $password);

            if ($user) {
                session_regenerate_id(true); // Pour éviter la fixation de session
                $_SESSION['user'] = $user;
                $redirectTo = $_SESSION['redirect_after_login'] ?? '/home';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirectTo");
                exit;
            } else {
                $_SESSION['flash_error'] = "Email ou mot de passe incorrect.";
                $this->view('auth/login');
                exit;
            }
        } catch (ServiceException $e) {
            $this->handleException($e, __METHOD__ . ' → Service → ');
        } catch (\Throwable $e) {
            $this->handleException($e, __METHOD__ . ' → System → ');
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}
