<?php

namespace App\Modules\Cgu\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Config\AppConfig;
use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/cgu')]
class CguIndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct('Cgu');
    }

    /**
     * Affiche la page des Conditions Générales d'Utilisation
     */
    #[Route('', methods: ['GET'])]
    public function index()
    {
        $this->set('base_entreprise_title', AppConfig::getEnv('BASE_ENTREPRISE_TITLE'));
        $this->set('base_entreprise_http', AppConfig::getEnv('BASE_ENTREPRISE_WEBSITE'));
        $this->set('base_entreprise_mail', AppConfig::getEnv('BASE_ENTREPRISE_MAIL'));
        $this->render();
    }
}
