<?php

namespace App\Modules\Category\Domain\Repository;

use App\Modules\Category\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    public function deleteCategory(int $id): void;
    public function findAll(): array;
    public function save(Category $category): Category;
}
