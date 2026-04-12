<?php

namespace Src\Modules\Steven\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/steven')]
final class StevenFernandoController extends BaseController
{
  /**
   * Affiche la page par défaut
   */
  #[Route('fernando', methods: ['GET'])]
  public function fernando()
  {
    $this->render('Steven/fernando.twig');
  }
}
