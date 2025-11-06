<?php

namespace Core;

use Config\EnvLoader;
use Core\Logger\AccessLogger;
use Core\Router;
use Config\AppConfig;

class AppKernel
{
    /**
     * Point d'entrée principal de l'application.
     * Exécute la chaîne complète : sécurité, chargement env, gestion des erreurs, routage, logging.
     */
    public function handle(): void
    {
        $this->secureBootstrap();       // 🔒 Sécurise le démarrage (sessions, constantes, ID requête)
        $this->loadEnvironment();       // 🌱 Charge l'environnement (.env, autoload)
        $this->checkWritableDirectories(); // ✅ Vérifie que les répertoires critiques sont bien configurés
        $this->registerGlobalErrorHandlers(); // 🛠️ Active les handlers d'erreurs / exceptions globales
        $this->executeRequest();        // 🚦 Lance le routeur pour traiter la requête
        $this->logRequestDuration();    // ⏱️ Log du temps d'exécution
    }

    /**
     * Initialise les éléments critiques pour la sécurité et le débogage.
     * - Active un drapeau SECURE_CHECK (permet de détecter un accès direct non autorisé dans d'autres fichiers)
     * - Démarre la session en mode sécurisé
     * - Sauvegarde le timestamp de début de requête et un identifiant unique
     */
    private function secureBootstrap(): void
    {
        define('SECURE_CHECK', true); // ✅ Empêche le chargement direct de certains fichiers sans passer par AppKernel
        $this->startSessionSecurely(); // 🔑 Démarrage sécurisé de la session
        define('REQUEST_START_TIME', microtime(true)); // ⏱️ Marque le début de la requête pour le suivi des perfs
        define('REQUEST_ID', $this->getRequestId());   // 🆔 Génère un identifiant unique de requête pour corrélation dans les logs
    }

    /**
     * Démarre la session avec des paramètres sécurisés (HTTP Only, Secure, SameSite)
     * => ⚠️ Si tu as des problèmes de session, c'est ici que ça se passe.
     */
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

    /**
     * Retourne l'ID unique de requête (utilisé pour tracer les logs).
     */
    private function getRequestId(): string
    {
        // Si un proxy / reverse-proxy fournit un X-Request-ID, on l'utilise
        return $_SERVER['HTTP_X_REQUEST_ID'] ?? bin2hex(random_bytes(8));
    }

    /**
     * Charge l'autoload et les variables d'environnement (.env)
     * => ⚠️ Si APP_ENV ou d'autres variables sont absentes, vérifier ici.
     */
    private function loadEnvironment(): void
    {
        require_once realpath(__DIR__ . '/../vendor/autoload.php'); // Composer autoload
        $envLoader = new EnvLoader(realpath(__DIR__ . '/../'));
        $envLoader->load(); // Charge les variables .env dans $_ENV
    }

    /**
     * Enregistre des handlers globaux pour :
     * - Les erreurs fatales (register_shutdown_function)
     * - Les erreurs PHP classiques (set_error_handler)
     * - Les exceptions non catchées (set_exception_handler)
     *
     * Cela permet de :
     *  - Logger chaque erreur
     *  - Afficher un message plus clair en mode dev
     *  - Empêcher l'affichage brut d'erreurs en production
     */
    private function registerGlobalErrorHandlers(): void
    {
        // Gestion des erreurs fatales (ex: E_ERROR, E_PARSE...)
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $message = "[SHUTDOWN] Erreur fatale : {$error['message']} dans {$error['file']} à la ligne {$error['line']}";
                AccessLogger::log($message, AccessLogger::LEVEL_ERROR);

                if (($_ENV['APP_ENV'] ?? '') === 'dev') {
                    echo "<pre style='color:red;'>$message</pre>";
                } else {
                    echo "Une erreur critique est survenue. Contactez l'administrateur.";
                }
            }
        });

        // Gestion des warnings, notices, etc.
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            $levels = [
                E_ERROR             => 'Erreur',
                E_WARNING           => 'Warning',
                E_PARSE             => 'Parse Error',
                E_NOTICE            => 'Notice',
                E_DEPRECATED        => 'Deprecated',
                E_USER_WARNING      => 'User Warning',
                E_USER_NOTICE       => 'User Notice'
            ];

            $levelName = $levels[$errno] ?? 'Unknown';
            $message = sprintf("[%s] %s dans %s:%d", $levelName, $errstr, $errfile, $errline);

            AccessLogger::log($message, AccessLogger::LEVEL_ERROR);

            if (($_ENV['APP_ENV'] ?? '') === 'dev') {
                echo "<pre style='color:orange;'>$message</pre>";
            }
            return true; // ✅ Empêche l'affichage par défaut de PHP
        });

        // Gestion des exceptions non attrapées
        set_exception_handler(function (\Throwable $e) {
            $errorId = uniqid('fatal_', true); // 🆔 Génère un identifiant d'erreur unique pour retrouver dans les logs
            AccessLogger::log("[$errorId] ❌ Erreur globale: " . $e, AccessLogger::LEVEL_ERROR);
            http_response_code(500); // Renvoie HTTP 500

            if (($_ENV['APP_ENV'] ?? '') === 'dev') {
                echo "<h1>Erreur système (dev)</h1><pre>$e</pre><p>Code : $errorId</p>";
            } else {
                echo "Une erreur technique est survenue (Code : $errorId).";
            }
        });
    }

    /**
     * Exécute le routeur et déclenche le contrôleur correspondant à l'URI.
     * => ⚠️ Si la page ne se charge pas correctement, vérifier que l'URI est bien transmise ici.
     */
    private function executeRequest(): void
    {
        $uri = $_GET['index'] ?? $_SERVER['REQUEST_URI'];
        $router = new Router($uri);
        $router->route();
    }

    /**
     * Calcule le temps d'exécution total et l'écrit dans les logs.
     * => Pratique pour détecter les pages lentes.
     */
    private function logRequestDuration(): void
    {
        $duration = microtime(true) - REQUEST_START_TIME;
        AccessLogger::log(sprintf("Durée de la requête : %.3f secondes", $duration), AccessLogger::LEVEL_PERF);
    }

    private function checkWritableDirectories(): void
    {
        // Liste des répertoires critiques qui doivent être accessibles en écriture
        $dirs = [
            AppConfig::getPath('APP_PATH_LOCAL_PUBLIC_UPLOADS'),
        ];

        foreach ($dirs as $dir) {
            if ($dir === false || !is_dir($dir)) {
                AccessLogger::log("⚠️ Dossier manquant : $dir", AccessLogger::LEVEL_ERROR);
                throw new \RuntimeException("Le dossier $dir est introuvable.");
            }

            if (!is_writable($dir)) {
                AccessLogger::log("⛔ Permission refusée sur : $dir", AccessLogger::LEVEL_ERROR);
                throw new \RuntimeException("Le dossier $dir n’est pas accessible en écriture.");
            }
        }
    }
}
