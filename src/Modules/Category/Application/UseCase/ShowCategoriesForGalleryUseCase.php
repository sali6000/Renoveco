<?php

namespace Src\Modules\Category\Application\UseCase;

use Src\Modules\Category\Application\Service\CategoryService;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

class ShowCategoriesForGalleryUseCase
{
    public function __construct(private CategoryService $categoryService) {}

    public function execute(): ResultUseCase
    {
        // récupère les données métier
        $datas = $this->categoryService->getCategoriesTree();
        return ResultUseCase::success($datas);
    }
}
