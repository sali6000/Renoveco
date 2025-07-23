<?php

namespace App\Services;

use App\Dao\ProductDAO;
use App\Core\Logger\AccessLogger;

class ProductService
{
    private $productDAO;

    public function __construct(ProductDAO $productDAO)
    {
        $this->productDAO = $productDAO;
    }

    public function getProductById($id)
    {
        try {
            return $this->productDAO->getProductById($id);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    public function getAllProducts()
    {
        try {
            return $this->productDAO->getAllProducts();
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    /**
     * Get all images associated with a product by product ID except the main image.
     *
     * @param int $productId
     * @return array
     */
    public function getImagesByProductIdExceptItself($productId)
    {
        try {
            // Get all images associated with the product
            return $this->productDAO->getImagesByProductId($productId);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    public function countProductsInCategory($categoryName)
    {
        try {
            return $this->productDAO->getCountOfProductsInWithCategory($categoryName);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    public function createProduct($name, $isProcural, $isActive, $FK_category)
    {
        try {
            return $this->productDAO->createProduct($name, $isProcural, $isActive, $FK_category);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    public function updateProduct($id, $name, $isProcural, $isActive, $FK_category)
    {
        try {
            return $this->productDAO->updateProduct($id, $name, $isProcural, $isActive, $FK_category);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    public function deleteProduct($id)
    {
        try {
            return $this->productDAO->deleteProduct($id);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }

    public function getAllProductsByFilter($filter, $equal)
    {
        try {
            return $this->productDAO->getAllProductsByFilter($filter, $equal);
        } catch (\Exception $e) {
            AccessLogger::log("Service Error (getAllProducts): " . $e->getMessage(), 3, BASE_PATH_STORAGE_LOGS);
            throw new \Exception("Failed to fetch all products. Please try again later.");
        }
    }
}
