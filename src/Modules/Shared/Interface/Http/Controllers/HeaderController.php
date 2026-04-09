<?php

namespace Src\Modules\Shared\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use Config\AppConfig;
use Core\Support\DebugHelper;

class HeaderController extends BaseController
{
    private string $baseUri;

    public function __construct()
    {
        // Vérifie si l'hôte contient 'localhost' pour définir la base URI appropriée
        $this->baseUri = (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) ? "https://$_SERVER[HTTP_HOST]/" : AppConfig::getConst('URL_PATH');
    }

    public function index(): array
    {
        $information = [
            //'type' => 'warning',
            //'text' => "En construction !"
        ];

        return [
            'env' => AppConfig::getEnv('APP_ENV'),
            'information' => $information,
            'baseUri' => $this->baseUri,
            'userSession' => $_SESSION['user'] ?? null
        ];
    }
}
