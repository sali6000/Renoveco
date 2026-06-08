<?php

declare(strict_types=1);

namespace Src\Modules\Product\Infrastructure\Persistence\Mysql;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Database\SchemaMysql;

final class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
    //----------------------------------------------------------------------------
    // PROPERTIES SCHEMES :
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

    private const STOCK_PRODUCT_COLUMNS = [
        SchemaMysql::STOCK_PRODUCT_ID,
        SchemaMysql::STOCK_PRODUCT_QUANTITY,
        SchemaMysql::STOCK_PRODUCT_STOCK_MINIMUM,
        SchemaMysql::STOCK_PRODUCT_STOCK_MAXIMUM
    ];

    /** @return string Schéma table product */
    protected function getTable(): string
    {
        return SchemaMysql::TABLE_PRODUCTS;
    }

    /** @return Product Produit obtenu depuis $row */
    protected function fromArray(array $row): Product
    {
        return Product::fromArray($row);
    }

    //----------------------------------------------------------------------------
    // EXECUTE QUERIES :
    //----------------------------------------------------------------------------

    public function findOne(ProductQuery $q): ?Product
    {
        return $this->executeFindOne($q, self::PRODUCT_COLUMNS, $this->applyFilters(...), $this->applyRelations(...));
    }

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

        if ($q->withCategories) $relations[] = SchemaMysql::productCategoriesRelation(self::CATEGORY_COLUMNS);
        if ($q->withImages) $relations[] = SchemaMysql::productImagesRelation(self::IMAGE_COLUMNS);
        if ($q->withStock) $relations[] = SchemaMysql::productStockRelation(self::STOCK_PRODUCT_COLUMNS);

        return $relations;
    }
}
