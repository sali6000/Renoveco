<?php

namespace Src\Modules\Product\Domain\Repository;

use Src\Modules\Product\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    //-----------------------------------------------
    // Récupérations d'éléments (return Product):
    //-----------------------------------------------
    public function findBySlugWithLightRefs(string $slug): ?Product;

    //-----------------------------------------------
    // Récupérations de listes (return Product[]):
    //-----------------------------------------------
    public function findAll(): array;
    public function findAllWithLightRefs(): array;
    public function findAttributesByProductId(int $productId): array;
    public function findAllForGallery(): array;
}
