<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Application\ViewModel\ProductDetailViewModel;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

class ShowProductForDetailUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(string $slug): ResultUseCase
    {
        $slug = strtolower($slug);

        $product = $this->productRepo->findBySlugWithLightRefs($slug);

        if (!$product) {
            ResultUseCase::failure("Aucun produit trouvé");
        }

        $product->attributes = $this->productRepo->findAttributesByProductId($product->id);

        // Produits similaires (même catégorie, hors produit courant, limite 4)
        $related = []; // $this->productRepo->findRelated(categoryId: $product->getCategory()->getId(),excludeSlug: $slug, limit: 4

        // Retourner le résultat en cas de succès
        return ResultUseCase::success(ProductDetailViewModel::fromEntity($product, $related));
    }
}
