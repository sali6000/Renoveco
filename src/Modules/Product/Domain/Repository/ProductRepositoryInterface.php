<?php

declare(strict_types=1);

namespace Src\Modules\Product\Domain\Repository;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Entity\ProductAttribute;
use Src\Modules\Product\Domain\Query\ProductQuery;

interface ProductRepositoryInterface
{
    public function findOne(ProductQuery $query): ?Product;

    /** @return Product[] */
    public function findAll(ProductQuery $query): array;
}
