<?php

namespace App\DAO;

use App\Core\BaseDAO;
use App\Core\QueryBuilderInterface;
use App\Core\Logger\AccessLogger;

class ProductDAO extends BaseDAO
{
    public function __construct(QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    public function getAllProducts()
    {
        try {
            $query = $this->queryBuilder
                ->select('product')
                ->columns(['product.*', 'category.name AS category_name', 'img.link AS img_link'])
                ->innerJoin('category', 'product.FK_category = category.category_id')
                ->leftJoin('img', 'product.product_id = img.FK_product')
                ->getQuery();
            $stmt = $this->queryBuilder->execute($query);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function getProductById($id)
    {
        try {
            $query = $this->queryBuilder
                ->select('product')
                ->columns(['product.*', 'category.name AS category_name', 'img.link AS img_link'])
                ->innerJoin('category', 'product.FK_category = category.category_id')
                ->leftJoin('img', 'product.product_id = img.FK_product')
                ->where('product.product_id = :id', ['id' => $id])
                ->getQuery();

            $stmt = $this->queryBuilder->execute($query);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error : " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function getAllProductsByFilter($filter, $equal)
    {
        try {
            $query = $this->queryBuilder
                ->select('product')
                ->columns(['product.*', 'category.name AS category_name', 'img.link AS img_link'])
                ->innerJoin('category', 'product.FK_category = category.category_id')
                ->leftJoin('img', 'product.product_id = img.FK_product')
                ->where('product.' . $filter . ' = :equal', ['equal' => $equal])
                ->getQuery();

            $stmt = $this->queryBuilder->execute($query);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            AccessLogger::log("DAO Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function getImagesByProductId($productId)
    {
        try {
            $query = $this->queryBuilder
                ->select('img')
                ->columns(['img.*'])
                ->where('img.FK_product = :productId', ['productId' => $productId])
                ->getQuery();

            $stmt = $this->queryBuilder->execute($query);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error : " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function createProduct($name, $isProcural, $isActive, $FK_category)
    {
        try {
            $query = $this->queryBuilder
                ->insert('product')
                ->values([
                    'name' => $name,
                    'isProcural' => $isProcural,
                    'isActive' => $isActive,
                    'FK_category' => $FK_category
                ])
                ->getQuery();

            return $this->queryBuilder->execute($query, [
                'name' => $name,
                'isProcural' => $isProcural,
                'isActive' => $isActive,
                'FK_category' => $FK_category
            ]);
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error : " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function updateProduct($id, $name, $isProcural, $isActive, $FK_category)
    {
        try {
            $query = $this->queryBuilder
                ->update('product')
                ->set([
                    'name' => $name,
                    'isProcural' => $isProcural,
                    'isActive' => $isActive,
                    'FK_category' => $FK_category
                ])
                ->where('product_id = :id', ['id' => $id])
                ->getQuery();

            return $this->queryBuilder->execute($query, [
                'id' => $id,
                'name' => $name,
                'isProcural' => $isProcural,
                'isActive' => $isActive,
                'FK_category' => $FK_category
            ]);
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error : " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function deleteProduct($id)
    {
        try {
            $query = $this->queryBuilder
                ->delete('product')
                ->where('product_id = :id', ['id' => $id])
                ->getQuery();

            return $this->queryBuilder->execute($query);
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error : " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }

    public function getCountOfProductsInWithCategory($category)
    {
        try {
            $query = $this->queryBuilder
                ->select('product')
                ->columns(['COUNT(*) as product_count'])
                ->innerJoin('category', 'product.FK_category = category.category_id')
                ->where('category.name = :category', ['category' => $category])
                ->getQuery();

            $stmt = $this->queryBuilder->execute($query, ['category' => $category]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['product_count'];
        } catch (\PDOException $e) {
            AccessLogger::log("DAO Error : " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw $e;
        }
    }
}
