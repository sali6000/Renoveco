<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Domain\Service\ProductService;

class ShowProductsForGallery
{
    public function __construct(private ProductService $productService) {}

    public function execute(): array
    {
        // récupère les données métier
        return $this->productService->getProductsDatasForGallery();
    }
}
