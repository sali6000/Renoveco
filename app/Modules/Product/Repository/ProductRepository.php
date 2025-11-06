<?php

namespace App\Modules\Product\Repository;

use Core\Database\AggregateTransformer;
use Core\Database\QueryBuilderInterface;
use Core\Database\SqlAggregator;
use Core\Database\Repository;
use App\Database\Schema;
use App\Modules\Product\Entity\Product;

class ProductRepository extends Repository
{
    private SqlAggregator $sqlAggregator;
    private AggregateTransformer $aggTransform;

    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($pdo);
    }

    public function getListProducts(): array
    {
        $this->sqlAggregator = new SqlAggregator();
        $this->aggTransform = new AggregateTransformer();

        $selectGroupConcatCategory = $this->sqlAggregator
            ->column([
                SCHEMA::CATEGORY_ID,
                SCHEMA::CATEGORY_SLUG,
                SCHEMA::CATEGORY_NAME
            ])
            ->separator('|')
            ->distinct()
            ->groupConcat()
            ->alias('categories')
            ->toSql();
        // Result: GROUP_CONCAT(DISTINCT pc.id, ':',pc.slug, ':',pc.name SEPARATOR "|") AS categories

        // 2️⃣ Construire la sous-requête indépendamment (clone) du QueryBuilder principal
        $selectSubqueryMainImage =  (clone $this->queryBuilder)
            ->selectFrom(Schema::TABLE_PRODUCT_IMAGES, [
                Schema::PRODUCT_IMAGE_FILE_PATH
            ])
            ->where(
                Schema::PRODUCT_IMAGE_PRODUCT_ID . ' = ' . Schema::PRODUCT_ID .
                    ' AND ' . Schema::PRODUCT_IMAGE_IS_MAIN . ' = TRUE'
            )
            ->limit(1)
            ->toSubSQL('main_image');
        // (SELECT pi.file_path FROM product_images pi 
        // WHERE `pi`.`product_id` = `p`.`id` AND `pi`.`is_main` = TRUE
        // LIMIT 1) AS main_image

        // 3️⃣ Construire le QueryBuilder principal et l'executer
        $stmt = $this->queryBuilder
            ->selectFrom(Schema::TABLE_PRODUCTS, [
                Schema::PRODUCT_ID,
                Schema::PRODUCT_NAME,
                Schema::PRODUCT_SLUG,
                Schema::PRODUCT_REFERENCE,
                Schema::PRODUCT_DESCRIPTION,
                $selectGroupConcatCategory,
                $selectSubqueryMainImage
            ])
            ->selectJoinManyToMany(
                Schema::TABLE_PIVOT_CATEGORY_PRODUCT,
                Schema::TABLE_CATEGORIES
            )
            ->where(Schema::PRODUCT_IS_ACTIVE . " = true")
            ->groupBy(Schema::PRODUCT_ID)
            ->orderBy(Schema::PRODUCT_NAME, 'ASC')
            ->limit(50)
            ->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $products = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->aggTransform->groupConcatToArray($row['categories'], ['id', 'slug', 'name']);
            $row['images'] = $this->aggTransform->subqueryToArray($row['main_image'] ?? null, ['file_path', 'alt_text']);
            $products[] = Product::fromArray($row);
        }

        return $products;
    }

    public function getProductBySlug(string $slug): Product
    {
        $stmt = $this->queryBuilder
            ->selectFrom(SCHEMA::TABLE_PRODUCTS)
            ->selectJoinLeft(SCHEMA::TABLE_PRODUCT_IMAGES, [
                SCHEMA::PRODUCT_IMAGE_FILE_PATH,
                SCHEMA::PRODUCT_IMAGE_ALT_TEXT
            ])
            ->where(SCHEMA::PRODUCT_SLUG . ' = :slug AND ' . SCHEMA::PRODUCT_IMAGE_IS_MAIN . ' = TRUE', [':slug' => $slug])
            ->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return Product::fromArray($row);
    }
}
