<?php

namespace App\Modules\Cgu\Controller;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\Controller;

class CguController extends Controller
{
    protected const VIEW = 'Cgu';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Affiche la page des Conditions Générales d'Utilisation
     */
    public function index()
    {
        $this->render();
    }
}
