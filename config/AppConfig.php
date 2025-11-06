<?php

namespace Config;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Config\Database;

class AppConfig
{
    private static bool $configLoaded = false;
    private static ?array $whitelist = null;
    private static ?\PDO $db = null;

    public static function getBool(string $key, bool $default = false): bool
    {
        return filter_var(self::get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        // Priorité 1 : variables d'environnement (env)
        if (isset($_ENV[$key])) return $_ENV[$key];
        if (isset($_SERVER[$key])) return $_SERVER[$key];

        // Priorité 2 : config interne
        return self::$config[$key] ?? $default;
    }

    // Charge la configuration des constantes si elle n'est pas déjà chargée
    // Cela permet de s'assurer que les constantes et chemins sont définis avant utilisation
    private static function loadConfig(): void
    {
        if (!self::$configLoaded) {
            require_once __DIR__ . '/constants.php';
            self::$configLoaded = true;
        }
    }

    // Permet de récupérer la liste blanche des accès
    // Si la liste n'est pas encore chargée, elle est chargée depuis le fichier access_whitelist.php
    public static function getWhitelist(): array
    {
        if (self::$whitelist === null) {
            self::$whitelist = require __DIR__ . '/access_whitelist.php';
        }
        return self::$whitelist;
    }

    // Permet de récupérer une variable d'environnement
    // Si la variable n'existe pas, retourne la valeur par défaut
    public static function getEnv(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    // Permet de récupérer la base de données
    // Si la connexion n'est pas encore établie, elle est créée via la classe Database
    public static function getDatabase(): \PDO
    {
        if (self::$db === null) {
            self::$db = Database::getInstance(); // ta classe actuelle
        }
        return self::$db;
    }

    // Permet de récupérer un chemin défini dans le fichier Routes.php
    // Si le chemin n'est pas défini, retourne null
    public static function getPath(string $key): mixed
    {
        self::loadConfig();

        if (defined($key)) {
            return constant($key);
        }

        return null;
    }

    // Permet de récupérer les chemins pour Twig
    // Retourne un tableau associatif avec les chemins et leurs valeurs
    public static function getGlobalsForTwig(): array
    {
        self::loadConfig();
        return [
            'entrepriseTitle' => self::getPath('APP_INFO_ENTREPRISE_TITLE'),
            'entrepriseMail'  => self::getPath('APP_INFO_ENTREPRISE_MAIL'),
            'entrepriseHttp'  => self::getPath('APP_INFO_ENTREPRISE_HTTP'),
            // ...
        ];
    }

    // Permet de récupérer tous les chemins définis dans le fichier Routes.php
    // Retourne un tableau associatif avec les chemins et leurs valeurs
    public static function getAllPaths(): array
    {
        self::loadConfig();
        return [
            'params' => [ // parametres de l'application
                'APP_PARAM_NAME' => APP_PARAM_NAME,
                'APP_PARAM_CONTROLLER_DEFAULT' => APP_PARAM_CONTROLLER_DEFAULT,
                'APP_PARAM_ACTION_DEFAULT' => APP_PARAM_ACTION_DEFAULT
            ],
            'infos' => [ // infos sur l'entreprise
                'APP_INFO_ENTREPRISE_NAME' => APP_INFO_ENTREPRISE_NAME,
                'APP_INFO_ENTREPRISE_HTTP' => APP_INFO_ENTREPRISE_HTTP,
                'APP_INFO_ENTREPRISE_MAIL' => APP_INFO_ENTREPRISE_MAIL,
                'APP_INFO_ENTREPRISE_PRODUCT_URL_SEO' => APP_INFO_ENTREPRISE_PRODUCT_URL_SEO,
                'APP_INFO_ENTREPRISE_TITLE' => APP_INFO_ENTREPRISE_TITLE,
                'APP_INFO_ENTREPRISE_TYPE' => APP_INFO_ENTREPRISE_TYPE,
            ],
            'paths' => [ // chemins serveur/client accessibles
                'APP_PATH_LOCAL' => APP_PATH_LOCAL,
                'APP_PATH_LOCAL_APP_MODELS' => APP_PATH_LOCAL_APP_MODELS,
                'APP_PATH_LOCAL_APP_CACHE_TWIG' => APP_PATH_LOCAL_APP_CACHE_TWIG,
                'APP_PATH_LOCAL_APP_CONTROLLERS' => APP_PATH_LOCAL_APP_CONTROLLERS,
                'APP_PATH_LOCAL_APP_PRESETS' => APP_PATH_LOCAL_APP_PRESETS,
                'APP_PATH_LOCAL_PUBLIC_BUILD' => APP_PATH_LOCAL_PUBLIC_BUILD,
                'APP_PATH_LOCAL_STORAGE_LOGS' => APP_PATH_LOCAL_STORAGE_LOGS,
                'APP_PATH_LOCAL_STORAGE_DATAS' => APP_PATH_LOCAL_STORAGE_DATAS,
                'APP_PATH_LOCAL_VENDOR_AUTOLOAD' => APP_PATH_LOCAL_VENDOR_AUTOLOAD,
                'APP_PATH_URL' => APP_PATH_URL
            ],
        ];
    }
}
