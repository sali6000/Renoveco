<?php
// config/constants.php

if (!defined('SECURE_CHECK'))
    die('access denied');

// Local : var/www/... ou bien C:/...
define('LOCAL_PATH', realPath(__DIR__ . '/..'));
define('LOCAL_PATH_APP_CACHE_TWIG', LOCAL_PATH . '/app/cache/twig');
define('LOCAL_PATH_APP_MODULES', LOCAL_PATH . '/app/Modules/');
define('LOCAL_PATH_STORAGE_CACHES', LOCAL_PATH . '/storage/cache/');
define('LOCAL_PATH_STORAGE_LOGS', LOCAL_PATH . '/storage/logs/' . date('Y-m-d') . '-');
define('LOCAL_PATH_CONFIG', LOCAL_PATH . '/config/');

// Public : public/ ou public_html/
define('PUBLIC_PATH', getPublicPathContext(LOCAL_PATH));
define('PUBLIC_PATH_BUILD', PUBLIC_PATH . '/build/');
define('PUBLIC_PATH_UPLOADS', PUBLIC_PATH . '/uploads/');

// URL : localhost/ ou bien monsite.com/
define('URL_PATH', get_base_url());

// Récupérer le chemin local du dossier public
function getPublicPathContext(string $basePath): string
{
    $publicDir = is_dir($basePath . '/public_html') ? 'public_html' : 'public';
    return $basePath . '/' . $publicDir;
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
