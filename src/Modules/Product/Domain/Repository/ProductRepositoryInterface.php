<?php

declare(strict_types=1);

namespace Src\Modules\Product\Domain\Repository;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Query\ProductQuery;

interface ProductRepositoryInterface
{
    /** @return Product */
    public function findProduct(ProductQuery $query): ?Product;

    /** @return Product[] */
    public function findProducts(ProductQuery $query): array;
}
