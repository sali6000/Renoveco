<?php

namespace Src\Modules\Product\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Src\Modules\Product\Application\UseCase\ShowProductForDetailUseCase;
use Src\Modules\Product\Interface\Http\Validator\ProductSlugValidator;

#[Route('/product')]
class ProductDetailController extends BaseController
{
  public function __construct(
    private ProductSlugValidator $productSlugValidator,
    private ShowProductForDetailUseCase $showProductForDetail
  ) {}

  #[Route('detail/{slug}', methods: ['GET'])]
  public function detail(string $slug): void
  {
    // Validation du slug
    if (!$this->productSlugValidator->validate($slug)) {
      http_response_code(404);
      $this->render('Error/404.html');
      return;
    }

    // Récupération du produit
    $result = $this->showProductForDetail->execute($slug);

    // Retourner une erreur en cas d'échec de récupération
    if ($result->isFailure()) {
      http_response_code(404);
      $this->render('Error/404.html');
      return;
    }

    // Afficher le produit
    $this->render('Product/detail.twig', ['model' => $result->getData()]);
  }
}
