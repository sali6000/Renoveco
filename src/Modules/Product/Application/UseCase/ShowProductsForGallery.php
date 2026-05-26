<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\UseCaseResult;

final class ShowProductsForGallery
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): UseCaseResult
    {
        $datas = $this->productRepo->findAllForGallery();
        return UseCaseResult::success($datas);
    }
}
