<?php

namespace Src\Modules\About\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/about')]
final class AboutIndexController extends BaseController
{
  /**
   * Affiche la page par défaut
   */
  #[Route('', methods: ['GET'])]
  public function index()
  {
    $this->render("About/index.twig", ['canonical' => "https://renoveconstruct.be/about"]);
  }
}
