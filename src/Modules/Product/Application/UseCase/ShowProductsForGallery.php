<?php

namespace Src\Modules\Product\Application\UseCase;

use Core\Logger\AccessLogger;
use Src\Exception\ServiceException;
use Src\Modules\Product\Domain\Repository\ProductRepositoryInterface;

final class ShowProductsForGallery
{
    public function __construct(private readonly ProductRepositoryInterface $productRepo) {}

    public function execute(): array
    {
        try {
            return $this->productRepo->findAllForGallery();
        } catch (\Throwable $e) {
            $errorId = uniqid('err_', true);
            AccessLogger::log("[$errorId] Erreur récupération galerie : " . $e, AccessLogger::LEVEL_ERROR);
            throw new ServiceException("Erreur récupération produits (Code : $errorId).");
        }
    }
}
