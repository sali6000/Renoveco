<?php

namespace App\Modules\Product\Controller;

if (!defined('SECURE_CHECK'))
  die('Direct access not permitted');

use App\Exception\ServiceException;
use App\Modules\Product\Service\ProductService;
use App\Services\Schema\SchemaBuilder;
use Core\Controller;

/**
 * ProductController
 *
 * This controller handles product-related actions such as displaying product details and listing all products.
 */
class ProductController extends Controller
{
  protected const VIEW = 'Product';

  public function __construct(
    private ProductService $productService,
    private SchemaBuilder $schemaBuilder
  ) {
    parent::__construct();
  }

  public function detail(string $param): void
  {
    try {
      $this->set('model', $this->productService->getProductBySlug($param));
      $this->render(__FUNCTION__, $this->data);
    } catch (ServiceException $e) {
      $this->handleException($e, __METHOD__ . ' → Service → ');
    } catch (\Throwable $e) {
      $this->handleException($e, __METHOD__ . ' → System → ');
    }
  }

  public function list(): void
  {
    try {
      $products = $this->productService->getListProducts();
      $this->set('products', $products);
      $this->set('jsonLd', $this->schemaBuilder->buildProductList($products));
      $this->render(__FUNCTION__);
    } catch (ServiceException $e) {
      $this->handleException($e, __METHOD__ . ' → Service → ');
    } catch (\Throwable $e) {
      $this->handleException($e, __METHOD__ . ' → System → ');
    }
  }
}
