<?php

namespace Src\Modules\Product\Application\UseCase;

use Core\Logger\AccessLogger;
use Src\Exception\ServiceException;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;

final class ShowProductsForAdminManage
{

    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): array
    {
        return $this->productRepo->findAllWithLightRefs();
    }
}
