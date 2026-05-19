<?php

namespace Src\Modules\Product\Application\UseCase;

use Core\Logger\AccessLogger;
use Src\Exception\ServiceException;
use Src\Modules\Product\Application\ViewModel\ProductDetailViewModel;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;

class ShowProductForDetail
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(string $slug): ?ProductDetailViewModel
    {
        $slug = strtolower($slug);

        try {
            $product = $this->productRepo->findBySlugWithLightRefs($slug);

            if (!$product) {
                AccessLogger::log("Produit introuvable pour le slug : $slug", AccessLogger::LEVEL_ERROR);
                return null;
            }

            // Produits similaires (même catégorie, hors produit courant, limite 4)
            $related = [];

            /*$this->productRepo->findRelated(
            categoryId: $product->getCategory()->getId(),
            excludeSlug: $slug,
            limit: 4
        );*/

            return ProductDetailViewModel::fromEntity($product, $related);
        } catch (\Throwable $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] Erreur findBySlug($slug) : " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Erreur récupération produit (Code : $errorId).");
        }
    }
}
