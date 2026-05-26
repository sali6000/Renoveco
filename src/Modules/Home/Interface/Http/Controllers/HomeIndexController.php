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
    // Afficher la vue
    $this->render("Home/index.twig", [
      'canonical' => "https://renoveconstruct.be"
    ]);
  }
}
