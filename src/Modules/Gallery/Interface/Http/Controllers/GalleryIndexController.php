<?php

namespace Src\Modules\Gallery\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/gallery')]
final class GalleryIndexController extends BaseController
{
  /**
   * Affiche la page par défaut
   */
  #[Route('index', methods: ['GET'])]
  public function index()
  {
    $this->render('Gallery/index.twig');
  }
}
