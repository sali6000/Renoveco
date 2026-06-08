<?php

namespace Src\Modules\Category\Domain\Repository;

use Src\Modules\Category\Domain\Entity\Category;
use Src\Modules\Category\Domain\Query\CategoryQuery;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function findAll(CategoryQuery $q): array;
    //public function save(Category $category): Category;
}
