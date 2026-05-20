<?php

namespace Src\Modules\Services\Interface\Http\Controllers;

use Config\AppConfig;
use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/services')]
final class DetailController extends BaseController
{
  /**
   * Affiche les détails dynamiques via slug
   */
  #[Route('{slug}', methods: ['GET'])]
  public function detail(string $slug)
  {
    self::validateSlug($slug);
    $normalizedSlug = self::normalizeViewSlug($slug);

    // Vérifier que le template existe
    $template = "Services/UI/Views/{$normalizedSlug}.twig";
    $path = AppConfig::getConst('ROOT_PATH_SRC_MODULES') . $template;

    if (!file_exists($path)) {
      http_response_code(404);
      $this->render('Error/404.twig');
      return;
    }

    $this->render("Services/{$normalizedSlug}.twig", [
      'current_page' => "services-{$normalizedSlug}",
      'canonical' => "https://renoveconstruct.be/services/{$normalizedSlug}"
    ]);
  }
}
