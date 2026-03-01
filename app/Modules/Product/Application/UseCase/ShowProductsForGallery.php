<?php

namespace App\Modules\Product\Application\UseCase;

use App\Modules\Product\Domain\Service\ProductService;

class ShowProductsForGallery
{
    public function __construct(private ProductService $productService) {}

    public function execute(): array
    {
        // récupère les données métier
        return $this->productService->getProductsDatasForGallery();
    }
}
