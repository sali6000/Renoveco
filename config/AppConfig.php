<?php

namespace Config;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

class AppConfig
{
    private static bool $configLoaded = false;
    private static ?array $whitelist = null;

    public static function getBool(string $key, bool $default = false): bool
    {
        return filter_var(self::getEnv($key, $default), FILTER_VALIDATE_BOOLEAN);
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

    // Charge la configuration des constantes si elle n'est pas déjà chargée
    // Cela permet de s'assurer que les constantes et chemins sont définis avant utilisation
    private static function loadConfig(): void
    {
        if (!self::$configLoaded) {
            require_once __DIR__ . '/constants.php';
            self::$configLoaded = true;
        }
    }

    // Permet de récupérer un chemin défini dans les constantes 
    // Si le chemin n'est pas défini, retourne null
    public static function getConst(string $key): mixed
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
            'entrepriseTitle' => self::getEnv('BASE_ENTREPRISE_TITLE'),
            'entrepriseMail'  => self::getEnv('BASE_ENTREPRISE_MAIL'),
            'entrepriseHttp'  => self::getEnv('BASE_ENTREPRISE_WEBSITE'),
            // ...
        ];
    }
}
