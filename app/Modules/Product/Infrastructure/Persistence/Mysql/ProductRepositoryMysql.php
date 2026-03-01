<?php

namespace App\Modules\Product\Infrastructure\Persistence\Mysql;

use App\Modules\Product\Domain\Entity\Product;
use App\Modules\Product\Domain\Repository\ProductRepositoryInterface;
use Core\Database\AggregateTransformer;
use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Core\Database\SqlAggregator;
use Core\Database\SqlHelpers;
use App\Database\SchemaMysql;

class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
    private SqlAggregator $sqlAggregator;
    private AggregateTransformer $aggTransform;

    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
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
            Schemamysql::PRODUCT_CREATED_AT,
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
            $row['categories'] = json_decode($row['categories'], true);
            $row['images'] = json_decode($row['images'], true);
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
        $this->sqlAggregator = new SqlAggregator();
        $this->aggTransform = new AggregateTransformer();

        $selectGroupConcatCategory = $this->sqlAggregator
            ->column([
                SchemaMysql::CATEGORY_ID,
                SchemaMysql::CATEGORY_SLUG,
                SchemaMysql::CATEGORY_NAME
            ])
            ->separator('|')
            ->distinct()
            ->groupConcat()
            ->alias('categories')
            ->toSql();
        // Result: GROUP_CONCAT(DISTINCT pc.id, ':',pc.slug, ':',pc.name SEPARATOR "|") AS categories

        // 2️⃣ Construire la sous-requête indépendamment (clone) du QueryBuilder principal
        $selectSubqueryMainImage =  (clone $this->queryBuilder)
            ->select([SchemaMysql::PRODUCT_IMAGE_FILE_PATH])
            ->from(SchemaMysql::TABLE_PRODUCT_IMAGES)
            ->where(
                SchemaMysql::PRODUCT_IMAGE_PRODUCT_ID . ' = ' . SchemaMysql::PRODUCT_ID .
                    ' AND ' . SchemaMysql::PRODUCT_IMAGE_IS_MAIN . ' = TRUE'
            )
            ->limit(1)
            ->toSubSQL('main_image');
        // (SELECT pi.file_path FROM product_images pi 
        // WHERE `pi`.`product_id` = `p`.`id` AND `pi`.`is_main` = TRUE
        // LIMIT 1) AS main_image

        // 3️⃣ Construire le QueryBuilder principal et l'executer
        $result = $this->queryBuilder
            ->select([
                SchemaMysql::PRODUCT_ID,
                SchemaMysql::PRODUCT_NAME,
                SchemaMysql::PRODUCT_SLUG,
                SchemaMysql::PRODUCT_REFERENCE,
                SchemaMysql::PRODUCT_DESCRIPTION,
                $selectGroupConcatCategory,
                $selectSubqueryMainImage
            ])
            ->from(SchemaMysql::TABLE_PRODUCTS)
            ->joinManyToMany(
                SchemaMysql::TABLE_PIVOT_CATEGORY_PRODUCT,
                SchemaMysql::PRODUCT_ID,
                SchemaMysql::PIVOT_CATEGORY_PRODUCT_FK_PRODUCT,
                SchemaMysql::TABLE_CATEGORIES,
                SchemaMysql::PIVOT_CATEGORY_PRODUCT_FK_CATEGORY,
                SchemaMysql::CATEGORY_ID
            )
            ->where(SchemaMysql::PRODUCT_IS_ACTIVE . " = true")
            ->groupBy(SchemaMysql::PRODUCT_ID)
            ->orderBy(SchemaMysql::PRODUCT_NAME, 'ASC')
            ->limit(50)
            ->executeAndFetchAll();

        $products = [];
        foreach ($result as $row) {
            $row['categories'] = $this->aggTransform->groupConcatToArray($row['categories'], [SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_ID), SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_SLUG), SchemaMysql::fieldProperty(SchemaMysql::CATEGORY_NAME)]);
            $row['images'] = $this->aggTransform->subqueryToArray($row['main_image'] ?? null, [SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IMAGE_FILE_PATH), SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IMAGE_ALT_TEXT)]);
            $products[] = Product::fromArray($row);
        }

        return $products;
    }

    public function findBySlugWithLightRefs(string $slug): ?Product
    {
        // 🔹 Toutes les colonnes du produit ainsi que les colonnes principales des références
        $select = array_merge(
            $this->getSchemeAllColumnsProduct(),
            [$this->getSubJsonLightImages(), $this->getSubJsonLightCategories()]
        );

        // 🔹 Query principal
        $result = $this->queryBuilder
            ->select($select)
            ->from(SchemaMysql::TABLE_PRODUCTS)
            ->where(SchemaMysql::PRODUCT_SLUG . ' = :slug', [':slug' => $slug])
            ->executeAndFetchOne();

        // 🔹 Transformation JSON → array
        $result['categories'] = json_decode($result['categories'], true);
        $result['images'] = json_decode($result['images'], true);

        return Product::fromArray($result);
    }
}
