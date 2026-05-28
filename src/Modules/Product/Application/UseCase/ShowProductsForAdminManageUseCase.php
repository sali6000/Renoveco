<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;

final class ShowProductsForAdminManageUseCase
{

    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): array
    {
        return $this->productRepo->findAllWithLightRefs();
    }
}
