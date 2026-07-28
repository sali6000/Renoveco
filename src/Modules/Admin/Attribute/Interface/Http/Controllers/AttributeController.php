<?php

namespace Src\Modules\Admin\Attribute\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Src\Modules\Admin\Attribute\Application\UseCase\ShowAttributeTreeUseCase;

#[Route('/admin/attribute')]
final class AttributeController extends BaseController
{
    public function __construct(
        private ShowAttributeTreeUseCase $showAttributeTree
    ) {}

    #[Route('list', methods: ['GET'])]
    public function list()
    {
        $attributs = $this->showAttributeTree->execute()->getData();
        $this->render('Admin/Attribute/list.twig', ['attributs' => $attributs]);
    }

    #[Route('create', methods: ['GET'])]
    public function create()
    {
        $products = $this->showAttributeTree->execute()->getData();
        $this->render('Admin/Product/create.twig', ['products' => $products]);
    }
}
