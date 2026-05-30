<?php
// config/constants.php

use Config\AppConfig;

/**
 * Chemins internes (à partir de la raçine).
 * 
 * ROOT_PATH: 
 * - Local          <= C://.../MonSite/ 
 * - Conteneur PHP  <= /var/www/html/
 * - Production     <= /home/renoveco
 * */
define('ROOT_PATH', realPath(__DIR__ . '/..') . '/');
define('ROOT_PATH_CONFIG', ROOT_PATH . 'config/');
define('ROOT_PATH_SRC_MODULES', ROOT_PATH . 'src/Modules/');

/**
 * SHARED_PATH : dossier hors web pour les fichiers non accessibles publiquement.
 * Défini via .env (prod uniquement). En local, fallback sur ROOT_PATH.
 *
 * - Local      <= ROOT_PATH (./storage/ etc. à la racine du projet)
 * - Production <= /home/renoveco/shared/
 */
define('SHARED_PATH', rtrim(AppConfig::getEnv('SHARED_PATH') ?: ROOT_PATH, '/') . '/');
define('ROOT_PATH_STORAGE_SECURE', SHARED_PATH . 'storage/secure/');
define('ROOT_PATH_STORAGE_CACHE', SHARED_PATH . 'storage/cache/');
define('ROOT_PATH_STORAGE_LOGS', SHARED_PATH . 'storage/logs/' . date('Y-m-d') . '-');
define('ROOT_PATH_TMP', SHARED_PATH . 'storage/tmp/');

/**
 * ROOT_PATH_PUBLIC : dossier web accessible publiquement.
 * Résolu automatiquement selon l'environnement via getPublicPathContext().
 *
 * - Local      <= ROOT_PATH/public/
 * - Production <= /home/renoveco/public_html/
 */
define('ROOT_PATH_PUBLIC', getPublicPathContext(ROOT_PATH));
define('ROOT_PATH_PUBLIC_BUILD', ROOT_PATH_PUBLIC . 'build/');

/**
 * Chemin externe (à partir du host).
 * 
 * URL_PATH: 
 * - Local <= https://localhost/ (local)
 * - Production <= https://www.monsite.com/
 * */
define('URL_PATH', get_base_url());
define('URL_PATH_UPLOADS', URL_PATH . 'uploads/'); // URL publique vers les fichiers uploadés

// Résoudre le chemin du dossier public selon l'environnement
function getPublicPathContext(string $basePath): string
{
    $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    if (is_dir($basePath . 'public_html')) {
        return $basePath . 'public_html' . DIRECTORY_SEPARATOR;
    }

    return $basePath . 'public' . DIRECTORY_SEPARATOR;
}

// Construire l'URL de base de l'application 
function get_base_url(): string
{
    $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('/public', '', dirname($scriptName));

    if (!str_ends_with($path, '/')) {
        $path .= '/';
    }

    return $protocol . $host . $path;
}
