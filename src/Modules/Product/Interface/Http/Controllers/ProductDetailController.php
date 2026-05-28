<?php

namespace Src\Modules\Product\Interface\Http\Controllers;

use Src\Modules\Product\Interface\Http\Validator\ProductSlugValidatorInterface;
use Core\BaseController;
use Core\Routing\Attribute\Route;
use Src\Modules\Product\Application\UseCase\ShowDemoProductForDetailUseCase;

#[Route('/product')]
class ProductDetailController extends BaseController
{
  public function __construct(
    private ProductSlugValidatorInterface $productSlugValidator,
    private ShowDemoProductForDetailUseCase $showDemoProductForDetail
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
    $result = $this->showDemoProductForDetail->execute($slug);

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
