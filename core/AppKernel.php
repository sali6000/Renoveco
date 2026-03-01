<?php

namespace Core;

use Config\EnvLoader;
use Core\Logger\AccessLogger;
use Core\Routing\Router;
use Config\AppConfig;

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
            $this->executeRequest();                // 🚀 Exécution du routeur et du contrôleur
        } catch (\Throwable $e) {
            // En cas d'erreur critique lors du bootstrap, on log et affiche un message simple
            AccessLogger::log("❌ Erreur critique lors du bootstrap : " . $e, AccessLogger::LEVEL_ERROR);
            if ((AppConfig::getEnv('APP_ENV') ?? '') === 'dev') {
                echo "<h1>Erreur critique lors du démarrage (dev)</h1><pre>$e</pre>";
            } else {
                echo "Une erreur technique est survenue lors du démarrage de l'application. Veuillez contacter l'administrateur.";
            }
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
        define('SECURE_CHECK', true); // ✅ Empêche le chargement direct de certains fichiers sans passer par AppKernel
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
        $envLoader = new EnvLoader(realpath(__DIR__ . '/../'));
        $envLoader->load(); // Charge les variables .env
    }

    // --------------------------------------------------
    // Handlers globaux
    // --------------------------------------------------
    private function registerGlobalErrorHandlers(): void
    {
        // Erreurs PHP classiques → converties en Throwable
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $e = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
            $this->handleFatalError($e);
        });

        // Erreurs fatales au shutdown
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
                $this->handleFatalError($e);
            }
        });

        // Exceptions non catchées
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

        // Logging complet
        AccessLogger::log(
            "[$errorId] Erreur critique: " . get_class($e) . " - " . $e->getMessage() .
                " dans {$e->getFile()}:{$e->getLine()}\nStack trace:\n" . $e->getTraceAsString(),
            AccessLogger::LEVEL_ERROR
        );

        // Retour HTTP
        http_response_code(500);

        // Affichage selon environnement
        if ((AppConfig::getEnv('APP_ENV') ?? '') === 'dev') {
            echo "<h1>Erreur système (dev)</h1>";
            echo "<pre>[$errorId] " . htmlspecialchars((string)$e) . "</pre>";
        } else {
            echo "Une erreur technique est survenue (Code : $errorId).";
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
