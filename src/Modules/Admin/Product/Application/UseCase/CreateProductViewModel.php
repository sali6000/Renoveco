<?php

namespace Src\Modules\Admin\Product\Application\UseCase;

use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class CreateProductViewModel
{

    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): ResultUseCase
    {
        //$product = $this->productRepo->add();

        return ResultUseCase::success(/*$product*/);
    }
}
