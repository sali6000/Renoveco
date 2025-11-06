<?php

namespace App\Modules\Product\Service;

use Core\Logger\AccessLogger;
use App\Exception\ServiceException;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;


class ProductService
{
    public function __construct(private ProductRepository $productRepo) {}

    public function getProductBySlug($reference): ?Product
    {
        try {
            $product = $this->productRepo->getProductBySlug($reference);
            if (!$product) {
                AccessLogger::log("getProductByReference($reference) a renvoyé un null", AccessLogger::LEVEL_ERROR);
                return null;
            }
            return $product;
        } catch (\Exception $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur DAO getProductByReference($reference): " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur est survenue dans la récupération du produit par reference (Code : $errorId).");
        }
    }

    public function getListProducts(): array
    {
        try {
            return $this->productRepo->getListProducts();
        } catch (\PDOException $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur DAO (getListProducts): " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur est survenue dans la récupération de la liste de produits (Code : $errorId).");
        }
    }
}
