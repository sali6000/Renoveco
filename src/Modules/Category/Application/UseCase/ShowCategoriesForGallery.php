<?php

namespace Src\Modules\Category\Application\UseCase;

use Src\Modules\Category\Domain\Service\CategoryService;

class ShowCategoriesForGallery
{
    public function __construct(private CategoryService $categoryService) {}

    public function execute(): array
    {
        // récupère les données métier
        return $this->categoryService->getCategoriesTree();
    }
}
