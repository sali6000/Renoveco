<?php

namespace App\Modules\Product\Repository;

use Core\Database\Repository;
use Core\Database\QueryBuilderInterface;
use App\Database\Schema;
use App\Modules\Product\Entity\ProductCategory;

class ProductCategoryRepository extends Repository
{
    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($pdo, ProductCategory::class);
    }

    /**
     * @return ProductCategory[]
     */
    public function getCategoriesTree(): array
    {
        $stmt = $this->queryBuilder
            ->selectFrom(SCHEMA::TABLE_CATEGORIES)
            ->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => ProductCategory::fromArray($row), $rows);;
    }



    public function save(ProductCategory $category): ProductCategory
    {
        $data = [
            SCHEMA::CATEGORY_NAME => $category->name,
            SCHEMA::CATEGORY_SLUG => $category->slug,
            SCHEMA::CATEGORY_DESCRIPTION => $category->description,
            SCHEMA::CATEGORY_PARENT_ID => $category->parentId
        ];

        if ($category->id) {
            $ok = $this->queryBuilder
                ->update(SCHEMA::TABLE_CATEGORIES, $data, SCHEMA::CATEGORY_ID . ' = :id', ['id' => $category->id]);
        } else {
            $ok = $this->queryBuilder
                ->insert(SCHEMA::TABLE_CATEGORIES, $data);
            if ($ok) {
                $category->id = (int) $this->queryBuilder->lastInsertId();
            }
        }

        return $category;
    }
}
