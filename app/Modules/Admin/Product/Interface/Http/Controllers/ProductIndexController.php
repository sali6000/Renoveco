<?php

namespace App\Modules\Admin\Product\Interface\Http\Controllers;

use Core\BaseController;
use App\Modules\Category\Domain\Service\CategoryService;
use App\Modules\Product\Domain\Service\ProductService;
use Core\Routing\Attribute\Route;

#[Route('/admin/product')]
class ProductIndexController extends BaseController
{
    public function __construct(
        private ProductService $productService,
        private CategoryService $categoryService
    ) {
        parent::__construct('Admin/Product');
    }

    #[Route('', methods: ['GET'])]
    public function index()
    {
        $this->set('categories', $this->categoryService->getCategories());
        $this->set('products', $this->productService->getProductsAllDatas());
        $this->render();
    }
}
