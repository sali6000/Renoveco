<?php

namespace Src\Modules\Product\Interface\Http\Controllers;

use Src\Modules\Product\Application\UseCase\ShowProductsForGalleryUseCase;
use Src\Modules\Category\Application\UseCase\ShowCategoriesForGalleryUseCase;
use Src\Services\Schema\SchemaBuilder;
use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\DebugHelper;

#[Route('/product')]
class ProductListController extends BaseController
{
  public function __construct(
    private ShowProductsForGalleryUseCase $showProductsForGallery,
    private ShowCategoriesForGalleryUseCase $showCategoriesForGallery,
    private SchemaBuilder $schemaBuilder
  ) {}

  #[Route('list', methods: ['GET'])]
  public function list(): void
  {
    // Récupération des produits
    $products = $this->showProductsForGallery->execute()->getData();

    // Récupération des catégories
    $categories = $this->showCategoriesForGallery->execute()->getData();

    // Afficher la vue
    $this->render('Product/list.twig', [
      'products' => $products,
      'categories' => $categories,
      'jsonLd' => $this->schemaBuilder->buildProductList($products)
    ]);
  }
}
