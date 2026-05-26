<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Application\ViewModel\ProductDetailViewModel;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\UseCaseResult;

class ShowProductForDetail
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(string $slug): UseCaseResult
    {
        $slug = strtolower($slug);

        $product = $this->productRepo->findBySlugWithLightRefs($slug);

        if (!$product) {
            UseCaseResult::failure("Aucun produit trouvé");
        }

        $product->attributes = $this->productRepo->findAttributesByProductId($product->id);

        // Produits similaires (même catégorie, hors produit courant, limite 4)
        $related = []; // $this->productRepo->findRelated(categoryId: $product->getCategory()->getId(),excludeSlug: $slug, limit: 4

        // Retourner le résultat en cas de succès
        return UseCaseResult::success(ProductDetailViewModel::fromEntity($product, $related));
    }
}
