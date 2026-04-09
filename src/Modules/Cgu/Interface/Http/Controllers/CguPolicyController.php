<?php

namespace Src\Modules\Cgu\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/cgu')]
final class CguPolicyController extends BaseController
{
  /**
   * Affiche la page par défaut
   */
  #[Route('policy', methods: ['GET'])]
  public function policy()
  {
    $this->render('Cgu/policy.twig');
  }
}
