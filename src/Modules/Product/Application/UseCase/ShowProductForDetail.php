<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Domain\Service\ProductService;
use Src\Modules\Product\Domain\Entity\Product;

class ShowProductForDetail
{
    public function __construct(private ProductService $productService) {}

    public function execute(string $slugUri): ?Product
    {
        // validation canonique = métier
        $canonicalSlug = strtolower($slugUri); // prépare le slug pour la DB

        // récupère les données métier
        return $this->productService->getProductDatasForDetail($canonicalSlug);
    }
}
