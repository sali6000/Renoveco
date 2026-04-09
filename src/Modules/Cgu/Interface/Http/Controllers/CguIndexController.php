<?php

namespace Src\Modules\Cgu\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Config\AppConfig;
use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/cgu')]
class CguIndexController extends BaseController
{
    #[Route('', methods: ['GET'])]
    public function index()
    {
        $this->render('Cgu/index.twig', [
            'base_entreprise_title' => AppConfig::getEnv('BASE_ENTREPRISE_NAME'),
            'base_url_privacy_policy' => 'cgu/policy',
            'base_entreprise_http' => AppConfig::getEnv('BASE_ENTREPRISE_WEBSITE'),
            'base_entreprise_mail' => AppConfig::getEnv('BASE_ENTREPRISE_MAIL')
        ]);
    }
}
