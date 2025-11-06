<?php

namespace App\Modules\About\Controller;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\Controller;

class AboutController extends Controller
{
  protected const VIEW = 'About';

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Affiche la page par défaut
   */
  public function index()
  {
    $this->render();
  }
}
