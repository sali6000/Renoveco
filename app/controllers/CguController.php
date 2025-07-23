<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;

class CguController extends Controller
{
    public function __construct()
    {
        // Appeler explicitement le constructeur de la classe parente
        parent::__construct();
    }

    /**
     * Affiche la page des Conditions Générales d'Utilisation
     */
    public function index()
    {
        $this->view('cgu');
    }
}
