<?php

namespace App\Modules\Product\Domain\Service;

use Core\Logger\AccessLogger;
use App\Exception\ServiceException;
use App\Modules\Product\Domain\Entity\Product;
use App\Modules\Product\Domain\Repository\ProductRepositoryInterface;

class ProductService
{
    public function __construct(private ProductRepositoryInterface $productRepo) {}

    public function getProductDatasForDetail(string $reference): ?Product
    {
        try {
            $product = $this->productRepo->findBySlugWithLightRefs($reference);

            if (!$product) {
                AccessLogger::log("getProductByReference($reference) a renvoyé un null", AccessLogger::LEVEL_ERROR);
                return null;
            }
            return $product;
        } catch (\Throwable $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur DAO getProductByReference($reference): " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur est survenue dans la récupération du produit par reference (Code : $errorId).");
        }
    }


    /**
     * @return Product[]
     */
    public function getProductsAllDatas(): array
    {
        try {
            return $this->productRepo->findAllWithLightRefs();
        } catch (\PDOException $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur DAO (getListProducts): " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur est survenue dans la récupération de la liste de produits (Code : $errorId).");
        }
    }

    /**
     * @return Product[]
     */
    public function getProductsDatasForGallery(): array
    {
        try {
            return $this->productRepo->findAllForGallery();
        } catch (\PDOException $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] ❌ Erreur DAO (getListProducts): " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Une erreur est survenue dans la récupération de la liste de produits (Code : $errorId).");
        }
    }
}
