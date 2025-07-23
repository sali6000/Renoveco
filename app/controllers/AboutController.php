<?php

namespace App\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use App\Core\Controller;

class AboutController extends Controller
{
  public function __construct()
  {
    // Appeler explicitement le constructeur de la classe parente
    parent::__construct();
  }

  /**
   * Affiche la page "À propos"
   */
  public function index()
  {
    $this->view('about');
  }
}
