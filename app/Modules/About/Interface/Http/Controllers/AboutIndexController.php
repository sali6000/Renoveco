<?php

namespace App\Modules\About\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/about')]
class AboutIndexController extends BaseController
{
  public function __construct()
  {
    parent::__construct('About');
  }

  /**
   * Affiche la page par défaut
   */
  #[Route('', methods: ['GET'])]
  public function index()
  {
    $this->render();
  }
}
