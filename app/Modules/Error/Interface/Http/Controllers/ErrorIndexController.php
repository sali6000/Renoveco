<?php

namespace App\Modules\Error\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/error')]
class ErrorIndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct('Error');
    }

    /**
     * Affiche la page par défaut
     */
    #[Route('500', methods: ['GET'])]
    public function index()
    {
        $this->render('500');
    }
}
