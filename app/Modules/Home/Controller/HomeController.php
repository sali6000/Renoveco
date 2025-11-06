<?php

namespace App\Modules\Home\Controller;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\Controller;

class HomeController extends Controller
{
  protected const VIEW = 'Home';

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Affiche la page d'accueil
   */
  public function index()
  {
    // Cache HTML côté client pendant 1 heure
    $this->setCache(3600);

    $this->render();
  }
}
