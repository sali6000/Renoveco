<?php

namespace App\Modules\Product\Domain\Repository;

use App\Modules\Product\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    //-----------------------------------------------
    // Récupérations d'éléments (return Product):
    //-----------------------------------------------
    public function findBySlugWithLightRefs(string $slug): ?Product;

    //-----------------------------------------------
    // Récupérations de listes (return Product[]):
    //-----------------------------------------------

    /**
     * @return Product[]
     */
    public function findAll(): array;

    /**
     * @return Product[]
     */
    public function findAllWithLightRefs(): array;

    /**
     * @return Product[]
     */
    public function findAllForGallery(): array;
}
