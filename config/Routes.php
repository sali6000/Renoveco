<?php
// config/config.php

if (!defined('SECURE_CHECK'))
    die('access denied');

// Configuration personnels
define('BASE_ENTREPRISE_NAME', 'MonSite');
define('BASE_ENTREPRISE_HTTP', 'http://monsite.free.nf');
define('BASE_ENTREPRISE_MAIL', 'monSite@gmail.com');
define('BASE_ENTREPRISE_PRODUCT_URL_SEO', 'chassis-en-aluminium');
define('BASE_ENTREPRISE_TITLE', 'MonSite - Châssis en aluminium');
define('BASE_ENTREPRISE_TYPE', 'SRL');
define('BASE_NAME', '/MonSite'); // Nom du site, utilisé pour les URLs
define('BASE_NAME_CONTROLLER_ACTION_DEFAULT', 'index');
define('BASE_NAME_CONTROLLER_HOME', 'HomeController');

// Arborescence coté serveur
define('BASE_PATH', realPath(__DIR__ . '/..')); // C:\wamp64\www\MonSite\ - http://www.monsite.com/)
define('BASE_PATH_APP_MODELS', BASE_PATH . '/app/models/');
define('BASE_PATH_APP_VIEWS', BASE_PATH . '/app/views/');
define('BASE_PATH_APP_CONTROLLERS', BASE_PATH . '/app/controllers/');
define('BASE_PATH_APP_PRESETS', BASE_PATH . '/app/presets/');
define('BASE_PATH_STORAGE_LOGS', BASE_PATH . '/storage/logs/' . date('Y-m-d') . '-log.log');
define('BASE_PATH_UTILS', BASE_PATH . '/utils/');
define('BASE_PATH_STORAGE_DATAS', BASE_PATH . '/storage/datas/');
define('BASE_PATH_VENDOR_AUTOLOAD', BASE_PATH . '/vendor/autoload.php');

// Arborescence coté client
define('BASE_URL', get_base_url()); // http://localhost/monSite/ - http://www.monsite.com/
define('BASE_URL_MANAGE', BASE_URL . 'manage/');
define('BASE_URL_PUBLIC', BASE_URL); // Utilisé pour les conteneurs Docker, la racine du serveur web est le dossier public.
# define('BASE_URL_PUBLIC', BASE_URL . 'public/'); // Uniquement depuis WAMP. 
define('BASE_URL_PUBLIC_ASSETS', BASE_URL_PUBLIC . 'assets/');
define('BASE_URL_PUBLIC_ASSETS_CSS', BASE_URL_PUBLIC_ASSETS . 'css/');
define('BASE_URL_PUBLIC_ASSETS_CSS_TEMPLATES', BASE_URL_PUBLIC_ASSETS_CSS . 'templates/');
define('BASE_URL_PUBLIC_ASSETS_IMG', BASE_URL_PUBLIC_ASSETS . 'img/');
define('BASE_URL_PUBLIC_ASSETS_IMG_GALLERY', BASE_URL_PUBLIC_ASSETS_IMG . 'gallery/');
define('BASE_URL_PUBLIC_ASSETS_IMG_MATERIALS', BASE_URL_PUBLIC_ASSETS_IMG . 'materials/');
define('BASE_URL_PUBLIC_ASSETS_IMG_LOGOS', BASE_URL_PUBLIC_ASSETS_IMG . 'logos/');
define('BASE_URL_PUBLIC_ASSETS_JS', BASE_URL_PUBLIC_ASSETS . 'js/');
define('BASE_URL_PUBLIC_ASSETS_WEBM', BASE_URL_PUBLIC_ASSETS . 'webm/');

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
