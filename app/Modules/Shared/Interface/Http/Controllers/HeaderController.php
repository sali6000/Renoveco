<?php

namespace App\Modules\Shared\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use Config\AppConfig;

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
        $promo = [
            'type' => 'warning',
            'text' => "En construction !"
        ];
        $this->set('promo', $promo);
        $this->set('baseUri', $this->baseUri);
        $this->set('userSession', $_SESSION['user'] ?? null);
        return $this->data;
    }
}
