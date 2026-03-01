<?php

namespace App\Modules\Category\Application\UseCase;

use App\Modules\Category\Domain\Service\CategoryService;

class ShowCategoriesForGallery
{
    public function __construct(private CategoryService $categoryService) {}

    public function execute(): array
    {
        // récupère les données métier
        return $this->categoryService->getCategoriesTree();
    }
}
