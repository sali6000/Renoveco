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
use Core\Support\DebugHelper;
use Src\Database\SchemaMysql;

class ProductRepositoryMysql extends RepositoryMySQL implements ProductRepositoryInterface
{
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

    public function __construct(
        \PDO $pdo,
        private QueryBuilderInterface $queryBuilder
    ) {
        parent::__construct($pdo);
    }

    //----------------------------------------------------------------------------
    // QUERIES SELECT :
    //----------------------------------------------------------------------------

    // SELECT : FIND ONE 
    public function findOne(ProductQuery $q): ?Product
    {
        return $this->executeFindOne($q, self::PRODUCT_COLUMNS);
    }

    // SELECT : FIND ALL
    public function findAll(ProductQuery $q): array
    {
        return $this->executeMany($q, self::PRODUCT_COLUMNS);
    }


    //----------------------------------------------------------------------------
    // QUERIES EXECUTE :
    //----------------------------------------------------------------------------

    // EXECUTE : FIND ONE
    private function executeFindOne(ProductQuery $q, array $columns): ?Product
    {
        // SELECT COLUMNS = $columns + relations params (ex: $q->withRoles, etc...)
        // FROM USER
        // WHERE (ex: $q->param !=== null)
        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, $columns, $relations);

        // IF RELATIONS (GROUPED <- rows)
        if (!empty($relations)) {

            // EXECUTION (return rows[])
            $rows = $query->executeAndFetchAll();

            // HYDRATATION (Entities['roles'] <- rows['role1'], rows['role2']) (> 1 result)
            $entities = $this->hydrateMany($rows, $relations);

            // GET ENTITY FROM ARRAY (Entity['roles'] <- Entities['roles']) (1 result)
            $entity = $entities[0] ?? null;

            // RETURN ENTITY
            return $entity;
        }

        // EXECUTION (return row)
        $row = $query->executeAndFetchOne();

        // HYDRATATION (Entity <- row)
        $entity = $row ? Product::fromArray($row) : null;

        // RETURN ENTITY
        return $entity;
    }

    // EXECUTE : FIND ALL
    public function executeMany(ProductQuery $q, array $columns): array
    {
        // SELECT COLUMNS = $columns + $relations (ex: $q->withRoles, etc...)
        // FROM TABLE
        // WHERE (ex: $q->param !=== null)
        $relations = $this->resolveRelations($q);
        $query = $this->buildQuery($q, $columns, $relations);

        // ADD <= LIMIT, OFFSET, ...
        if ($q->limit !== null) $query = $query->limit($q->limit);
        if ($q->offset !== null) $query = $query->offset($q->offset);

        // EXECUTION (return rows[])
        $rows = $query->executeAndFetchAll();

        // HYDRATATION (Entities['roles'] <- rows['role1'], rows['role2']) (> 1 result)
        $entities = $this->hydrateMany($rows, $relations);

        // RETURN ENTITIES
        return $entities;
    }


    //----------------------------------------------------------------------------
    // QUERY MAKER :
    //----------------------------------------------------------------------------

    // PREPARE QUERY
    private function buildQuery(ProductQuery $q, array $columns = self::PRODUCT_COLUMNS, array $relations = []): QueryBuilderInterface
    {
        // GET COLUMNS (from $columns and $relations)
        foreach ($relations as $relation) {
            $columns = array_merge($columns, $relation->getColumns());
        }

        // PREPARE QUERY <= SELECT ... FROM ...
        $query = $this->queryBuilder
            ->select($columns)
            ->from(SchemaMysql::TABLE_PRODUCTS);

        // ADD TO QUERY <= JOINS ... (for each $relations)
        foreach ($relations as $relation) {
            $query = $relation->applyJoin($query);
        }

        // ADD TO QUERY <= WHERE, LIMIT, OFFSET,... (filter)
        return $this->applyFilters($query, $q);
    }

    // PREPARE FILTERS (conditions)
    private function applyFilters(QueryBuilderInterface $query, ProductQuery $queryParams): QueryBuilderInterface
    {
        // SLUG
        if ($queryParams->slug !== null) {
            $query = $query->where(SchemaMysql::PRODUCT_SLUG . ' = :slug', [':slug' => $queryParams->slug]);
        }

        // ID
        if ($queryParams->id !== null) {
            $query = $query->where(SchemaMysql::PRODUCT_ID . ' = :id', [':id' => $queryParams->id]);
        }

        // IS ACTIVE
        if ($queryParams->isActive !== null) {
            $active = $queryParams->isActive ? 'TRUE' : 'FALSE';
            $query = $query->where(SchemaMysql::PRODUCT_IS_ACTIVE . " = {$active}");
        }

        return $query;
    }

    //----------------------------------------------------------------------------
    // RELATIONS MAKER :
    //----------------------------------------------------------------------------

    // CONDITIONS JOINS (Depend of UserQuery params)
    private function resolveRelations(ProductQuery $q): array
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
     * 
     * */
    private function makeCategoriesRelation(array $columns = self::CATEGORY_COLUMNS): ManyToManyRelation
    {
        return SchemaMysql::productCategoriesRelation($columns);
    }

    /** 
     * Get columns from images
     * 
     * @return OneToManyRelation
     * 
     * */
    private function makeImagesRelation(array $columns = self::IMAGE_COLUMNS): OneToManyRelation
    {
        return SchemaMysql::productImagesRelation($columns);
    }


    /**
     * Transforme plusieurs lignes SQL en users uniques.
     *
     * Exemple :
     *
     * Entrée — 1 user avec 2 rôles = 2 lignes SQL :
     * 
     * <code>
     * ['id' => 1, 'email' => 'a@b.com', 'role_id' => 1, 'role_name' => 'admin']
     * 
     * ['id' => 1, 'email' => 'a@b.com', 'role_id' => 2, 'role_name' => 'editor']
     * </code>
     *
     * Sortie — 1 user avec ses rôles groupés :
     * 
     * <code>
     * ['id' => 1, 'email' => 'a@b.com', 'roles' => [['id' => 1, ...], ['id' => 2, ...]]]
     * </code>
     *
     * @return Product[]
     */
    private function hydrateMany(array $rows, array $relations): array
    {
        // RETURN (without relations)
        if (empty($relations)) {
            return array_map(fn(array $row) => Product::fromArray($row), $rows);
        }

        // HYDRATE (with relations)
        foreach ($relations as $relation) {
            $rows = $relation->hydrate($rows);
        }

        return array_map(fn(array $row) => Product::fromArray($row), $rows);
    }
}
