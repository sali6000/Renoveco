<?php
// config/constants.php

use Config\AppConfig;
use Core\Support\DebugHelper;

if (!defined('SECURE_CHECK'))
    die('access denied');

/**
 * Chemins internes (à partir de la raçine).
 * 
 * ROOT_PATH: 
 * - Local <= C://.../MonSite/ 
 * - Conteneur PHP <= ./var/www/html/
 * - Production <= ./
 * */
define('ROOT_PATH', realPath(__DIR__ . '/..') . '/'); // ./ (chemin relatif à partir de ../config)
define('ROOT_PATH_CONFIG', ROOT_PATH . 'config/');
define('ROOT_PATH_SRC_MODULES', ROOT_PATH . 'src/Modules/');
define('SHARED_PATH', rtrim(AppConfig::getEnv('SHARED_PATH') ?: ROOT_PATH, '/') . '/'); // ./ (chemin relatif selon le SHARED_PATH .env)
define('ROOT_PATH_STORAGE_SECURE', SHARED_PATH . 'storage/secure/');
define('ROOT_PATH_STORAGE_CACHE', SHARED_PATH . 'storage/cache/');
define('ROOT_PATH_STORAGE_LOGS', SHARED_PATH . 'storage/logs/' . date('Y-m-d') . '-');
define('ROOT_PATH_TMP', SHARED_PATH . 'storage/tmp/');
define('ROOT_PATH_PUBLIC', getPublicPathContext(ROOT_PATH));
define('ROOT_PATH_PUBLIC_BUILD', ROOT_PATH_PUBLIC . 'build/');
define('ROOT_PATH_PUBLIC_UPLOADS', ROOT_PATH_PUBLIC . 'uploads/');


/**
 * Chemin externe (à partir du host).
 * 
 * URL_PATH: 
 * - Local <= https://localhost/ (local)
 * - Production <= https://www.monsite.com/
 * */
define('URL_PATH', get_base_url());

// Récupérer le chemin local du dossier public
function getPublicPathContext(string $basePath): string
{
    $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    if (is_dir($basePath . 'public_html')) {
        return $basePath . 'public_html' . DIRECTORY_SEPARATOR;
    }

    return $basePath . 'public' . DIRECTORY_SEPARATOR;
}


// Récupérer l'URL 
function get_base_url(): string
{
    $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('/public', '', dirname($scriptName));

    // Ensure path ends with a slash
    if (substr($path, -1) !== '/') {
        $path .= '/';
    }

    return $protocol . $host . $path;
}
