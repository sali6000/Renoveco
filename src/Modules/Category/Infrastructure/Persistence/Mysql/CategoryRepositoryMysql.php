<?php

namespace Src\Modules\Category\Infrastructure\Persistence\Mysql;

use Core\Database\RepositoryMysql;
use Core\Database\QueryBuilderInterface;
use Src\Database\SchemaMysql;
use Src\Modules\Category\Domain\Entity\Category;
use Src\Modules\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Database\SqlAggregator;

class CategoryRepositoryMysql extends RepositoryMysql implements CategoryRepositoryInterface
{
    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($pdo);
    }

    /**
     * Faire un selectJoinManyToMany pour faire le lien avec la table intermédiaire
     */
    public function buildLightCategoriesSub(array $categoryColumns): string
    {
        $sqlAggregator = new SqlAggregator();

        // Return GROUP_CONCAT(DISTINCT c.id, ':',c.name SEPARATOR "|") AS categories
        return  $sqlAggregator
            ->column($categoryColumns)
            ->separator('|')
            ->distinct()
            ->groupConcat()
            ->alias('categories')
            ->toSql();
    }


    public function deleteCategory(int $id): void
    {
        $this->delete(SchemaMysql::TABLE_CATEGORIES, SchemaMysql::CATEGORY_ID, $id);
    }

    /**
     * @return Category[]
     */
    public function findAll(): array
    {
        $stmt = $this->queryBuilder
            ->select()
            ->from(SchemaMysql::TABLE_CATEGORIES)
            ->executeAndFetchAll();
        return array_map(fn($row) => Category::fromArray($row), $stmt);
    }

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
    }
}
