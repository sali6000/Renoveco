<?php

namespace Core;

use Config\EnvLoader;
use Core\Logger\AccessLogger;
use Core\Routing\Router;
use Config\AppConfig;
use Core\Routing\Exception\RoutingException;

class AppKernel
{
    private Container $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    // Point d'entrée
    public function handle(): void
    {
        try {
            $this->initialization();
            $this->executeRequest();
        } catch (RoutingException $e) {
            $code = $e->getCode();

            $validCodes = [403, 404];
            $httpCode   = in_array($code, $validCodes, strict: true) ? $code : 500;
            $page       = match ($httpCode) {
                403 => '403.html',
                404 => '404.html',
                default => '500.html',
            };

            http_response_code($httpCode);
            AccessLogger::logTo($e, AccessLogger::LEVEL_ERROR, AccessLogger::CHANNEL_KERNEL);
            include __DIR__ . '/../public/errors/' . $page;
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            AccessLogger::logTo($e, AccessLogger::LEVEL_ERROR, AccessLogger::CHANNEL_KERNEL);
            include __DIR__ . '/../public/errors/500.html';
            exit;
        }
    }

    private function initialization()
    {
        $this->secureLogs();
        $this->secureBootstrap();               // 🔐 Initialisation des éléments de sécurité
        $this->loadEnvironment();               // 🌐 Chargement des variables d'environnement
        $this->registerGlobalErrorHandlers();   // 🚨 Enregistrement des handlers d'erreurs globaux

    }

    private function secureLogs()
    {
        // En prod, On log. On affiche rien à utilisateur.
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        error_reporting(E_ALL);
    }

    // --------------------------------------------------
    // Bootstrap sécurisé
    // --------------------------------------------------
    private function secureBootstrap(): void
    {
        $this->startSessionSecurely(); // 🔑 Démarrage sécurisé de la session
        define('REQUEST_START_TIME', microtime(true)); // ⏱️ Marque le début de la requête pour le suivi des perfs
        define('REQUEST_ID', $this->getRequestId());   // 🆔 Génère un identifiant unique de requête pour corrélation dans les logs
    }

    // --------------------------------------------------
    // Démarrage d'une session sécurisée
    // --------------------------------------------------
    private function startSessionSecurely(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // 🔧 Active les bonnes pratiques de session côté PHP
            ini_set('session.cookie_httponly', 1); // Empêche l'accès aux cookies via JS
            ini_set('session.use_strict_mode', 1); // Empêche l'utilisation d'ID de session non valides
            ini_set('session.use_only_cookies', 1); // Interdit les sessions via URL (plus sûr)

            // Active le flag "secure" uniquement si HTTPS est actif
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                ini_set('session.cookie_secure', 1);
            }
        }

        // Définit les paramètres de cookie de session
        session_set_cookie_params([
            'lifetime' => 0, // Session cookie (expire à la fermeture du navigateur)
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Strict' // Empêche les attaques CSRF basiques
        ]);

        session_start(); // 🚀 Lance la session
    }

    // --------------------------------------------------
    // Chargement des variables d'environnement
    // --------------------------------------------------
    private function loadEnvironment(): void
    {
        $envLoader = new EnvLoader();
        $envLoader->load(); // Charge les variables .env
    }

    // --------------------------------------------------
    // Handlers globaux
    // --------------------------------------------------
    private function registerGlobalErrorHandlers(): void
    {
        /**
         * Erreurs PHP classiques
         * Ex:
         * echo $undefinedVariable;
         * array_map('invalid', []);
         * include 'missing.php';
         */
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $e = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
            $this->handleFatalError($e);
        });

        /**
         * Erreurs fatales qui tuent le process
         * Ex:
         * call_to_undefined_function();
         * class Foo extends Missing {};
         * fichier avec syntaxe invalide
         */
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
                $this->handleFatalError($e);
            }
        });

        /**
         * Si une exception est levée en dehors de handle()
         * Ex:
         * Un destructeur, un callback async, etc.
         */
        set_exception_handler(function (\Throwable $e) {
            $this->handleFatalError($e);
        });
    }

    // --------------------------------------------------
    // Gestion centralisée des erreurs / exceptions
    // --------------------------------------------------
    private function handleFatalError(\Throwable $e): void
    {
        $errorId = uniqid('fatal_', true);

        AccessLogger::logTo(
            $e,
            AccessLogger::LEVEL_ERROR,
            AccessLogger::CHANNEL_KERNEL
        );

        http_response_code(500);

        if (AppConfig::getEnv('APP_ENV') === 'dev') {
            echo "<h1>Erreur système [$errorId]</h1>";
            echo "<pre>" . htmlspecialchars((string) $e) . "</pre>";
        } else {
            include __DIR__ . '/../public/errors/500.html';
        }

        exit();
    }

    // --------------------------------------------------
    // Exécution du routeur
    // --------------------------------------------------
    private function executeRequest(): void
    {
        $uri = $_GET['index'] ?? $_SERVER['REQUEST_URI'];
        $router = new Router($uri, $this->container);
        $router->route();
    }

    // --------------------------------------------------
    // Générer un identifiant unique pour la requête
    // --------------------------------------------------
    private function getRequestId(): string
    {
        // Si un proxy / reverse-proxy fournit un X-Request-ID, on l'utilise
        return $_SERVER['HTTP_X_REQUEST_ID'] ?? bin2hex(random_bytes(8));
    }
}
