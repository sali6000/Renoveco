<?php

namespace Src\Modules\Category\Infrastructure\Persistence\Mysql;

use Core\Database\RepositoryMysql;
use Core\Database\QueryBuilderInterface;
use Src\Modules\Category\Domain\Entity\Category;
use Src\Modules\Category\Domain\Query\CategoryQuery;
use Src\Modules\Category\Domain\Repository\CategoryRepositoryInterface;
use Src\Modules\Category\Infrastructure\Schema\CategorySchemaMysql;

class CategoryRepositoryMysql extends RepositoryMysql implements CategoryRepositoryInterface
{

    private const CATEGORY_COLUMNS = [
        CategorySchemaMysql::ID,
        CategorySchemaMysql::DESCRIPTION,
        CategorySchemaMysql::NAME,
        CategorySchemaMysql::PARENT_ID,
        CategorySchemaMysql::SLUG
    ];

    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $qb
    ) {
        parent::__construct($pdo, $qb);
    }

    //----------------------------------------------------------------------------
    // PREPARE METHODS SCHEMES :
    //----------------------------------------------------------------------------

    protected function getTable(): string
    {
        return CategorySchemaMysql::TABLE;
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
        if ($qp->slug !== null) $qb = $qb->where(CategorySchemaMysql::SLUG . ' = :slug', [':slug' => $qp->slug]);

        // ID
        if ($qp->id !== null) $qb = $qb->where(CategorySchemaMysql::ID . ' = :id', [':id' => $qp->id]);

        return $qb;
    }

    public function save(Category $category): Category
    {
        $data = [
            CategorySchemaMysql::NAME => $category->name,
            CategorySchemaMysql::SLUG => $category->slug,
            CategorySchemaMysql::DESCRIPTION => $category->description,
            CategorySchemaMysql::PARENT_ID => $category->parentId
        ];

        if ($category->id) {
            $ok = $this->qb
                ->update(CategorySchemaMysql::TABLE, $data, CategorySchemaMysql::ID . ' = :id', ['id' => $category->id]);
        } else {
            $stmt = $this->qb;
            $category->id = $stmt->insert(CategorySchemaMysql::TABLE, $data);
        }

        return $category;
    }
}
