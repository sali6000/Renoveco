<?php

namespace Src\Modules\Product\Application\UseCase;

use Src\Database\SchemaMysql;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class ShowProductsForGalleryUseCase
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): ResultUseCase
    {
        $datas = $this->productRepo->findAll(new ProductQuery(
            columns: [
                SchemaMysql::PRODUCT_NAME,
                SchemaMysql::PRODUCT_REFERENCE,
                SchemaMysql::PRODUCT_SLUG
            ],
            withImages: true
        ));
        return ResultUseCase::success($datas);
    }
}
