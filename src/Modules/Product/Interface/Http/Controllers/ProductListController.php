<?php

namespace Src\Modules\Product\Interface\Http\Controllers;

if (!defined('SECURE_CHECK'))
  die('Direct access not permitted');

use Src\Exception\ServiceException;
use Src\Modules\Product\Application\UseCase\ShowProductsForGallery;
use Src\Modules\Category\Application\UseCase\ShowCategoriesForGallery;
use Src\Services\Schema\SchemaBuilder;
use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/product')]
class ProductListController extends BaseController
{
  public function __construct(
    private ShowProductsForGallery $showProductsForGallery,
    private ShowCategoriesForGallery $showCategoriesForGallery,
    private SchemaBuilder $schemaBuilder
  ) {}

  #[Route('list', methods: ['GET'])]
  public function list(): void
  {
    try {
      $products = $this->showProductsForGallery->execute();
      $categories = $this->showCategoriesForGallery->execute();

      $this->render('Product/list.twig', [
        'products' => $products,
        'categories' => $categories,
        'jsonLd' => $this->schemaBuilder->buildProductList($products)
      ]);
    } catch (ServiceException $e) {
      $this->handleException($e, __METHOD__ . ' → Service → ');
    } catch (\Throwable $e) {
      $this->handleException($e, __METHOD__ . ' → System → ');
    }
  }
}
