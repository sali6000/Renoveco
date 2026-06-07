<?php

namespace Src\Modules\Product\Application\UseCase;

use Core\Support\DebugHelper;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class ShowProductsForGalleryUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): ResultUseCase
    {
        $datas = $this->productRepo->findAll(new ProductQuery(withImages: true, withCategories: true));

        DebugHelper::verboseServer($datas);
        return ResultUseCase::success($datas);
    }
}
