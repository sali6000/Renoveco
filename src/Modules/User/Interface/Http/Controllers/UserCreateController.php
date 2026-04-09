<?php

namespace Src\Modules\User\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Src\Exception\ServiceException;
use Src\Exception\UniqueConstraintException;
use Src\Modules\User\Domain\Service\UserService;
use Core\BaseController;
use Core\Logger\AccessLogger;
use Core\Routing\Attribute\Route;
use Core\Support\ResponseHelper;
use Core\Support\SecurityHelper;

#[Route('/user')]
class UserCreateController extends BaseController
{
    public function __construct(private UserService $userService) {}

    #[Route('register', methods: ['GET'])]
    public function create()
    {
        // Génération d'un token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Sauvegarder l'URL où l’utilisateur voulait aller (si pas déjà en login)
        if (empty($_SESSION['redirect_after_login']) && isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], '/auth/login')) {
            $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
        }

        // Afficher la vue Modules/User/Views/create.twig
        $this->render('User/create.twig', ['csrf_token' => $_SESSION['csrf_token']]);
    }

    #[Route('registerJson', methods: ['POST'])]
    public function registerJson()
    {
        try {
            // Récupérer le JSON envoyé
            $data = json_decode(file_get_contents('php://input'), true);

            // Si le formulaire reçu n'a pas de token CSRF
            if (!isset($data['csrf_token'], $_SESSION['csrf_token'])) {
                return  ResponseHelper::error('Token CSRF manquant.');
            }

            // Si le token CSRF reçu ne correspond pas au token envoyé par /user/create
            if (!hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
                return ResponseHelper::error('Token CSRF invalide.');
            }

            // Nettoyer données
            $email = SecurityHelper::sanitizeString($data['email'], "email", minLength: 5, maxLength: 30);
            $password = SecurityHelper::sanitizeString($data['password'], "mot de passe", minLength: 8, maxLength: 30);

            // Sauvegarde de l'utilisateur en base
            $user = $this->userService->createUser($email, $password);

            // Succès
            return ResponseHelper::success("Bienvenue " . $user->email . " ! Vous serez redirigé sous peu vers la page de connection.");
        } catch (UniqueConstraintException $e) {
            return ResponseHelper::error("Ce champ est déjà utilisé : " . $e->getField());
        } catch (ServiceException $e) {
            return ResponseHelper::error("Erreur du service d'enregistrement. (Code : " . $e->getErrorId() . ")");
        } catch (\Throwable $e) {
            $errorId = uniqid('usr_ctrl_', true);
            AccessLogger::log("Erreur inconnue (Code: $errorId) : " . $e, AccessLogger::LEVEL_ERROR);
            return ResponseHelper::error("❌ Erreur inconnue, contactez l'administrateur (Code: $errorId)");
        }
    }
}
