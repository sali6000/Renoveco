<?php

namespace App\Core;

use App\Config\Database;
use App\Controllers\ProductController;
use App\Controllers\HomeController;
use App\Controllers\Templates\HeaderController;
use App\Controllers\HistoryController;
use App\Controllers\ProcuralController;
use App\Dao\HistoryDAO;
use App\Dao\ProductDAO;
use App\Services\HistoryService;
use App\Services\ProductService;
use Exception;


class ControllerFactory
{

    public static function create($controllerClass)
    {
        if (!class_exists($controllerClass)) {
            throw new Exception("Le chemin vers le controleur : $controllerClass n'a pas été trouvé.");
        }

        $pdo = Database::getInstance();
        $queryBuilder = new QueryBuilder($pdo);

        switch ($controllerClass) {
            case ProductController::class:
                $productDAO = new ProductDAO($queryBuilder);
                $productService = new ProductService($productDAO);
                return new ProductController($productService);
            case HomeController::class:
                $productDAO = new ProductDAO($queryBuilder);
                $productService = new ProductService($productDAO);
                return new HomeController($productService);
            case HeaderController::class:
                $productDAO = new ProductDAO($queryBuilder);
                $productService = new ProductService($productDAO);
                return new HeaderController($productService);
            case ProcuralController::class:
                $productDAO = new ProductDAO($queryBuilder);
                $productService = new ProductService($productDAO);
                return new ProcuralController($productService);
            case HistoryController::class:
                $historyDAO = new HistoryDAO(BASE_PATH_STORAGE_DATAS . 'history.json');
                $historyService = new HistoryService($historyDAO);
                return new HistoryController($historyService);
            default:
                return new $controllerClass(); // Pour les contrôleurs sans dépendances
        }
    }
}
