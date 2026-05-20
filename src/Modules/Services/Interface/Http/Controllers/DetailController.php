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

    if (!file_exists(AppConfig::getConst('ROOT_PATH_SRC_MODULES') . $template)) {
      throw new \Exception("Page introuvable");
    }
    $this->render("Services/{$normalizedSlug}.twig", [
      'current_page' => "services-{$normalizedSlug}",
      'canonical' => "https://renoveconstruct.be/services/{$normalizedSlug}"
    ]);
  }
}
