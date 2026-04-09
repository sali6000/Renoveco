<?php

namespace Src\Modules\Home\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Detection\MobileDetect;

#[Route('/')]
class HomeIndexController extends BaseController
{
  #[Route('', methods: ['GET'])]
  public function index(): void
  {
    // Cache HTML côté client pendant 1 heure
    $this->setCache(3600);
    $detect = new MobileDetect();
    $isMobile = $detect->isMobile();
    $this->render("Home/index.twig", ['is_mobile' => $isMobile]);
  }
}
