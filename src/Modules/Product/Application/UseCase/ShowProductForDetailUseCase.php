<?php

namespace Src\Modules\Product\Application\UseCase;

use Core\Support\DebugHelper;
use Src\Modules\Product\Application\ViewModel\ProductDetailViewModel;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

class ShowProductForDetailUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(string $slug): ResultUseCase
    {
        $slug = strtolower($slug);

        $product = $this->productRepo->findOne(new ProductQuery(slug: $slug));

        DebugHelper::verboseServer($product);

        if (!$product) {
            ResultUseCase::failure("Aucun produit trouvé");
        }

        // Produits similaires (même catégorie, hors produit courant, limite 4)
        $related = []; // $this->productRepo->findRelated(categoryId: $product->getCategory()->getId(),excludeSlug: $slug, limit: 4

        // Retourner le résultat en cas de succès
        return ResultUseCase::success(ProductDetailViewModel::fromEntity($product, $related));
    }
}
