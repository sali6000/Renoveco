<?php

namespace Src\Modules\Category\Infrastructure\Persistence\Mysql;

use Core\Database\RepositoryMysql;
use Core\Database\QueryBuilderInterface;
use Src\Database\SchemaMysql;
use Src\Modules\Category\Domain\Entity\Category;
use Src\Modules\Category\Domain\Query\CategoryQuery;
use Src\Modules\Category\Domain\Repository\CategoryRepositoryInterface;

class CategoryRepositoryMysql extends RepositoryMysql implements CategoryRepositoryInterface
{

    private const CATEGORY_COLUMNS = [
        SchemaMysql::CATEGORY_ID,
        SchemaMysql::CATEGORY_DESCRIPTION,
        SchemaMysql::CATEGORY_NAME,
        SchemaMysql::CATEGORY_PARENT_ID,
        SchemaMysql::CATEGORY_SLUG
    ];


    //----------------------------------------------------------------------------
    // PREPARE METHODS SCHEMES :
    //----------------------------------------------------------------------------

    protected function getTable(): string
    {
        return SchemaMysql::TABLE_CATEGORIES;
    }

    protected function fromArray(array $row): Category
    {
        return Category::fromArray($row);
    }


    //----------------------------------------------------------------------------
    // EXECUTE QUERIES :
    //----------------------------------------------------------------------------

    /**
     * @return Category[]
     */
    public function findAll(CategoryQuery $q): array
    {
        return $this->executeMany($q, self::CATEGORY_COLUMNS, $this->applyFilters(...));
    }

    // PREPARE FILTERS (conditions)
    private function applyFilters(QueryBuilderInterface $qb, CategoryQuery $qp): QueryBuilderInterface
    {
        // SLUG
        if ($qp->slug !== null) $qb = $qb->where(SchemaMysql::CATEGORY_SLUG . ' = :slug', [':slug' => $qp->slug]);

        // ID
        if ($qp->id !== null) $qb = $qb->where(SchemaMysql::CATEGORY_ID . ' = :id', [':id' => $qp->id]);

        return $qb;
    }



    /*
    public function save(Category $category): Category
    {
        $data = [
            SchemaMysql::CATEGORY_NAME => $category->name,
            SchemaMysql::CATEGORY_SLUG => $category->slug,
            SchemaMysql::CATEGORY_DESCRIPTION => $category->description,
            SchemaMysql::CATEGORY_PARENT_ID => $category->parentId
        ];

        if ($category->id) {
            $ok = $this->queryBuilder
                ->update(SchemaMysql::TABLE_CATEGORIES, $data, SchemaMysql::CATEGORY_ID . ' = :id', ['id' => $category->id]);
        } else {
            $stmt = $this->queryBuilder;
            $category->id = $stmt->insert(SchemaMysql::TABLE_CATEGORIES, $data);
        }

        return $category;
    }*/
}
