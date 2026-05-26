<?php

namespace Src\Modules\Product\Infrastructure\Persistence\Mysql;

use Src\Modules\Product\Domain\Entity\Product;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Core\Database\AggregateTransformer;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Core\Database\SqlAggregator;
use Core\Database\SqlHelpers;
use Src\Database\SchemaMysql;
use Src\Modules\Product\Domain\Entity\ProductAttribute;

class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $queryBuilder,
        private SqlAggregator $sqlAggregator,
        private AggregateTransformer $aggTransform
    ) {
        parent::__construct($pdo);
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
            SchemaMysql::PRODUCT_UPDATED_AT
        ];
    }

    /*
        COALESCE((
                    SELECT JSON_ARRAYAGG(JSON_OBJECT('id', pi.id, 'file_path', pi.file_path))
                    FROM product_images pi
                    WHERE pi.product_id = p.id

                ), JSON_ARRAY()) AS images
    */
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

    /*
        COALESCE((
                    SELECT JSON_ARRAYAGG(JSON_OBJECT('id', c.id, 'name', c.name))
                    FROM category_product cp
                    LEFT JOIN categories c ON c.id = cp.category_id
                    WHERE cp.product_id = p.id

                ), JSON_ARRAY()) AS categories
    */
    private function getSubJsonLightCategories(): string
    {
        return SqlHelpers::jsonArrayAggreg(
            select: [SchemaMysql::CATEGORY_ID, SchemaMysql::CATEGORY_NAME],
            from: SchemaMysql::TABLE_PIVOT_CATEGORY_PRODUCT,
            joins: ['LEFT JOIN categories c ON c.id = cp.category_id'],
            where: SchemaMysql::PIVOT_CATEGORY_PRODUCT_FK_PRODUCT,
            equal: SchemaMysql::PRODUCT_ID,
            alias: 'categories'
        );
    }

    public function findAllWithLightRefs(): array
    {
        // 🔹 Toutes les colonnes du produit ainsi que les colonnes principales des références
        $select = array_merge(
            $this->getSchemeAllColumnsProduct(),
            [
                $this->getSubJsonLightImages(),
                $this->getSubJsonLightCategories()
            ]
        );

        // 🔹 Query principal
        $result = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_PRODUCTS)
            ->where(SchemaMysql::PRODUCT_IS_ACTIVE . " = TRUE")
            ->orderBy(SchemaMysql::PRODUCT_NAME, 'ASC')
            ->limit(50)
            ->executeAndFetchAll();

        // 🔹 Transformation JSON → array
        $products = [];
        foreach ($result as $row) {
            $row = $this->decodeJsonRelations($row);
            $products[] = Product::fromArray($row);
        }

        return $products;
    }


    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        $stmt = $this->queryBuilder
            ->select()
            ->from(SchemaMysql::TABLE_PRODUCTS)
            ->executeAndFetchAll();
        return array_map(fn($row) => Product::fromArray($row), $stmt);
    }

    /**
     * @return Product[]
     */
    public function findAllForGallery(): array
    {
        $sql = "
        SELECT
            p.id,
            p.name,
            p.slug,
            p.reference,
            p.description,
            p.is_active,
            GROUP_CONCAT(
                DISTINCT CONCAT(c.id, ':', c.slug, ':', c.name)
                SEPARATOR '|'
            ) AS categories,
            (
                SELECT pi.file_path
                FROM product_images pi
                WHERE pi.product_id = p.id
                  AND pi.is_main = TRUE
                LIMIT 1
            ) AS main_image
        FROM products p
        JOIN category_product cp ON cp.product_id = p.id
        JOIN categories c        ON c.id = cp.category_id
        WHERE p.is_active = TRUE
        GROUP BY p.id
        ORDER BY p.name ASC
        LIMIT 50
    ";

        $result = $this->queryBuilder
            ->raw($sql)
            ->executeAndFetchAll();

        $products = [];
        foreach ($result as $row) {
            $row['categories'] = $this->aggTransform->groupConcatToArray(
                $row['categories'],
                ['id', 'slug', 'name']
            );
            $row['images'] = $this->aggTransform->subqueryToArray(
                $row['main_image'] ?? null,
                ['file_path']
            );
            $products[] = Product::fromArray($row);
        }

        return $products;
    }

    public function findAttributesByProductId(int $productId): array
    {
        $sql = "SELECT "
            . SchemaMysql::ATTRIBUTE_GROUPS_NAME . " AS group_name, "
            . SchemaMysql::ATTRIBUTE_GROUPS_DISPLAY_ORDER . ","
            . SchemaMysql::ATTRIBUTES_NAME . " AS attribute_name,"
            . SchemaMysql::PRODUCT_ATTRIBUTE_VALUE . " FROM " . SchemaMysql::TABLE_PRODUCT_ATTRIBUTE .
            " JOIN " . SchemaMysql::TABLE_ATTRIBUTES . " ON " . SchemaMysql::ATTRIBUTES_ID . " = " . SchemaMysql::PRODUCT_ATTRIBUTE_ATTRIBUTE_ID .
            " JOIN " . SchemaMysql::TABLE_ATTRIBUTE_GROUPS . " ON " . SchemaMysql::ATTRIBUTE_GROUPS_ID . " = " . SchemaMysql::ATTRIBUTES_ATTRIBUTE_GROUP_ID .
            " WHERE " . SchemaMysql::PRODUCT_ATTRIBUTE_PRODUCT_ID . " = :product_id ORDER BY " . SchemaMysql::ATTRIBUTE_GROUPS_DISPLAY_ORDER . " ASC";

        $result = $this->queryBuilder->raw($sql, [':product_id' => $productId])->executeAndFetchAll();

        return array_map([ProductAttribute::class, 'fromArray'], $result);
    }

    public function findBySlugWithLightRefs(string $slug): ?Product
    {
        // 🔹 Toutes les colonnes du produit ainsi que les colonnes principales des références
        $select = array_merge(
            $this->getSchemeAllColumnsProduct(),
            [$this->getSubJsonLightImages(), $this->getSubJsonLightCategories()]
        );

        // Query principal
        $result = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_PRODUCTS)
            ->where(SchemaMysql::PRODUCT_SLUG . ' = :slug', [':slug' => $slug])
            ->executeAndFetchOne();

        return $result === null ? null : Product::fromArray($this->decodeJsonRelations($result));
    }

    private function decodeJsonRelations(array $row): array
    {
        $row['categories'] = json_decode($row['categories'], true);
        $row['images'] = json_decode($row['images'], true);
        return $row;
    }
}
