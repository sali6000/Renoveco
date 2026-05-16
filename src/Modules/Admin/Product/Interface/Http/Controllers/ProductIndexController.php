<?php

namespace Src\Modules\Admin\Product\Interface\Http\Controllers;

use Core\BaseController;
use Src\Modules\Category\Domain\Service\CategoryService;
use Src\Modules\Product\Domain\Service\ProductService;
use Core\Routing\Attribute\Route;
use Src\Modules\Product\Application\UseCase\ShowProductsForAdminManage;

#[Route('/admin/product')]
final class ProductIndexController extends BaseController
{
    public function __construct(
        private ShowProductsForAdminManage $showProducts,
        private CategoryService $categoryService
    ) {
        parent::__construct('Admin/Product');
    }

    #[Route('', methods: ['GET'])]
    public function index()
    {
        $this->set('categories', $this->categoryService->getCategories());
        $this->set('products', $this->showProducts->execute());
        $this->render();
    }
}
