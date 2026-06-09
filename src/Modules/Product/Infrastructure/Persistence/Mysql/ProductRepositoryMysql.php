<?php

declare(strict_types=1);

namespace Src\Modules\Product\Infrastructure\Persistence\Mysql;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Src\Modules\Category\Infrastructure\Schema\CategorySchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductCategorySchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductImageSchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductSchemaMysql;
use Src\Modules\Product\Infrastructure\Schema\ProductStockSchemaMysql;

final class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
    //----------------------------------------------------------------------------
    // PROPERTIES SCHEMES :
    //----------------------------------------------------------------------------
    private const PRODUCT_COLUMNS = [
        ProductSchemaMysql::ID,
        ProductSchemaMysql::REFERENCE,
        ProductSchemaMysql::SLUG,
        ProductSchemaMysql::NAME,
        ProductSchemaMysql::DESCRIPTION,
        ProductSchemaMysql::COMPOSITION,
        ProductSchemaMysql::IS_ACTIVE,
        ProductSchemaMysql::SUBTITLE,
        ProductSchemaMysql::META_DESCRIPTION,
        ProductSchemaMysql::FEATURES,
        ProductImageSchemaMysql::ALT_TEXT,
        ProductImageSchemaMysql::FILE_PATH,
    ];

    private const IMAGE_COLUMNS = [
        ProductImageSchemaMysql::ID,
        ProductImageSchemaMysql::FILE_PATH,
        ProductImageSchemaMysql::ALT_TEXT,
        ProductImageSchemaMysql::IS_MAIN,
    ];

    private const CATEGORY_COLUMNS = [
        CategorySchemaMysql::ID,
        CategorySchemaMysql::NAME,
        CategorySchemaMysql::SLUG,
        CategorySchemaMysql::DESCRIPTION,
    ];

    private const STOCK_PRODUCT_COLUMNS = [
        ProductStockSchemaMysql::ID,
        ProductStockSchemaMysql::QUANTITY,
        ProductStockSchemaMysql::STOCK_MINIMUM,
        ProductStockSchemaMysql::STOCK_MAXIMUM
    ];

    /** @return string Schéma table product */
    protected function getTable(): string
    {
        return ProductSchemaMysql::TABLE;
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
        if ($q->slug !== null) $qb = $qb->where(ProductSchemaMysql::SLUG . ' = :slug', [':slug' => $q->slug]);
        if ($q->id !== null) $qb = $qb->where(ProductSchemaMysql::ID . ' = :id', [':id' => $q->id]);
        if ($q->isActive !== null) {
            $active = $q->isActive ? 'TRUE' : 'FALSE';
            $qb = $qb->where(ProductSchemaMysql::IS_ACTIVE . " = {$active}");
        }

        return $qb;
    }

    //----------------------------------------------------------------------------
    // RELATIONS MAKER :
    //----------------------------------------------------------------------------

    protected function applyRelations(ProductQuery $q): array
    {
        $relations = [];

        if ($q->withCategories) $relations[] = ProductCategorySchemaMysql::withCategories(self::CATEGORY_COLUMNS);
        if ($q->withImages) $relations[] = ProductImageSchemaMysql::withImages(self::IMAGE_COLUMNS);
        if ($q->withStock) $relations[] = ProductStockSchemaMysql::withStock(self::STOCK_PRODUCT_COLUMNS);

        return $relations;
    }
}
