<?php

declare(strict_types=1);

namespace Src\Modules\Product\Infrastructure\Persistence\Mysql;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Entity\ProductAttribute;
use Src\Modules\Product\Domain\Query\ProductQuery;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Core\Database\SqlHelpers;
use Src\Database\SchemaMysql;

class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($pdo);
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    public function findAll(ProductQuery $query): array
    {
        [$select, $params] = $this->buildSelect($query);

        $qb = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_PRODUCTS);

        $qb = $this->applyWhereConditions($qb, $query, $params);

        $qb = $qb->orderBy(SchemaMysql::PRODUCT_NAME, 'ASC');

        if ($query->limit !== null) {
            $qb = $qb->limit($query->limit);
        }

        if ($query->offset !== null) {
            $qb = $qb->offset($query->offset);
        }

        $result = $qb->executeAndFetchAll();

        return array_map(
            fn(array $row) => Product::fromArray($this->decodeJsonRelations($row, $query)),
            $result
        );
    }

    public function findOne(ProductQuery $query): ?Product
    {
        [$select, $params] = $this->buildSelect($query);

        $qb = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_PRODUCTS);

        $qb = $this->applyWhereConditions($qb, $query, $params);

        $result = $qb->executeAndFetchOne();

        return $result === null
            ? null
            : Product::fromArray($this->decodeJsonRelations($result, $query));
    }

    public function findAttributesByProductId(int $productId): array
    {
        $sql = "SELECT "
            . SchemaMysql::ATTRIBUTE_GROUPS_NAME . " AS group_name, "
            . SchemaMysql::ATTRIBUTE_GROUPS_DISPLAY_ORDER . ", "
            . SchemaMysql::ATTRIBUTES_NAME . " AS attribute_name, "
            . SchemaMysql::PRODUCT_ATTRIBUTE_VALUE
            . " FROM " . SchemaMysql::TABLE_PRODUCT_ATTRIBUTE
            . " JOIN " . SchemaMysql::TABLE_ATTRIBUTES
            . " ON " . SchemaMysql::ATTRIBUTES_ID . " = " . SchemaMysql::PRODUCT_ATTRIBUTE_ATTRIBUTE_ID
            . " JOIN " . SchemaMysql::TABLE_ATTRIBUTE_GROUPS
            . " ON " . SchemaMysql::ATTRIBUTE_GROUPS_ID . " = " . SchemaMysql::ATTRIBUTES_ATTRIBUTE_GROUP_ID
            . " WHERE " . SchemaMysql::PRODUCT_ATTRIBUTE_PRODUCT_ID . " = :product_id"
            . " ORDER BY " . SchemaMysql::ATTRIBUTE_GROUPS_DISPLAY_ORDER . " ASC";

        $result = $this->queryBuilder
            ->raw($sql, [':product_id' => $productId])
            ->executeAndFetchAll();

        return array_map([ProductAttribute::class, 'fromArray'], $result);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Construit le SELECT et retourne les params bindings associés.
     *
     * @return array{0: array, 1: array}
     */
    private function buildSelect(ProductQuery $query): array
    {
        $select = $query->columns !== null
            ? array_map(fn(string $col) => $col, $query->columns)
            : $this->getSchemeAllColumnsProduct();

        $params = [];

        if ($query->withImages) {
            $select[] = $query->onlyMainImage
                ? $this->getSubJsonMainImage()
                : $this->getSubJsonLightImages();
        }

        if ($query->withCategories) {
            $select[] = $this->getSubJsonLightCategories();
        }

        return [$select, $params];
    }

    private function applyWhereConditions(
        mixed $qb,
        ProductQuery $query,
        array $params
    ): mixed {
        if ($query->isActive !== null) {
            $active = $query->isActive ? 'TRUE' : 'FALSE';
            $qb = $qb->where(SchemaMysql::PRODUCT_IS_ACTIVE . " = {$active}");
        }

        if ($query->slug !== null) {
            $params[':slug'] = $query->slug;
            $qb = $qb->where(SchemaMysql::PRODUCT_SLUG . ' = :slug', $params);
        }

        if ($query->id !== null) {
            $params[':id'] = $query->id;
            $qb = $qb->where(SchemaMysql::PRODUCT_ID . ' = :id', $params);
        }

        return $qb;
    }

    private function decodeJsonRelations(array $row, ProductQuery $query): array
    {
        if ($query->withImages) {
            $row['images'] = json_decode($row['images'] ?? 'null', true) ?? [];
        }

        if ($query->withCategories) {
            $row['categories'] = json_decode($row['categories'] ?? 'null', true) ?? [];
        }

        return $row;
    }

    private function getSchemeAllColumnsProduct(): array
    {
        return [
            SchemaMysql::PRODUCT_ID,
            SchemaMysql::PRODUCT_REFERENCE,
            SchemaMysql::PRODUCT_SLUG,
            SchemaMysql::PRODUCT_NAME,
            SchemaMysql::PRODUCT_DESCRIPTION,
            SchemaMysql::PRODUCT_COMPOSITION,
            SchemaMysql::PRODUCT_USE_FOR,
            SchemaMysql::PRODUCT_IS_ACTIVE,
            SchemaMysql::PRODUCT_DEFAULT_SUPPLIER_ID,
            SchemaMysql::PRODUCT_CREATED_AT,
            SchemaMysql::PRODUCT_UPDATED_AT,
        ];
    }

    private function getSubJsonLightImages(): string
    {
        return SqlHelpers::jsonArrayAggreg(
            select: [SchemaMysql::PRODUCT_IMAGE_ID, SchemaMysql::PRODUCT_IMAGE_FILE_PATH],
            from: SchemaMysql::TABLE_PRODUCT_IMAGES,
            where: SchemaMysql::PRODUCT_IMAGE_PRODUCT_ID,
            equal: SchemaMysql::PRODUCT_ID,
            alias: 'images'
        );
    }

    private function getSubJsonMainImage(): string
    {
        return SqlHelpers::jsonArrayAggreg(
            select: [SchemaMysql::PRODUCT_IMAGE_ID, SchemaMysql::PRODUCT_IMAGE_FILE_PATH],
            from: SchemaMysql::TABLE_PRODUCT_IMAGES,
            where: SchemaMysql::PRODUCT_IMAGE_PRODUCT_ID,
            equal: SchemaMysql::PRODUCT_ID,
            extraWhere: SchemaMysql::PRODUCT_IMAGE_IS_MAIN . ' = TRUE',
            alias: 'images'
        );
    }

    private function getSubJsonLightCategories(): string
    {
        return SqlHelpers::jsonArrayAggreg(
            select: [SchemaMysql::CATEGORY_ID, SchemaMysql::CATEGORY_NAME],
            from: SchemaMysql::TABLE_CATEGORY_PRODUCT,
            joins: [
                'LEFT JOIN ' . SchemaMysql::TABLE_CATEGORIES
                    . ' ON ' . SchemaMysql::CATEGORY_ID
                    . ' = ' . SchemaMysql::CATEGORY_PRODUCT_CATEGORY_ID
            ],
            where: SchemaMysql::CATEGORY_PRODUCT_PRODUCT_ID,
            equal: SchemaMysql::PRODUCT_ID,
            alias: 'categories'
        );
    }
}
