<?php

namespace Src\Modules\Admin\Product\Application\UseCase;

use Src\Modules\Admin\Product\Application\ViewModel\AdminProductViewModel;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class ShowProductsForAdminUseCase
{

    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): ResultUseCase
    {
        $products = $this->productRepo->findProducts(new ProductQuery(withCategories: true, withAttributes: true, withStock: true));

        $vms = [];

        foreach ($products as $product) {
            $vm = new AdminProductViewModel();
            $vm->name = $product->name;
            $vm->quantity = $product->stockProduct->quantity;
            $vm->available = $vm->quantity > 0;
            $vms[] = $vm;
        }

        return ResultUseCase::success($vms);
    }
}
