<?php

namespace Src\Modules\Admin\Product\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Src\Modules\Admin\Product\Application\UseCase\ShowProductsForAdminUseCase;

#[Route('/admin/product')]
final class ProductController extends BaseController
{
    public function __construct(
        private ShowProductsForAdminUseCase $showProductsForAdmin
    ) {}

    #[Route('list', methods: ['GET'])]
    public function index()
    {
        $products = $this->showProductsForAdmin->execute()->getData();
        $this->render('Admin/Product/list.twig', ['products' => $products]);
    }

    #[Route('create', methods: ['GET'])]
    public function create()
    {
        $products = $this->showProductsForAdmin->execute()->getData();
        $this->render('Admin/Product/create.twig', ['products' => $products]);
    }
}
