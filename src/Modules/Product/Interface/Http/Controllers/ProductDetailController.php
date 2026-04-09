<?php

namespace Src\Modules\Product\Interface\Http\Controllers;

if (!defined('SECURE_CHECK'))
  die('Direct access not permitted');

use Src\Exception\ServiceException;
use Src\Modules\Product\Application\UseCase\ShowProductForDetail;
use Src\Modules\Product\Interface\Http\Validator\ProductSlugValidatorInterface;
use Core\BaseController;
use Core\Routing\Attribute\Route;

#[Route('/product')]
class ProductDetailController extends BaseController
{
  public function __construct(
    private ProductSlugValidatorInterface $productSlugValidator,
    private ShowProductForDetail $showProductDetailUseCase
  ) {}

  #[Route('detail/{slug}', methods: ['GET'])]
  public function detail(string $slug): void
  {
    $this->productSlugValidator->validate($slug); // validation HTTP

    try {
      $this->render('Product/detail.twig', ['model' => $this->showProductDetailUseCase->execute($slug)]);
    } catch (ServiceException $e) {
      $this->handleException($e, __METHOD__ . ' → Service → ');
    } catch (\Throwable $e) {
      $this->handleException($e, __METHOD__ . ' → System → ');
    }
  }
}
