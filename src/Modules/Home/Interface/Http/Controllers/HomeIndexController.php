<?php

namespace Src\Modules\Home\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/')]
class HomeIndexController extends BaseController
{
  #[Route('', methods: ['GET'])]
  public function index(): void
  {
    // Cache HTML côté client pendant 1 heure
    $this->setCache(3600);

    // Afficher la vue
    $this->render("Home/index.twig", [
      'canonical' => "https://renoveconstruct.be"
    ]);
  }
}
