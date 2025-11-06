<?php

namespace App\Modules\Shared\Controller;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\Controller;
use Config\AppConfig;

class HeaderController extends Controller
{
    private $baseUri;

    public function __construct()
    {
        // Vérifie si l'hôte contient 'localhost' pour définir la base URI appropriée
        $this->baseUri = (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) ? "http://$_SERVER[HTTP_HOST]/" : AppConfig::getPath('APP_PATH_URL');
    }

    public function index(): array
    {
        $this->set('baseUri', $this->baseUri);
        $this->set('userSession', $_SESSION['user'] ?? null);
        return $this->data;
    }
}
