<?php
// config/constants.php

if (!defined('SECURE_CHECK'))
    die('access denied');

// Configuration de l'environnement
// Ces constantes sont utilisées dans l'application pour définir les chemins, URLs, etc.
// Elles sont chargées depuis le fichier .env ou définies par défaut.
// La page core/View.php utilise ces constantes pour initialiser Twig et rendre les vues.

// Configuration personnels
define('APP_INFO_ENTREPRISE_NAME', $_ENV['BASE_ENTREPRISE_NAME'] ?? 'MonSite');
define('APP_INFO_ENTREPRISE_HTTP', $_ENV['BASE_ENTREPRISE_HTTP'] ?? 'http://monsite.free.nf');
define('APP_INFO_ENTREPRISE_MAIL', $_ENV['BASE_ENTREPRISE_MAIL'] ?? 'monSite@gmail.com');
define('APP_INFO_ENTREPRISE_PRODUCT_URL_SEO', $_ENV['BASE_ENTREPRISE_PRODUCT_URL_SEO'] ?? 'Produits-en-tous-genres');
define('APP_INFO_ENTREPRISE_TITLE', $_ENV['BASE_ENTREPRISE_TITLE'] ?? 'Mon site en développement');
define('APP_INFO_ENTREPRISE_TYPE', $_ENV['BASE_ENTREPRISE_TYPE'] ?? 'SRL');

// Configurations par défaut de l'application (voir Router.php)
define('APP_PARAM_NAME', $_ENV['BASE_NAME'] ?? '/MonSite'); // Nom du site, utilisé pour les URLs
define('APP_PARAM_CONTROLLER_DEFAULT', $_ENV['BASE_NAME_CONTROLLER_HOME'] ?? 'HomeController');
define('APP_PARAM_ACTION_DEFAULT', $_ENV['BASE_NAME_CONTROLLER_ACTION_DEFAULT'] ?? 'index');

// Chemins local (serveur - C:\...)
define('APP_PATH_LOCAL', realPath(__DIR__ . '/..')); // C:\Users\***\Docker-Projets\MonSiteV2.4\ - http://www.monsite.com/
define('APP_PATH_LOCAL_APP_CACHE_TWIG', APP_PATH_LOCAL . '/app/cache/twig');
define('APP_PATH_LOCAL_APP_MODELS', APP_PATH_LOCAL . '/app/models/');
define('APP_PATH_LOCAL_APP_MODULES', APP_PATH_LOCAL . '/app/modules/');
define('APP_PATH_LOCAL_APP_CONTROLLERS', APP_PATH_LOCAL . '/app/controllers/');
define('APP_PATH_LOCAL_APP_PRESETS', APP_PATH_LOCAL . '/app/presets/');
define('APP_PATH_LOCAL_PUBLIC_BUILD', APP_PATH_LOCAL . '/public/build/');
define('APP_PATH_LOCAL_PUBLIC_UPLOADS', APP_PATH_LOCAL . '/public/uploads/');
define('APP_PATH_LOCAL_STORAGE_LOGS', APP_PATH_LOCAL . '/storage/logs/' . date('Y-m-d') . '-');
define('APP_PATH_LOCAL_STORAGE_DATAS', APP_PATH_LOCAL . '/storage/datas/');
define('APP_PATH_LOCAL_VENDOR_AUTOLOAD', APP_PATH_LOCAL . '/vendor/autoload.php');

// Chemin public (client - http://...)
define('APP_PATH_URL', get_base_url()); // http(s)://localhost/ - http(s)://www.monsite.com/

function get_base_url()
{
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
