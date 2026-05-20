<?php

namespace Src\Modules\Cgu\Interface\Http\Controllers;

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
    $this->render('Cgu/policy.twig', ['canonical' => "https://renoveconstruct.be/cgu/policy"]);
  }
}
