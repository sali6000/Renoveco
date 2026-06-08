<?php

declare(strict_types=1);

namespace Src\Modules\Product\Infrastructure\Persistence\Mysql;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Core\Database\QueryBuilderInterface;
use Core\Database\Relations\ManyToManyRelation;
use Core\Database\Relations\OneToManyRelation;
use Core\Database\RepositoryMysql;
use Src\Database\SchemaMysql;

class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
    //----------------------------------------------------------------------------
    // PREPARE PROPERTIES SCHEMES :
    //----------------------------------------------------------------------------

    private const PRODUCT_COLUMNS = [
        SchemaMysql::PRODUCT_ID,
        SchemaMysql::PRODUCT_REFERENCE,
        SchemaMysql::PRODUCT_SLUG,
        SchemaMysql::PRODUCT_NAME,
        SchemaMysql::PRODUCT_DESCRIPTION,
        SchemaMysql::PRODUCT_COMPOSITION,
        SchemaMysql::PRODUCT_IS_ACTIVE,
        SchemaMysql::PRODUCT_SUBTITLE,
        SchemaMysql::PRODUCT_META_DESCRIPTION,
        SchemaMysql::PRODUCT_FEATURES,
        SchemaMysql::PRODUCT_IMAGE_ALT_TEXT,
        SchemaMysql::PRODUCT_IMAGE_FILE_PATH,
    ];

    private const IMAGE_COLUMNS = [
        SchemaMysql::PRODUCT_IMAGE_ID,
        SchemaMysql::PRODUCT_IMAGE_FILE_PATH,
        SchemaMysql::PRODUCT_IMAGE_ALT_TEXT,
        SchemaMysql::PRODUCT_IMAGE_IS_MAIN,
    ];

    private const CATEGORY_COLUMNS = [
        SchemaMysql::CATEGORY_ID,
        SchemaMysql::CATEGORY_NAME,
        SchemaMysql::CATEGORY_SLUG,
        SchemaMysql::CATEGORY_DESCRIPTION,
    ];

    //----------------------------------------------------------------------------
    // PREPARE METHODS SCHEMES :
    //----------------------------------------------------------------------------

    protected function getTable(): string
    {
        return SchemaMysql::TABLE_PRODUCTS;
    }

    protected function fromArray(array $row): Product
    {
        return Product::fromArray($row);
    }

    //----------------------------------------------------------------------------
    // EXECUTE QUERIES :
    //----------------------------------------------------------------------------

    // SELECT : FIND ONE 
    public function findOne(ProductQuery $q): ?Product
    {
        return $this->executeFindOne($q, self::PRODUCT_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

    // SELECT : FIND ALL
    public function findAll(ProductQuery $q): array
    {
        return $this->executeMany($q, self::PRODUCT_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

    //----------------------------------------------------------------------------
    // FILTERS MAKER :
    //----------------------------------------------------------------------------

    protected function applyFilters(QueryBuilderInterface $qb, ProductQuery $q): QueryBuilderInterface
    {
        // SLUG
        if ($q->slug !== null) $qb = $qb->where(SchemaMysql::PRODUCT_SLUG . ' = :slug', [':slug' => $q->slug]);

        // ID
        if ($q->id !== null) $qb = $qb->where(SchemaMysql::PRODUCT_ID . ' = :id', [':id' => $q->id]);

        // IS ACTIVE
        if ($q->isActive !== null) {
            $active = $q->isActive ? 'TRUE' : 'FALSE';
            $qb = $qb->where(SchemaMysql::PRODUCT_IS_ACTIVE . " = {$active}");
        }

        return $qb;
    }

    //----------------------------------------------------------------------------
    // RELATIONS MAKER :
    //----------------------------------------------------------------------------

    protected function applyRelations(ProductQuery $q): array
    {
        $relations = [];

        if ($q->withCategories) $relations[] = $this->makeCategoriesRelation();
        if ($q->withImages) $relations[] = $this->makeImagesRelation();

        return $relations;
    }

    /** 
     * Get columns from categories
     * 
     * @return ManyToManyRelation
     * */
    private function makeCategoriesRelation(array $columns = self::CATEGORY_COLUMNS): ManyToManyRelation
    {
        return SchemaMysql::productCategoriesRelation($columns);
    }

    /** 
     * Get columns from images
     * 
     * @return OneToManyRelation
     * */
    private function makeImagesRelation(array $columns = self::IMAGE_COLUMNS): OneToManyRelation
    {
        return SchemaMysql::productImagesRelation($columns);
    }
}
