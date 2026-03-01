<?php

namespace App\Modules\Product\Interface\Http\Controllers;

if (!defined('SECURE_CHECK'))
  die('Direct access not permitted');

use App\Exception\ServiceException;
use App\Modules\Product\Application\UseCase\ShowProductsForGallery;
use App\Modules\Category\Application\UseCase\ShowCategoriesForGallery;
use App\Services\Schema\SchemaBuilder;
use Core\BaseController;
use Core\Routing\Attribute\Route;

/**
 * ProductController
 *
 * This controller handles product-related actions such as displaying product details and listing all products.
 */
#[Route('/product')]
class ProductListController extends BaseController
{

  public function __construct(
    private ShowProductsForGallery $showProductsForGallery,
    private ShowCategoriesForGallery $showCategoriesForGallery,
    private SchemaBuilder $schemaBuilder
  ) {
    parent::__construct('Product');
  }

  #[Route('list', methods: ['GET'])]
  public function list(): void
  {
    try {
      $products = $this->showProductsForGallery->execute();
      $categories = $this->showCategoriesForGallery->execute();

      $this->set('products', $products);
      $this->set('categories', $categories);
      $this->set('jsonLd', $this->schemaBuilder->buildProductList($products));
      $this->render(__FUNCTION__);
    } catch (ServiceException $e) {
      $this->handleException($e, __METHOD__ . ' → Service → ');
    } catch (\Throwable $e) {
      $this->handleException($e, __METHOD__ . ' → System → ');
    }
  }
}
