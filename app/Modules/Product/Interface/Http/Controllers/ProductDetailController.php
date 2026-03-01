<?php

namespace App\Modules\Product\Interface\Http\Controllers;

if (!defined('SECURE_CHECK'))
  die('Direct access not permitted');

use App\Exception\ServiceException;
use App\Modules\Product\Application\UseCase\ShowProductForDetail;
use App\Modules\Product\Interface\Http\Validator\ProductSlugValidatorInterface;
use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/product')]
class ProductDetailController extends BaseController
{
  public function __construct(
    private ProductSlugValidatorInterface $productSlugValidator,
    private ShowProductForDetail $showProductDetailUseCase
  ) {
    parent::__construct('Product');
  }

  #[Route('detail/{slug}', methods: ['GET'])]
  public function detail(string $slug): void
  {
    $this->productSlugValidator->validate($slug); // validation HTTP

    try {
      $this->set('model', $this->showProductDetailUseCase->execute($slug));
      $this->render(__FUNCTION__);
    } catch (ServiceException $e) {
      $this->handleException($e, __METHOD__ . ' → Service → ');
    } catch (\Throwable $e) {
      $this->handleException($e, __METHOD__ . ' → System → ');
    }
  }
}
