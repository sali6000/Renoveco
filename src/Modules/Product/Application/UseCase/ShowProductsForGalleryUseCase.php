<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class ShowProductsForGalleryUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): ResultUseCase
    {
        $datas = $this->productRepo->findAllForGallery();
        return ResultUseCase::success($datas);
    }
}
