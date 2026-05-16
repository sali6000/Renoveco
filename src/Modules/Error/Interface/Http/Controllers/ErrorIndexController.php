<?php

namespace Src\Modules\Error\Interface\Http\Controllers;

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
