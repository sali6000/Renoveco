<?php

namespace App\Modules\Home\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/')]
class HomeIndexController extends BaseController
{
  public function __construct()
  {
    parent::__construct('Home');
  }

  /**
   * Affiche la page par défaut
   */
  #[Route('', methods: ['GET'])]
  public function index(): void
  {
    // Cache HTML côté client pendant 1 heure
    $this->setCache(3600);
    $this->render();
  }
}
